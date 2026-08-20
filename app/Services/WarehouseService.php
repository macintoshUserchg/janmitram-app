<?php

declare(strict_types=1);

namespace App\Services;

use App\Exceptions\InsufficientStockException;
use App\Models\Product;
use App\Models\Shop;
use App\Models\ShopInventory;
use App\Models\StockLedger;
use App\Models\StockRequest;
use App\Models\Warehouse;
use App\Models\WarehouseStock;
use Illuminate\Support\Facades\DB;

class WarehouseService
{
    /**
     * Find a WarehouseStock record using smart fallback:
     * 1. Exact color/size match (if specified)
     * 2. Any stock with sufficient quantity
     * 3. Any stock record at all (for error reporting)
     */
    private static function findStock(
        Warehouse $warehouse,
        Product $product,
        int $qty,
        ?int $colorId = null,
        ?int $sizeId = null
    ): ?WarehouseStock {
        // Step 1: exact color/size match
        if ($colorId !== null || $sizeId !== null) {
            $query = WarehouseStock::where('warehouse_id', $warehouse->id)
                ->where('product_id', $product->id);

            if ($colorId !== null) {
                $query->where('color_id', $colorId);
            } else {
                $query->whereNull('color_id');
            }

            if ($sizeId !== null) {
                $query->where('size_id', $sizeId);
            } else {
                $query->whereNull('size_id');
            }

            $stock = $query->lockForUpdate()->first();
            if ($stock) {
                return $stock;
            }
        }

        // Step 2: any stock with sufficient quantity
        $stock = WarehouseStock::where('warehouse_id', $warehouse->id)
            ->where('product_id', $product->id)
            ->where('quantity', '>=', $qty)
            ->orderBy('quantity', 'desc')
            ->lockForUpdate()
            ->first();

        if ($stock) {
            return $stock;
        }

        // Step 3: any stock record at all
        return WarehouseStock::where('warehouse_id', $warehouse->id)
            ->where('product_id', $product->id)
            ->orderBy('quantity', 'desc')
            ->lockForUpdate()
            ->first();
    }

    /**
     * Create a new warehouse.
     */
    public static function createWarehouse(array $data): Warehouse
    {
        return Warehouse::create($data);
    }

    /**
     * Add stock to a warehouse (with lockForUpdate and ledger entry).
     */
    public static function addStock(
        Warehouse $warehouse,
        Product $product,
        int $qty,
        ?int $colorId = null,
        ?int $sizeId = null,
        string $referenceType = 'admin_addition',
        ?int $referenceId = null,
        ?string $notes = null
    ): void {
        DB::transaction(function () use ($warehouse, $product, $qty, $colorId, $sizeId, $referenceType, $referenceId, $notes) {
            $query = WarehouseStock::where('warehouse_id', $warehouse->id)
                ->where('product_id', $product->id);

            if ($colorId) {
                $query->where('color_id', $colorId);
            } else {
                $query->whereNull('color_id');
            }

            if ($sizeId) {
                $query->where('size_id', $sizeId);
            } else {
                $query->whereNull('size_id');
            }

            $stock = $query->lockForUpdate()->first();

            if ($stock) {
                $stock->increment('quantity', $qty);
            } else {
                WarehouseStock::create([
                    'warehouse_id' => $warehouse->id,
                    'product_id' => $product->id,
                    'color_id' => $colorId,
                    'size_id' => $sizeId,
                    'quantity' => $qty,
                ]);
            }

            // Sync product table quantity for physical products
            if (! $product->is_digital) {
                $product->increment('quantity', $qty);
            }

            StockLedger::create([
                'from_warehouse_id' => null,
                'to_warehouse_id' => $warehouse->id,
                'product_id' => $product->id,
                'color_id' => $colorId,
                'size_id' => $sizeId,
                'quantity' => $qty,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
                'notes' => $notes,
            ]);
        });
    }

    /**
     * Deduct stock from a warehouse (with lockForUpdate and ledger entry).
     */
    public static function deductStock(
        Warehouse $warehouse,
        Product $product,
        int $qty,
        ?int $colorId = null,
        ?int $sizeId = null,
        string $referenceType = 'loss',
        ?int $referenceId = null,
        ?string $notes = null
    ): void {
        DB::transaction(function () use ($warehouse, $product, $qty, $colorId, $sizeId, $referenceType, $referenceId, $notes) {
            $stock = self::findStock($warehouse, $product, $qty, $colorId, $sizeId);

            if (! $stock || $stock->quantity < $qty) {
                throw new InsufficientStockException("Insufficient stock in warehouse {$warehouse->name} for product {$product->name}.");
            }

            $stock->decrement('quantity', $qty);

            // Sync product table quantity for physical products
            if (! $product->is_digital) {
                $product->decrement('quantity', min($qty, $product->quantity));
            }

            StockLedger::create([
                'from_warehouse_id' => $warehouse->id,
                'to_warehouse_id' => null,
                'product_id' => $product->id,
                'color_id' => $colorId,
                'size_id' => $sizeId,
                'quantity' => $qty,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
                'notes' => $notes,
            ]);
        });
    }

