<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use App\Models\ShopMonthlyPayout;
use App\Services\PayoutService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

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

        $latest = ShopMonthlyPayout::orderByDesc('year')->orderByDesc('month')->first();
        if ($latest !== null) {
            return [(int) $latest->year, (int) $latest->month];
        }

        $now = now()->subMonthNoOverflow();

        return [$now->year, $now->month];
    }
}
