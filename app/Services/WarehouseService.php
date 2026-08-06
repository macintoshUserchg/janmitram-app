<?php

declare(strict_types=1);

namespace App\Services;

use App\Exceptions\InsufficientStockException;
use App\Models\Product;
use App\Models\Shop;
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
     * Deep clone a master product to a shop-local copy.
     */
    public static function cloneMasterToShop(Product $master, Shop $shop): Product
    {
        return DB::transaction(function () use ($master, $shop) {
            $existing = Product::where('master_product_id', $master->id)
                ->where('shop_id', $shop->id)
                ->first();

            if ($existing) {
                return $existing;
            }

            $shopProduct = $master->replicate(['shop_id', 'master_product_id', 'quantity']);
            $shopProduct->shop_id = $shop->id;
            $shopProduct->master_product_id = $master->id;
            $shopProduct->quantity = 0;
            $shopProduct->save();

            // Sync pivot relationships
            $shopProduct->categories()->sync($master->categories->pluck('id'));
            $shopProduct->subcategories()->sync($master->subcategories->pluck('id'));

            foreach ($master->colors as $color) {
                $shopProduct->colors()->attach($color->id, ['price' => $color->pivot->price ?? 0]);
            }

            foreach ($master->sizes as $size) {
                $shopProduct->sizes()->attach($size->id, ['price' => $size->pivot->price ?? 0]);
            }

            $shopProduct->medias()->sync($master->medias->pluck('id'));

            foreach ($master->translations as $translation) {
                $shopProduct->translations()->create([
                    'lang' => $translation->lang,
                    'name' => $translation->name,
                    'description' => $translation->description,
                    'short_description' => $translation->short_description,
                ]);
            }

            return $shopProduct;
        });
    }

    /**
     * Fulfill a stock request: deduct from warehouse stock and increment shop product quantity.
     */
    public static function fulfillStockRequest(StockRequest $request): void
    {
        DB::transaction(function () use ($request) {
            $request->load('items.product');

            foreach ($request->items as $item) {
                $stock = self::findStock(
                    $request->warehouse,
                    $item->product,
                    $item->quantity,
                    $item->color_id,
                    $item->size_id
                );

                // Deduct warehouse stock if available
                $deductQty = 0;
                if ($stock) {
                    $deductQty = min($item->quantity, max(0, $stock->quantity));
                    if ($deductQty > 0) {
                        $stock->decrement('quantity', $deductQty);
                    }
                }
                // No else branch creating dead zero-qty rows

                // Sync master product table quantity (stock leaves central hub)
                if ($item->product && $item->product->quantity > 0) {
                    $item->product->decrement('quantity', min($item->quantity, $item->product->quantity));
                }

                // Create or find shop copy of product and add quantity to shop
                $shopProduct = self::cloneMasterToShop($item->product, $request->shop);
                $shopProduct->increment('quantity', $deductQty);

                // Record ledger entry
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
        // Delegate to findStock so the pre-check agrees with what fulfillStockRequest
        // actually picks: exact variant first, then any sufficient row.
        $stock = self::findStock($warehouse, $product, $qty, $colorId, $sizeId);

        return $stock !== null && $stock->quantity >= $qty;
    }
}
