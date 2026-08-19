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
use Mpdf\Mpdf;

class PayoutController extends Controller
{
    /**
     * Show the payout history with month/year filters.
     */
    public function index(): View
    {
        $year = request('year');
        $month = request('month');
        $search = request('search');
        $months = collect(range(1, 12));

        $payouts = ShopMonthlyPayout::with('shop.user')
            ->when($year, function ($query) use ($year) {
                return $query->where('year', $year);
            })
            ->when($month, function ($query) use ($month) {
                return $query->where('month', $month);
            })
            ->when($search, function ($query) use ($search) {
                return $query->whereHas('shop', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($uq) use ($search) {
                            $uq->where('name', 'like', "%{$search}%");
                        });
                });
            })
            ->orderByDesc('year')
            ->orderByDesc('month')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        return view('admin.payout.index', compact('payouts', 'months', 'year', 'month', 'search'));
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
     * Show the payout user guide and compensation plan documentation.
     */
    public function guide(): View
    {
        return view('admin.payout.guide');
    }

    /**
     * Download or view monthly payout slip for any shop (admin view).
     */
    public function slip(ShopMonthlyPayout $payout, Request $request)
    {
        $payout->load(['shop.user', 'shop.parent']);

        if ($request->query('view') === 'html') {
            return view('PDF.payout-slip', compact('payout'));
        }

        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_left' => 12,
            'margin_right' => 12,
            'margin_top' => 12,
            'margin_bottom' => 12,
            'tempDir' => storage_path('app/public/mpdf_tmp'),
        ]);

        $html = view('PDF.payout-slip', compact('payout'))->render();
        $mpdf->WriteHTML($html);

        $filename = "payout-slip-JAN-{$payout->shop_id}-{$payout->year}-".str_pad($payout->month, 2, '0', STR_PAD_LEFT).'.pdf';

        return response($mpdf->Output($filename, 'S'), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.$filename.'"',
        ]);
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

        $now = now();

        return [$now->year, $now->month];
    }
}
