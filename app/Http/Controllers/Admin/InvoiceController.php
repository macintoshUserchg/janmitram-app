<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StockRequest;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $query = StockRequest::where('status', 'completed')
            ->with(['shop', 'warehouse', 'items.product']);

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                    ->orWhereHas('shop', function ($sq) use ($search) {
                        $sq->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('warehouse', function ($wq) use ($search) {
                        $wq->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $invoices = $query->latest()->paginate(15);

        // Aggregate statistics for completed stock request dispatches
        $completedRequests = StockRequest::where('status', 'completed')->with('items.product')->get();
        $totalInvoices = $completedRequests->count();
        $totalDispatchedUnits = 0;
        $totalValuation = 0;

        foreach ($completedRequests as $sr) {
            foreach ($sr->items as $item) {
                $qty = $item->quantity;
                $price = $item->product?->price ?? 0;
                $totalDispatchedUnits += $qty;
                $totalValuation += ($qty * $price);
            }
        }

        return view('admin.invoice.index', compact(
            'invoices',
            'totalInvoices',
            'totalDispatchedUnits',
            'totalValuation'
        ));
    }
}
