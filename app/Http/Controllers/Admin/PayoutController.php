<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use App\Models\ShopMonthlyPayout;
use App\Services\PayoutService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PayoutController extends Controller
{
    /**
     * Show the payout history with month/year filters.
     */
    public function index(): View
    {
        $year = request('year');
        $month = request('month');
        $months = collect(range(1, 12));

        $payouts = ShopMonthlyPayout::with('shop.user')
            ->when($year, function ($query) use ($year) {
                return $query->where('year', $year);
            })
            ->when($month, function ($query) use ($month) {
                return $query->where('month', $month);
            })
            ->orderByDesc('year')
            ->orderByDesc('month')
            ->orderByDesc('id')
            ->paginate(20);

        return view('admin.payout.index', compact('payouts', 'months', 'year', 'month'));
    }

    /**
     * Run the monthly payout for the requested month.
     */
    public function run(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'month' => ['required', 'integer', 'between:1,12'],
            'year' => ['required', 'integer', 'between:2000,2100'],
        ]);

        $result = PayoutService::payoutMonth((int) $data['year'], (int) $data['month']);

        $summary = sprintf(
            'Payouts for %04d-%02d: %d processed, %d credited, %d skipped, %d errors.',
            $data['year'],
            $data['month'],
            $result['processed'],
            $result['credited'],
            $result['skipped'],
            count($result['errors'])
        );

        if ($result['errors'] !== []) {
            $details = implode("\n", array_map(
                fn (array $error) => "Shop {$error['shop_id']}: {$error['message']}",
                $result['errors']
            ));

            return redirect()->route('admin.payout.network', ['year' => $data['year'], 'month' => $data['month']])
                ->with('error', $summary."\n".$details);
        }

        return redirect()->route('admin.payout.network', ['year' => $data['year'], 'month' => $data['month']])
            ->with('success', $summary);
    }

    /**
     * Show the MLM network tree for a month.
     */
    public function network(): View
    {
        [$year, $month] = $this->resolveMonth(request('year'), request('month'));

        $nodes = PayoutService::networkForMonth($year, $month);
        $months = collect(range(1, 12));
        $isPaid = ShopMonthlyPayout::where('year', $year)->where('month', $month)->exists();

        return view('admin.payout.network', compact('nodes', 'months', 'year', 'month', 'isPaid'));
    }

    /**
     * AJAX: direct children of a node for lazy expansion.
     */
    public function children(Request $request, Shop $shop): JsonResponse
    {
        [$year, $month] = $this->resolveMonth($request->query('year'), $request->query('month'));

        return response()->json(PayoutService::childrenOf($shop->id, $year, $month));
    }

    /**
     * Show the review-then-run form with a live preview tree.
     */
    public function runForm(): View
    {
        [$year, $month] = $this->resolveMonth(request('year'), request('month'));

        $nodes = PayoutService::networkForMonth($year, $month);
        $months = collect(range(1, 12));
        $isPaid = ShopMonthlyPayout::where('year', $year)->where('month', $month)->exists();

        return view('admin.payout.run', compact('nodes', 'months', 'year', 'month', 'isPaid'));
    }

    /**
     * Resolve the requested month, defaulting to the latest month with a
     * snapshot, else the previous month.
     *
     * @return array{0: int, 1: int} [year, month]
     */
    private function resolveMonth($year, $month): array
    {
        $year = (int) $year;
        $month = (int) $month;

        if ($month >= 1 && $month <= 12 && $year >= 2000 && $year <= 2100) {
            return [$year, $month];
        }

        $latest = ShopMonthlyPayout::orderByDesc('year')->orderByDesc('month')->first();
        if ($latest !== null) {
            return [(int) $latest->year, (int) $latest->month];
        }

        $now = now()->subMonthNoOverflow();

        return [$now->year, $now->month];
    }
}
