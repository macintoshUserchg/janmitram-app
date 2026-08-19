<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Http\Requests\ShopCreateRequest;
use App\Models\Shop;
use App\Models\ShopMonthlyPayout;
use App\Repositories\ShopRepository;
use App\Services\PayoutService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Mpdf\Mpdf;

class PayoutController extends Controller
{
    /**
     * Display shop owner's payout history and current month earnings projection.
     */
    public function index(): View
    {
        $user = auth()->user();
        $shop = $user->shop ?? Shop::where('user_id', $user->id)->first();

        [$year, $month] = $this->resolveMonth(request('year'), request('month'));
        $months = collect(range(1, 12));

        if (! $shop) {
            return view('shop.payout.index', [
                'shop' => null,
                'payouts' => collect(),
                'months' => $months,
                'year' => $year,
                'month' => $month,
                'currentTree' => null,
                'lifetimeTotal' => 0,
                'lifetimePersonal' => 0,
                'lifetimeGroup' => 0,
            ]);
        }

        $payouts = ShopMonthlyPayout::where('shop_id', $shop->id)
            ->orderByDesc('year')
            ->orderByDesc('month')
            ->paginate(15);

        $lifetimeTotal = (float) ShopMonthlyPayout::where('shop_id', $shop->id)->sum('total_payout');
        $lifetimePersonal = (float) ShopMonthlyPayout::where('shop_id', $shop->id)->sum('personal_sales');
        $lifetimeGroup = (float) ShopMonthlyPayout::where('shop_id', $shop->id)->sum('group_sales');

        $currentTree = PayoutService::treeForShop($shop->id, $year, $month);
        $isPaid = ShopMonthlyPayout::where('year', $year)->where('month', $month)->exists();

        return view('shop.payout.index', compact(
            'shop',
            'payouts',
            'months',
            'year',
            'month',
            'currentTree',
            'lifetimeTotal',
            'lifetimePersonal',
            'lifetimeGroup',
            'isPaid'
        ));
    }

    /**
     * Display shop owner's downline network tree.
     */
    public function network(): View
    {
        $user = auth()->user();
        $shop = $user->shop ?? Shop::where('user_id', $user->id)->first();

        [$year, $month] = $this->resolveMonth(request('year'), request('month'));
        $months = collect(range(1, 12));
        $isPaid = ShopMonthlyPayout::where('year', $year)->where('month', $month)->exists();

        $rootNode = $shop ? PayoutService::treeForShop($shop->id, $year, $month) : null;
        $nodes = $rootNode ? [$rootNode] : [];

        return view('shop.payout.network', compact('shop', 'nodes', 'months', 'year', 'month', 'isPaid'));
    }

    /**
     * AJAX endpoint to fetch children of a downline shop.
     */
    public function children(Request $request, Shop $shop): JsonResponse
    {
        $user = auth()->user();
        $myShop = $user->shop ?? Shop::where('user_id', $user->id)->first();

        if (! $myShop) {
            return response()->json([], 403);
        }

        [$year, $month] = $this->resolveMonth($request->query('year'), $request->query('month'));

        return response()->json(PayoutService::childrenOf($shop->id, $year, $month));
    }

    /**
     * Download or view monthly payout slip for a shop owner.
     */
    public function slip(ShopMonthlyPayout $payout, Request $request)
    {
        $user = auth()->user();
        $shop = $user->shop ?? Shop::where('user_id', $user->id)->first();

        if (! $shop || $payout->shop_id !== $shop->id) {
            abort(403, 'Unauthorized payout statement access.');
        }

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
     * Show form to add a new downline shop under current shop owner.
     */
    public function createDownline(): View
    {
        $user = auth()->user();
        $shop = $user->shop ?? Shop::where('user_id', $user->id)->firstOrFail();

        return view('shop.payout.create-downline', compact('shop'));
    }

    /**
     * Store a new downline shop directly registered by current shop owner.
     */
    public function storeDownline(ShopCreateRequest $request)
    {
        $user = auth()->user();
        $shop = $user->shop ?? Shop::where('user_id', $user->id)->firstOrFail();

        if (! $shop->canAcceptDirectDownline()) {
            return back()->withInput()->with('error', __(
                'You have reached the maximum limit of :max direct downline shops. Your downline partners can recruit under their own shops to expand your Phase 2 group sales!',
                ['max' => Shop::MAX_DIRECT_DOWNLINES]
            ));
        }

        // Force parent_shop_id to logged-in shop owner
        $request->merge(['parent_shop_id' => $shop->id]);

        $newShop = ShopRepository::storeByRequest($request);

        return to_route('shop.payout.network')
            ->withSuccess(__('Downline shop ":name" (#:id) registered successfully in your downline network!', [
                'name' => $newShop->name,
                'id' => $newShop->id,
            ]));
    }

    /**
     * Resolve target year and month.
     *
     * @return array{0: int, 1: int}
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
