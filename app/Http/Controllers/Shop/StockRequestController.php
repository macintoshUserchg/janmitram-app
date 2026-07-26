<?php

declare(strict_types=1);

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Http\Requests\StockRequestRequest;
use App\Models\Product;
use App\Models\StockRequest;
use App\Models\WarehouseStock;
use App\Repositories\WarehouseRepository;

class StockRequestController extends Controller
{
    public function index()
    {
        $shop = generaleSetting('shop');

        $requests = StockRequest::where('shop_id', $shop?->id)
            ->with(['warehouse', 'items.product'])
            ->latest()
            ->paginate(15);

        return view('shop.stock-request.index', compact('requests'));
    }

    public function create()
    {
        $shop = generaleSetting('shop');
        $warehouse = $shop?->warehouse ?? WarehouseRepository::getCentralWarehouse();

        if (! $warehouse) {
            return redirect()->route('shop.stock-request.index')->with('error', __('No linked warehouse found for your shop.'));
        }

        $warehouseStocks = WarehouseStock::where('warehouse_id', $warehouse->id)
            ->with(['product', 'color', 'size'])
            ->get();

        $masterProducts = Product::where('is_digital', false)
            ->whereNull('master_product_id')
            ->get();

        return view('shop.stock-request.create', compact('warehouse', 'warehouseStocks', 'masterProducts'));
    }

    public function store(StockRequestRequest $request)
    {
        $shop = generaleSetting('shop');
        $warehouse = $shop?->warehouse ?? WarehouseRepository::getCentralWarehouse();

        if (! $warehouse) {
            return back()->with('error', __('No linked warehouse available.'));
        }

        $stockRequest = StockRequest::create([
            'shop_id' => $shop->id,
            'warehouse_id' => $warehouse->id,
            'status' => 'pending',
            'notes' => $request->notes,
        ]);

        foreach ($request->items as $item) {
            $stockRequest->items()->create([
                'product_id' => $item['product_id'],
                'color_id' => $item['color_id'] ?? null,
                'size_id' => $item['size_id'] ?? null,
                'quantity' => $item['quantity'],
            ]);
        }

        return redirect()->route('shop.stock-request.index')->with('success', __('Stock request submitted to warehouse.'));
    }

    public function show(StockRequest $stockRequest)
    {
        $shop = generaleSetting('shop');

        if ($stockRequest->shop_id !== $shop?->id) {
            abort(403);
        }

        $stockRequest->load(['warehouse', 'items.product', 'items.color', 'items.size']);

        return view('shop.stock-request.show', compact('stockRequest'));
    }
}
