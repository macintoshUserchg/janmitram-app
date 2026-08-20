<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StockAssignmentRequest;
use App\Models\Product;
use App\Models\Shop;
use App\Models\StockRequest;
use App\Models\Warehouse;
use App\Services\WarehouseService;
use Illuminate\Support\Facades\DB;

class StockAssignmentController extends Controller
{
    use SortableIndex;

    /**
     * List past shop-inventory assignments (marked in notes by this controller).
     */
    public function index()
    {
        [$sort, $direction] = $this->resolveSort();

        $assignments = $this->applySort(
            StockRequest::where('notes', 'like', '%[inventory-assignment]%')->with(['shop', 'warehouse', 'items.product']),
            $sort,
            $direction
        )->paginate($this->resolvePerPage())->withQueryString();

        return view('admin.inventory-assignment.index', compact('assignments', 'sort', 'direction'));
    }

    public function create()
    {
        $warehouses = Warehouse::all();
        $shops = Shop::with(['user', 'stockRequests' => fn ($q) => $q->where('status', 'completed')])->get()
            ->map(function ($shop) {
                $shop->is_first_transfer = $shop->stockRequests->isEmpty();

                return $shop;
            });
        $products = Product::where('is_digital', false)
            ->where('is_active', true)
            ->with('warehouseStocks')
            ->orderBy('name')
            ->get()
            ->map(function ($product) {
                $product->stock_map = $product->warehouseStocks
                    ->groupBy('warehouse_id')
                    ->map(fn ($group) => (int) $group->sum('quantity'))
                    ->toArray();

                return $product;
            });

        return view('admin.inventory-assignment.create', compact('warehouses', 'shops', 'products'));
    }

    public function store(StockAssignmentRequest $request)
    {
        $warehouse = Warehouse::findOrFail($request->from_warehouse_id);
        $shop = Shop::findOrFail($request->shop_id);

        $items = collect($request->items)->filter(fn ($item) => isset($item['product_id']) && (int) $item['quantity'] > 0);

        if ($items->isEmpty()) {
            return back()->withErrors(['items' => __('Select at least one product with a quantity.')])->withInput();
        }

        // Strict stock check before any write — avoids the silent-clamp behaviour of fulfillStockRequest.
        foreach ($items as $item) {
            $product = Product::find($item['product_id']);
            if (! $product || ! WarehouseService::hasSufficientStock($warehouse, $product, (int) $item['quantity'])) {
                return back()->withErrors([
                    "items.{$item['product_id']}" => __('Insufficient stock in :warehouse for :product.', [
                        'warehouse' => $warehouse->name,
                        'product' => $product?->name ?? $item['product_id'],
                    ]),
                ])->withInput();
            }
        }

        $result = DB::transaction(function () use ($warehouse, $shop, $items, $request) {
            $stockRequest = StockRequest::create([
                'shop_id' => $shop->id,
                'warehouse_id' => $warehouse->id,
                'status' => 'pending',
                'notes' => $request->notes ? $request->notes.'<br>[inventory-assignment]' : '[inventory-assignment]',
            ]);

            foreach ($items as $item) {
                $stockRequest->items()->create([
                    'product_id' => $item['product_id'],
                    'color_id' => $item['color_id'] ?? null,
                    'size_id' => $item['size_id'] ?? null,
                    'quantity' => (int) $item['quantity'],
                ]);
            }

            WarehouseService::fulfillStockRequest($stockRequest);

            return $stockRequest;
        });

        return redirect()->route('admin.inventory-assignment.index')->with('success', __('Inventory assigned to :shop.', ['shop' => $shop->name]).' #'.$result->id);
    }
}
