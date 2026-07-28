<?php

declare(strict_types=1);

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Http\Requests\StockRequestRequest;
use App\Models\Product;
use App\Models\StockRequest;
use App\Models\StockRequestItem;
use App\Models\WarehouseStock;
use App\Repositories\WarehouseRepository;

class StockRequestController extends Controller
{
    private function getShop()
    {
        $user = auth()->user();

        return $user?->shop ?? $user?->myShop ?? generaleSetting('shop');
    }

    public function index()
    {
        $shop = $this->getShop();
        $warehouse = $shop?->warehouse ?? WarehouseRepository::getCentralWarehouse();
        $status = request('status');

        $query = StockRequest::where('shop_id', $shop?->id)
            ->with(['warehouse', 'items.product']);

        if ($status && in_array($status, ['pending', 'completed', 'rejected'])) {
            $query->where('status', $status);
        }

        $requests = $query->latest()->paginate(15)->withQueryString();

        $totalRequests = StockRequest::where('shop_id', $shop?->id)->count();
        $pendingRequests = StockRequest::where('shop_id', $shop?->id)->where('status', 'pending')->count();
        $completedRequests = StockRequest::where('shop_id', $shop?->id)->where('status', 'completed')->count();

        return view('shop.stock-request.index', compact('requests', 'totalRequests', 'pendingRequests', 'completedRequests', 'warehouse', 'shop'));
    }

    public function inventory()
    {
        $shop = $this->getShop();
        $warehouse = $shop?->warehouse ?? WarehouseRepository::getCentralWarehouse();

        $status = request('status');
        $search = request('search');

        $query = Product::where('shop_id', $shop?->id)
            ->where('is_digital', false)
            ->with(['masterProduct', 'brand', 'categories']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            });
        }

        if ($status === 'low_stock') {
            $query->where('quantity', '>', 0)->where('quantity', '<=', 10);
        } elseif ($status === 'out_of_stock') {
            $query->where('quantity', 0);
        } elseif ($status === 'in_stock') {
            $query->where('quantity', '>', 10);
        }

        $products = $query->latest()->paginate(15)->withQueryString();

        // Calculate total completed warehouse requested quantity per product for this shop
        $completedRequestedQuantities = StockRequestItem::whereHas('stockRequest', function ($q) use ($shop) {
            $q->where('shop_id', $shop?->id)->where('status', 'completed');
        })->selectRaw('product_id, SUM(quantity) as total_requested')
            ->groupBy('product_id')
            ->pluck('total_requested', 'product_id');

        $products->getCollection()->transform(function ($product) use ($completedRequestedQuantities) {
            $masterId = $product->master_product_id ?? $product->id;
            $approvedQty = (int) ($completedRequestedQuantities->get($masterId) ?? $completedRequestedQuantities->get($product->id) ?? 0);

            // Total requested stock is approved quantity from warehouse (or current quantity if direct shop product)
            $product->total_requested_qty = max($approvedQty, $product->quantity);
            $product->sold_qty = max(0, $product->total_requested_qty - $product->quantity);

            return $product;
        });

        // Calculate shop inventory summary metrics
        $allShopProducts = Product::where('shop_id', $shop?->id)->where('is_digital', false)->get();
        $totalSkuLines = $allShopProducts->count();
        $totalStockUnits = $allShopProducts->sum('quantity');
        $totalInventoryValue = $allShopProducts->sum(fn ($p) => $p->quantity * $p->price);
        $lowStockCount = $allShopProducts->filter(fn ($p) => $p->quantity <= 10)->count();

        return view('shop.stock-request.inventory', compact(
            'products', 'warehouse', 'shop', 'totalSkuLines',
            'totalStockUnits', 'totalInventoryValue', 'lowStockCount'
        ));
    }

    public function create()
    {
        $shop = $this->getShop();
        $warehouse = $shop?->warehouse ?? WarehouseRepository::getCentralWarehouse();

        if (! $warehouse) {
            return redirect()->route('shop.stock-request.index')->with('error', __('No linked warehouse found for your shop.'));
        }

        $search = request('search');

        // Get warehouse stock for products available in the shop's linked warehouse
        $warehouseStocks = WarehouseStock::where('warehouse_id', $warehouse->id)
            ->get()
            ->keyBy('product_id');

        $query = Product::where('is_digital', false)
            ->whereNull('master_product_id')
            ->with(['brand', 'categories']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $masterProducts = $query->latest()->get()->map(function ($product) use ($warehouseStocks) {
            $stockInWh = $warehouseStocks->get($product->id)?->quantity ?? 0;
            $product->warehouse_qty = $stockInWh;

            return $product;
        });

        return view('shop.stock-request.create', compact('warehouse', 'masterProducts', 'shop'));
    }

    public function store(StockRequestRequest $request)
    {
        $shop = $this->getShop();
        $warehouse = $shop?->warehouse ?? WarehouseRepository::getCentralWarehouse();

        if (! $warehouse) {
            return back()->with('error', __('No linked warehouse available for your shop.'));
        }

        // Filter valid requested items with quantity > 0
        $items = collect($request->items)->filter(function ($item) {
            return isset($item['product_id']) && isset($item['quantity']) && (int) $item['quantity'] > 0;
        });

        if ($items->isEmpty()) {
            return back()->with('error', __('Please select at least one product with quantity greater than 0.'));
        }

        $stockRequest = StockRequest::create([
            'shop_id' => $shop->id,
            'warehouse_id' => $warehouse->id, // Strictly locked to shop's linked warehouse
            'status' => 'pending',
            'notes' => $request->notes,
        ]);

        foreach ($items as $item) {
            $stockRequest->items()->create([
                'product_id' => $item['product_id'],
                'color_id' => $item['color_id'] ?? null,
                'size_id' => $item['size_id'] ?? null,
                'quantity' => (int) $item['quantity'],
            ]);
        }

        return redirect()->route('shop.stock-request.index')->with('success', __('Stock request submitted to warehouse.'));
    }

    public function show(StockRequest $stockRequest)
    {
        $shop = $this->getShop();

        if ($stockRequest->shop_id !== $shop?->id) {
            abort(403);
        }

        $stockRequest->load(['warehouse', 'items.product', 'items.color', 'items.size']);

        return view('shop.stock-request.show', compact('stockRequest'));
    }
}
