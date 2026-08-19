<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StockRequest;
use App\Models\WarehouseStock;
use App\Services\WarehouseService;
use Mpdf\Mpdf;

class StockRequestController extends Controller
{
    use SortableIndex;

    public function index()
    {
        [$sort, $direction] = $this->resolveSort();

        $requests = $this->applySort(StockRequest::with(['shop', 'warehouse', 'items.product']), $sort, $direction)
            ->paginate($this->resolvePerPage())
            ->withQueryString();

        return view('admin.stock-request.index', compact('requests', 'sort', 'direction'));
    }

    public function show(StockRequest $stockRequest)
    {
        $stockRequest->load(['shop', 'warehouse', 'items.product', 'items.color', 'items.size']);

        $itemDetails = [];
        $hasShortfall = false;

        foreach ($stockRequest->items as $item) {
            $query = WarehouseStock::where('warehouse_id', $stockRequest->warehouse_id)
                ->where('product_id', $item->product_id);

            // Only constrain to a specific variant when one was requested; otherwise fall
            // back to any stock row (across variants), matching what fulfillStockRequest picks.
            if ($item->color_id !== null || $item->size_id !== null) {
                if ($item->color_id) {
                    $query->where('color_id', $item->color_id);
                } else {
                    $query->whereNull('color_id');
                }

                if ($item->size_id) {
                    $query->where('size_id', $item->size_id);
                } else {
                    $query->whereNull('size_id');
                }

                $available = $query->first()?->quantity ?? 0;
            } else {
                $available = $query->orderByDesc('quantity')->first()?->quantity ?? 0;
            }

            $shortfall = max(0, $item->quantity - $available);

            if ($shortfall > 0) {
                $hasShortfall = true;
            }

            $itemDetails[] = [
                'item' => $item,
                'available' => $available,
                'shortfall' => $shortfall,
            ];
        }

        return view('admin.stock-request.show', compact('stockRequest', 'itemDetails', 'hasShortfall'));
    }

    public function approve(StockRequest $stockRequest)
    {
        if ($stockRequest->status !== 'pending') {
            return back()->with('error', __('Stock request is already processed.'));
        }

        WarehouseService::fulfillStockRequest($stockRequest);

        return redirect()->route('admin.stock-request.index')->with('success', __('Stock request approved and fulfilled successfully.'));
    }

    public function reject(StockRequest $stockRequest)
    {
        if ($stockRequest->status !== 'pending') {
            return back()->with('error', __('Stock request is already processed.'));
        }

        $stockRequest->update(['status' => 'rejected']);

        return redirect()->route('admin.stock-request.index')->with('success', __('Stock request rejected.'));
    }

    public function invoice(StockRequest $stockRequest)
    {
        if ($stockRequest->status !== 'completed') {
            return back()->with('error', __('Invoice is only available for completed stock requests.'));
        }

        $stockRequest->load(['shop', 'warehouse', 'items.product.brand', 'items.color', 'items.size']);

        if (request('download') === 'pdf' && class_exists('\Mpdf\Mpdf')) {
            $mPdf = new Mpdf([
                'mode' => 'utf-8',
                'format' => 'A4',
                'margin_left' => 12,
                'margin_right' => 12,
                'margin_top' => 12,
                'margin_bottom' => 12,
                'tempDir' => storage_path('app/public/mpdf_tmp'),
            ]);
            $view = view('PDF.stock-request-invoice', compact('stockRequest'))->render();
            $mPdf->WriteHTML($view);

            return $mPdf->Output('invoice-stock-request-'.$stockRequest->id.'.pdf', 'I');
        }

        return view('PDF.stock-request-invoice', compact('stockRequest'));
    }
}
