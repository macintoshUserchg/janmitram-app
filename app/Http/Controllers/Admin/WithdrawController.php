<?php

namespace App\Http\Controllers\Admin;

use App\Events\ProductApproveEvent;
use App\Http\Controllers\Controller;
use App\Models\Shop;
use App\Models\ShopMonthlyPayout;
use App\Models\Withdraw;
use App\Repositories\NotificationRepository;
use App\Repositories\WithdrawRepository;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class WithdrawController extends Controller
{
    use SortableIndex;

    /**
     * Show withdraw requests list with financial period analysis and multi-filtering.
     */
    public function index(Request $request)
    {
        $query = $this->buildFilteredQuery($request);

        // Compute summary KPI metrics based on the current filter context
        $metricsQuery = clone $query;
        $summary = [
            'total_count' => (clone $metricsQuery)->count(),
            'total_amount' => (float) (clone $metricsQuery)->sum('amount'),
            'pending_count' => (clone $metricsQuery)->where('status', 'pending')->count(),
            'pending_amount' => (float) (clone $metricsQuery)->where('status', 'pending')->sum('amount'),
            'approved_count' => (clone $metricsQuery)->where('status', 'approved')->count(),
            'approved_amount' => (float) (clone $metricsQuery)->where('status', 'approved')->sum('amount'),
            'denied_count' => (clone $metricsQuery)->where('status', 'denied')->count(),
            'denied_amount' => (float) (clone $metricsQuery)->where('status', 'denied')->sum('amount'),
        ];

        $allowedColumns = ['id', 'created_at', 'amount', 'status', 'shop_name', 'name'];
        [$sort, $direction] = $this->resolveSort($allowedColumns, 'id', 'desc');

        if ($sort === 'shop_name') {
            $query->leftJoin('shops', 'shops.id', '=', 'withdraws.shop_id')
                ->orderBy('shops.name', $direction)
                ->select('withdraws.*');
        } else {
            $this->applySort($query, $sort, $direction, $allowedColumns);
        }

        $withdraws = $query->with(['shop.user', 'shop.kyc', 'shop.parent'])
            ->paginate($this->resolvePerPage(20))
            ->withQueryString();

        $shops = Shop::orderBy('name')->get(['id', 'name']);

        // Available Financial Years for filter dropdown (e.g. FY 2026-27, FY 2025-26, FY 2024-25)
        $currentYear = now()->month >= 4 ? now()->year : now()->year - 1;
        $financialYears = [];
        for ($y = $currentYear; $y >= 2024; $y--) {
            $financialYears[] = [
                'value' => "fy_{$y}_".($y + 1),
                'label' => "FY {$y}-".substr((string) ($y + 1), -2),
                'start' => "{$y}-04-01",
                'end' => ($y + 1).'-03-31',
            ];
        }

        return view('admin.withdraw.index', compact('withdraws', 'summary', 'shops', 'financialYears', 'sort', 'direction'));
    }

    /**
     * Export filtered withdrawal records to a CSV file.
     */
    public function export(Request $request): StreamedResponse
    {
        $query = $this->buildFilteredQuery($request)
            ->with(['shop.user', 'shop.kyc', 'shop.parent'])
            ->orderByDesc('id');

        $filename = 'withdrawals_export_'.now()->format('Y_m_d_His').'.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return response()->stream(function () use ($query) {
            $handle = fopen('php://output', 'w');

            // UTF-8 BOM for Microsoft Excel compatibility
            fwrite($handle, "\xEF\xBB\xBF");

            // CSV Header Row
            fputcsv($handle, [
                'Request ID',
                'Request Date & Time',
                'Shop ID',
                'Shop Name',
                'Owner Name',
                'Contact Phone',
                'Contact Email',
                'Sponsor Code',
                'Bank Name',
                'Account Number',
                'IFSC Code',
                'Aadhaar Number',
                'PAN Number',
                'Withdrawal Method',
                'Amount (INR)',
                'Status',
                'Reason / Remarks',
                'Last Updated At',
            ]);

            $query->chunk(200, function ($records) use ($handle) {
                foreach ($records as $withdraw) {
                    $shop = $withdraw->shop;
                    $user = $shop?->user;
                    $kyc = $shop?->kyc;

                    fputcsv($handle, [
                        '#W-'.str_pad((string) $withdraw->id, 5, '0', STR_PAD_LEFT),
                        $withdraw->created_at?->format('Y-m-d H:i:s'),
                        $shop ? $shop->id : '',
                        $shop ? $shop->name : ($withdraw->name ?? '—'),
                        $user ? $user->fullName : ($withdraw->name ?? '—'),
                        $withdraw->contact_number ?? $user?->phone ?? '',
                        $user?->email ?? '',
                        $shop?->parent ? $shop->parent->referral_code : 'Root',
                        $kyc?->bank_name ?? '—',
                        $kyc?->account_number ? "'".$kyc->account_number : '—',
                        $kyc?->ifsc ?? '—',
                        $kyc?->aadhaar_number ? "'".$kyc->aadhaar_number : '—',
                        $kyc?->pan_number ?? '—',
                        $withdraw->withdraw_method ?? 'Bank Transfer',
                        number_format((float) $withdraw->amount, 2, '.', ''),
                        ucfirst($withdraw->status),
                        $withdraw->reason ?? '',
                        $withdraw->updated_at?->format('Y-m-d H:i:s'),
                    ]);
                }
            });

            fclose($handle);
        }, 200, $headers);
    }

    /**
     * Show withdraw request details.
     */
    public function show(Withdraw $withdraw)
    {
        $shop = $withdraw->shop;
        $walletBalance = (float) ($shop->user->wallet?->balance ?? 0);
        $lifetimePayouts = (float) ShopMonthlyPayout::where('shop_id', $shop->id)->sum('total_payout');
        $approvedWithdraws = (float) Withdraw::where('shop_id', $shop->id)->where('status', 'approved')->sum('amount');
        $pendingWithdraws = (float) Withdraw::where('shop_id', $shop->id)->where('status', 'pending')->sum('amount');
        $latestPayout = ShopMonthlyPayout::where('shop_id', $shop->id)->latest('id')->first();

        return view('admin.withdraw.show', compact(
            'withdraw',
            'walletBalance',
            'lifetimePayouts',
            'approvedWithdraws',
            'pendingWithdraws',
            'latestPayout'
        ));
    }

    /**
     * Update withdraw request status.
     */
    public function update(Withdraw $withdraw, Request $request)
    {
        $previousStatus = $withdraw->status;

        $result = WithdrawRepository::updateWithdraw($withdraw, $request);

        if (! $result['ok']) {
            return back()->with('error', $result['message']);
        }

        // Only notify on a genuine status change (skip idempotent re-approvals).
        if ($previousStatus === $withdraw->status) {
            return back()->withSuccess($result['message']);
        }

        // Admin notification message
        $message = 'Withdraw request '.$withdraw->status;
        try {
            ProductApproveEvent::dispatch($message, $withdraw->shop->id);
        } catch (\Throwable $th) {
        }

        $data = (object) [
            'title' => $message,
            'content' => 'Withdraw request '.$withdraw->status.' from admin',
            'url' => '/shop/withdraw',
            'icon' => $request->status == 'approved' ? 'bi-check2-circle' : 'bi-x-octagon-fill',
            'type' => $request->status == 'approved' ? 'success' : 'danger',
            'shop_id' => $withdraw->shop_id,
            'withdraw_id' => $withdraw->id,
        ];
        // Store notification
        NotificationRepository::storeByRequest($data);

        return back()->withSuccess(__('Withdraw request updated successfully'));
    }

    /**
     * Build the filtered Eloquent query based on request parameters.
     */
    private function buildFilteredQuery(Request $request): Builder
    {
        $query = Withdraw::query();

        // 1. Status Filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // 2. Shop Filter
        if ($request->filled('shop_id')) {
            $query->where('shop_id', $request->shop_id);
        }

        // 3. Amount Range Filters
        if ($request->filled('min_amount')) {
            $query->where('amount', '>=', (float) $request->min_amount);
        }
        if ($request->filled('max_amount')) {
            $query->where('amount', '<=', (float) $request->max_amount);
        }

        // 4. Free Search (Shop Name, Owner Name, Contact Number, or Request ID)
        if ($request->filled('search')) {
            $search = trim($request->search);
            $cleanId = ltrim($search, '#Ww- ');

            $query->where(function ($q) use ($search, $cleanId) {
                if (is_numeric($cleanId)) {
                    $q->orWhere('id', (int) $cleanId);
                }
                $q->orWhere('name', 'like', "%{$search}%")
                    ->orWhere('contact_number', 'like', "%{$search}%")
                    ->orWhere('reason', 'like', "%{$search}%")
                    ->orWhereHas('shop', function ($sq) use ($search) {
                        $sq->where('name', 'like', "%{$search}%")
                            ->orWhereHas('user', function ($uq) use ($search) {
                                $uq->where('first_name', 'like', "%{$search}%")
                                    ->orWhere('last_name', 'like', "%{$search}%")
                                    ->orWhere('email', 'like', "%{$search}%")
                                    ->orWhere('phone', 'like', "%{$search}%");
                            });
                    });
            });
        }

        // 5. Financial Period / Date Filtering
        $period = $request->get('period', 'all');
        $startDate = null;
        $endDate = null;

        switch ($period) {
            case 'today':
                $startDate = Carbon::today()->startOfDay();
                $endDate = Carbon::today()->endOfDay();
                break;
            case 'this_month':
                $startDate = Carbon::now()->startOfMonth()->startOfDay();
                $endDate = Carbon::now()->endOfMonth()->endOfDay();
                break;
            case 'last_month':
                $startDate = Carbon::now()->subMonth()->startOfMonth()->startOfDay();
                $endDate = Carbon::now()->subMonth()->endOfMonth()->endOfDay();
                break;
            case 'this_quarter':
                $currentMonth = Carbon::now()->month;
                $currentYear = Carbon::now()->year;
                if ($currentMonth >= 4 && $currentMonth <= 6) {
                    $startDate = Carbon::create($currentYear, 4, 1)->startOfDay();
                    $endDate = Carbon::create($currentYear, 6, 30)->endOfDay();
                } elseif ($currentMonth >= 7 && $currentMonth <= 9) {
                    $startDate = Carbon::create($currentYear, 7, 1)->startOfDay();
                    $endDate = Carbon::create($currentYear, 9, 30)->endOfDay();
                } elseif ($currentMonth >= 10 && $currentMonth <= 12) {
                    $startDate = Carbon::create($currentYear, 10, 1)->startOfDay();
                    $endDate = Carbon::create($currentYear, 12, 31)->endOfDay();
                } else {
                    $startDate = Carbon::create($currentYear, 1, 1)->startOfDay();
                    $endDate = Carbon::create($currentYear, 3, 31)->endOfDay();
                }
                break;
            case 'this_fy':
                $startYear = Carbon::now()->month >= 4 ? Carbon::now()->year : Carbon::now()->year - 1;
                $startDate = Carbon::create($startYear, 4, 1)->startOfDay();
                $endDate = Carbon::create($startYear + 1, 3, 31)->endOfDay();
                break;
            case 'last_fy':
                $startYear = (Carbon::now()->month >= 4 ? Carbon::now()->year : Carbon::now()->year - 1) - 1;
                $startDate = Carbon::create($startYear, 4, 1)->startOfDay();
                $endDate = Carbon::create($startYear + 1, 3, 31)->endOfDay();
                break;
            case 'custom':
                if ($request->filled('start_date')) {
                    $startDate = Carbon::parse($request->start_date)->startOfDay();
                }
                if ($request->filled('end_date')) {
                    $endDate = Carbon::parse($request->end_date)->endOfDay();
                }
                break;
            default:
                // Check if a specific FY was selected (e.g. fy_2025_2026)
                if (str_starts_with($period, 'fy_')) {
                    $parts = explode('_', $period);
                    if (count($parts) === 3 && is_numeric($parts[1]) && is_numeric($parts[2])) {
                        $startDate = Carbon::create((int) $parts[1], 4, 1)->startOfDay();
                        $endDate = Carbon::create((int) $parts[2], 3, 31)->endOfDay();
                    }
                }
                break;
        }

        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        } elseif ($startDate) {
            $query->where('created_at', '>=', $startDate);
        } elseif ($endDate) {
            $query->where('created_at', '<=', $endDate);
        }

        return $query;
    }
}