    /**
     * Transfer stock between warehouses.
     */
    public static function transfer(
        Warehouse $from,
        Warehouse $to,
        Product $product,
        int $qty,
        ?int $colorId = null,
        ?int $sizeId = null,
        ?int $transferId = null,
        ?string $notes = null
    ): void {
        DB::transaction(function () use ($from, $to, $product, $qty, $colorId, $sizeId, $transferId, $notes) {
            // Deduct from source warehouse
            $stockFrom = self::findStock($from, $product, $qty, $colorId, $sizeId);

            if (! $stockFrom || $stockFrom->quantity < $qty) {
                throw new InsufficientStockException("Insufficient stock in source warehouse {$from->name} for product {$product->name}.");
            }

            $stockFrom->decrement('quantity', $qty);

            $targetColorId = $colorId ?? $stockFrom->color_id;
            $targetSizeId = $sizeId ?? $stockFrom->size_id;

            // Add to target warehouse
            $queryTo = WarehouseStock::where('warehouse_id', $to->id)
                ->where('product_id', $product->id);

            if ($targetColorId) {
                $queryTo->where('color_id', $targetColorId);
            } else {
                $queryTo->whereNull('color_id');
            }

            if ($targetSizeId) {
                $queryTo->where('size_id', $targetSizeId);
            } else {
                $queryTo->whereNull('size_id');
            }

            $stockTo = $queryTo->lockForUpdate()->first();

            if ($stockTo) {
                $stockTo->increment('quantity', $qty);
            } else {
                WarehouseStock::create([
                    'warehouse_id' => $to->id,
                    'product_id' => $product->id,
                    'color_id' => $targetColorId,
                    'size_id' => $targetSizeId,
                    'quantity' => $qty,
                ]);
            }

            // Ledger entry
            StockLedger::create([
                'from_warehouse_id' => $from->id,
                'to_warehouse_id' => $to->id,
                'product_id' => $product->id,
                'color_id' => $targetColorId,
                'size_id' => $targetSizeId,
                'quantity' => $qty,
                'reference_type' => 'warehouse_transfer',
                'reference_id' => $transferId,
                'notes' => $notes,
            ]);
        });
    }

    /**
     * Ensure shop inventory record exists for product.
     */
    public static function cloneMasterToShop(Product $master, Shop $shop): Product
    {
        ShopInventory::firstOrCreate(
            ['shop_id' => $shop->id, 'product_id' => $master->id],
            ['quantity' => 0, 'is_active' => true]
        );

        return $master;
    }

    /**
     * Fulfill a stock request: deduct from warehouse stock and increment shop inventory quantity.
     */
    public static function fulfillStockRequest(StockRequest $request): void
    {
        DB::transaction(function () use ($request) {
            $request->load('items.product');

            foreach ($request->items as $item) {
                $requestedQty = (int) $item->quantity;
                $deductedTotal = 0;

                if ($item->color_id !== null || $item->size_id !== null) {
                    $stock = self::findStock(
                        $request->warehouse,
                        $item->product,
                        $requestedQty,
                        $item->color_id,
                        $item->size_id
                    );

                    if ($stock && $stock->quantity > 0) {
                        $deductQty = min($requestedQty, $stock->quantity);
                        $stock->decrement('quantity', $deductQty);
                        $deductedTotal += $deductQty;

                        StockLedger::create([
                            'from_warehouse_id' => $request->warehouse_id,
                            'to_warehouse_id' => null,
                            'product_id' => $item->product_id,
                            'color_id' => $item->color_id,
                            'size_id' => $item->size_id,
                            'quantity' => $deductQty,
                            'reference_type' => 'shop_request',
                            'reference_id' => $request->id,
                            'notes' => "Fulfilled stock request #{$request->id} for shop #{$request->shop_id}",
                        ]);
                    }
                } else {
                    // Multi-row deduction across available stock rows in warehouse
                    $stockRows = WarehouseStock::where('warehouse_id', $request->warehouse_id)
                        ->where('product_id', $item->product_id)
                        ->where('quantity', '>', 0)
                        ->orderByDesc('quantity')
                        ->lockForUpdate()
                        ->get();

                    $remainingToDeduct = $requestedQty;
                    foreach ($stockRows as $row) {
                        if ($remainingToDeduct <= 0) {
                            break;
                        }

                        $take = min($remainingToDeduct, $row->quantity);
                        $row->decrement('quantity', $take);
                        $remainingToDeduct -= $take;
                        $deductedTotal += $take;

                        StockLedger::create([
                            'from_warehouse_id' => $request->warehouse_id,
                            'to_warehouse_id' => null,
                            'product_id' => $item->product_id,
                            'color_id' => $row->color_id,
                            'size_id' => $row->size_id,
                            'quantity' => $take,
                            'reference_type' => 'shop_request',
                            'reference_id' => $request->id,
                            'notes' => "Fulfilled stock request #{$request->id} for shop #{$request->shop_id}",
                        ]);
                    }
                }

                // Sync master product table quantity (stock leaves central hub)
                if ($item->product && $item->product->quantity > 0 && $deductedTotal > 0) {
                    $item->product->decrement('quantity', min($deductedTotal, $item->product->quantity));
                }

                // Increment branch inventory in shop_inventories
                if ($deductedTotal > 0) {
                    $shopInv = ShopInventory::firstOrCreate(
                        [
                            'shop_id' => $request->shop_id,
                            'product_id' => $item->product_id,
                        ],
                        [
                            'quantity' => 0,
                            'is_active' => true,
                        ]
                    );
                    $shopInv->increment('quantity', $deductedTotal);
                }
            }

            $request->update(['status' => 'completed']);
        });
    }

    /**
     * Check if warehouse has sufficient stock.
     */
    public static function hasSufficientStock(
        Warehouse $warehouse,
        Product $product,
        int $qty,
        ?int $colorId = null,
        ?int $sizeId = null
    ): bool {
        if ($colorId !== null || $sizeId !== null) {
            $stock = self::findStock($warehouse, $product, $qty, $colorId, $sizeId);

            return $stock !== null && $stock->quantity >= $qty;
        }

        $totalAvailable = (int) WarehouseStock::where('warehouse_id', $warehouse->id)
            ->where('product_id', $product->id)
            ->sum('quantity');

        return $totalAvailable >= $qty;
    }
}
