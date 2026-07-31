<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\WarehouseRequest;
use App\Models\Color;
use App\Models\Product;
use App\Models\Size;
use App\Models\Warehouse;
use App\Models\WarehouseStock;
use App\Services\WarehouseService;

class WarehouseController extends Controller
{
    private static function getCentralWarehouse(): ?Warehouse
    {
        return Warehouse::where('is_default', true)->first() ?? Warehouse::first();
    }

    public function index()
    {
        $warehouses = Warehouse::with('stocks')->latest()->paginate(15);

        return view('admin.warehouse.index', compact('warehouses'));
    }

    public function create()
    {
        $centralWarehouse = self::getCentralWarehouse();

        return view('admin.warehouse.create', compact('centralWarehouse'));
    }

    public function store(WarehouseRequest $request)
    {
        $data = $request->validated();

        WarehouseService::createWarehouse($data);

        return redirect()->route('admin.warehouse.index')->with('success', __('Sub-warehouse created successfully under Central Warehouse.'));
    }

    public function show(Warehouse $warehouse)
    {
        $stocks = WarehouseStock::where('warehouse_id', $warehouse->id)
            ->with(['product', 'color', 'size'])
            ->paginate(20);

        return view('admin.warehouse.show', compact('warehouse', 'stocks'));
    }

    public function edit(Warehouse $warehouse)
    {
        $centralWarehouse = self::getCentralWarehouse();

        return view('admin.warehouse.edit', compact('warehouse', 'centralWarehouse'));
    }

    public function update(WarehouseRequest $request, Warehouse $warehouse)
    {
        $warehouse->update($request->validated());

        return redirect()->route('admin.warehouse.index')->with('success', __('Warehouse updated successfully.'));
    }

    public function destroy(Warehouse $warehouse)
    {
        if ($warehouse->is_default) {
            return back()->with('error', __('Central warehouse cannot be deleted.'));
        }

        $warehouse->delete();

        return redirect()->route('admin.warehouse.index')->with('success', __('Warehouse deleted successfully.'));
    }

    public function stock(Warehouse $warehouse)
    {
        $warehouseStocks = WarehouseStock::where('warehouse_id', $warehouse->id)
            ->get()
            ->keyBy('product_id');

        $products = Product::where('is_digital', false)
            ->whereNull('master_product_id')
            ->get()
            ->map(function ($product) use ($warehouseStocks) {
                $product->current_wh_qty = $warehouseStocks->get($product->id)?->quantity ?? 0;

                return $product;
            });

        $colors = Color::all();
        $sizes = Size::all();

        return view('admin.warehouse.stock', compact('warehouse', 'products', 'colors', 'sizes'));
    }

    public function addStock(Warehouse $warehouse)
    {
        request()->validate([
            'product_id' => 'required|exists:products,id',
            'color_id' => 'nullable|exists:colors,id',
            'size_id' => 'nullable|exists:sizes,id',
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string',
        ]);

        $product = Product::findOrFail(request('product_id'));
        $product->update(['is_stock_managed' => true]);

        WarehouseService::addStock(
            $warehouse,
            $product,
            (int) request('quantity'),
            request('color_id') ? (int) request('color_id') : null,
            request('size_id') ? (int) request('size_id') : null,
            'admin_addition',
            null,
            request('notes')
        );

        return redirect()->route('admin.warehouse.show', $warehouse->id)->with('success', __('Stock added successfully.'));
    }

    public function clearStock(Warehouse $warehouse)
    {
        WarehouseStock::where('warehouse_id', $warehouse->id)->delete();

        return redirect()->route('admin.warehouse.show', $warehouse->id)->with('success', __('All stock items cleared from warehouse.'));
    }

    public function destroyStock(WarehouseStock $stock)
    {
        $warehouseId = $stock->warehouse_id;
        $stock->delete();

        return redirect()->route('admin.warehouse.show', $warehouseId)->with('success', __('Stock item removed successfully.'));
    }
}
