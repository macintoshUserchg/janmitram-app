<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StockRequest;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    use SortableIndex;

    public function index(Request $request)
    {
        $allowedColumns = ['id', 'created_at', 'updated_at', 'shop_name', 'warehouse_name'];
        [$sort, $direction] = $this->resolveSort($allowedColumns, 'id', 'desc');

        $query = StockRequest::where('status', 'completed')
            ->with(['shop', 'warehouse', 'items.product']);

        if ($request->filled('search')) {
            $search = trim($request->search);
            $cleanId = ltrim($search, '#INV-SR-sr- ');
            $query->where(function ($q) use ($search, $cleanId) {
                if (is_numeric($cleanId)) {
                    $q->orWhere('stock_requests.id', (int) $cleanId);
                }
                $q->orWhere('stock_requests.id', 'like', "%{$search}%")
                    ->orWhereHas('shop', function ($sq) use ($search) {
                        $sq->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('warehouse', function ($wq) use ($search) {
                        $wq->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if ($sort === 'shop_name') {
            $query->leftJoin('shops', 'shops.id', '=', 'stock_requests.shop_id')
                ->orderBy('shops.name', $direction)
                ->select('stock_requests.*');
        } elseif ($sort === 'warehouse_name') {
            $query->leftJoin('warehouses', 'warehouses.id', '=', 'stock_requests.warehouse_id')
                ->orderBy('warehouses.name', $direction)
                ->select('stock_requests.*');
        } else {
            $this->applySort($query, $sort, $direction, $allowedColumns);
        }

        $invoices = $query->paginate($this->resolvePerPage(15))->withQueryString();

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
            'totalValuation',
            'sort',
            'direction'
        ));
    }
}
