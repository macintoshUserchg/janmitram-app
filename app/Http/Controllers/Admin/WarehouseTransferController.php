<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Exceptions\InsufficientStockException;
use App\Http\Controllers\Controller;
use App\Http\Requests\WarehouseTransferRequest;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\WarehouseTransfer;
use App\Services\WarehouseService;

class WarehouseTransferController extends Controller
{
    public function index()
    {
        $transfers = WarehouseTransfer::with(['fromWarehouse', 'toWarehouse', 'items.product'])
            ->latest()
            ->paginate(15);

        return view('admin.warehouse-transfer.index', compact('transfers'));
    }

    public function create()
    {
        $warehouses = Warehouse::all();
        $products = Product::where('is_digital', false)
            ->whereNull('master_product_id')
            ->get();

        return view('admin.warehouse-transfer.create', compact('warehouses', 'products'));
    }

    public function store(WarehouseTransferRequest $request)
    {
        $transfer = WarehouseTransfer::create([
            'from_warehouse_id' => $request->from_warehouse_id,
            'to_warehouse_id' => $request->to_warehouse_id,
            'status' => 'pending',
            'notes' => $request->notes,
        ]);

        foreach ($request->items as $item) {
            $transfer->items()->create([
                'product_id' => $item['product_id'],
                'color_id' => $item['color_id'] ?? null,
                'size_id' => $item['size_id'] ?? null,
                'quantity' => $item['quantity'],
            ]);
        }

        return redirect()->route('admin.warehouse-transfer.show', $transfer->id)->with('success', __('Warehouse transfer created.'));
    }

    public function show(WarehouseTransfer $warehouseTransfer)
    {
        $warehouseTransfer->load(['fromWarehouse', 'toWarehouse', 'items.product', 'items.color', 'items.size']);

        return view('admin.warehouse-transfer.show', compact('warehouseTransfer'));
    }

    public function complete(WarehouseTransfer $warehouseTransfer)
    {
        if ($warehouseTransfer->status !== 'pending') {
            return back()->with('error', __('Transfer is already processed.'));
        }

        try {
            foreach ($warehouseTransfer->items as $item) {
                WarehouseService::transfer(
                    $warehouseTransfer->fromWarehouse,
                    $warehouseTransfer->toWarehouse,
                    $item->product,
                    (int) $item->quantity,
                    $item->color_id ? (int) $item->color_id : null,
                    $item->size_id ? (int) $item->size_id : null,
                    (int) $warehouseTransfer->id,
                    $warehouseTransfer->notes
                );
            }

            $warehouseTransfer->update(['status' => 'completed']);

            return redirect()->route('admin.warehouse-transfer.index')->with('success', __('Warehouse transfer completed successfully.'));
        } catch (InsufficientStockException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function cancel(WarehouseTransfer $warehouseTransfer)
    {
        if ($warehouseTransfer->status !== 'pending') {
            return back()->with('error', __('Transfer is already processed.'));
        }

        $warehouseTransfer->update(['status' => 'cancelled']);

        return redirect()->route('admin.warehouse-transfer.index')->with('success', __('Warehouse transfer cancelled.'));
    }
}
