<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\Shop;
use App\Models\ShopMonthlyPayout;
use App\Models\User;
use App\Models\Wallet;
use App\Repositories\TransactionRepository;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PayoutService
{
    public const PURPOSE = 'mlm_payout';

    public const PHASE1_RATE = 0.10;

    public const DEACTIVATION_DAYS = 90;

    /**
     * MLM group-sales tiers, evaluated in order (lowest level first).
     * [level, min_sales, max_sales|null, min_group_size, rate, cap|null]
     * rate >= 1 means a flat amount; rate < 1 means a fraction of group sales.
     * Each non-final tier has an exclusive max so higher tiers stay reachable
     * (the first matching row would otherwise always win).
     */
    private const TIERS = [
        [0, 33000, 75000, 10, 3000, null],
        [1, 75000, 300000, 10, 0.04, null],
        [2, 300000, 3000000, 100, 0.01, null],
        [3, 3000000, 30000000, 10000, 0.002, null],
        [4, 30000000, null, 100000, 0.0004, 150000],
    ];

    /**
     * Resolve the phase-2 tier for a group.
     *
     * @return array{0: int|null, 1: float} [level|null, amount]
     */
    public static function phase2(float $groupSales, int $groupSize): array
    {
        foreach (self::TIERS as [$level, $min, $max, $sizeMin, $rate, $cap]) {
            if ($groupSales < $min || ($max !== null && $groupSales >= $max)) {
                continue;
            }
            if ($groupSize < $sizeMin) {
                continue;
            }
            $amount = $rate < 1 ? $groupSales * $rate : $rate;
            if ($cap !== null) {
                $amount = min($amount, $cap);
            }

            return [$level, self::money($amount)];
        }

        return [null, 0.0];
    }

    /**
     * Personal sales of one shop in a month (Delivered orders only).
     */
    public static function personalSales(Shop $shop, int $year, int $month): float
    {
        [$start, $end] = self::monthBounds($year, $month);

        return self::money((float) $shop->orders()
            ->where('order_status', OrderStatus::DELIVERED->value)
            ->whereBetween('created_at', [$start, $end])
            ->sum('total_amount'));
    }

    /**
     * Pure calculation for a single shop (no descendants are resolvable here,
     * so group sales equal personal sales). Returns the snapshot-shaped array.
     *
     * @return array<string, mixed>
     */
    public static function calculateForMonth(Shop $shop, int $year, int $month): array
    {
        $personal = self::personalSales($shop, $year, $month);
        [$level, $phase2] = self::phase2($personal, 1);
        $phase1 = self::money($personal * self::PHASE1_RATE);

        return [
            'personal_sales' => $personal,
            'group_sales' => $personal,
            'group_size' => 1,
            'level' => $level,
            'phase1_amount' => $phase1,
            'phase2_amount' => $phase2,
            'total_payout' => self::money($phase1 + $phase2),
        ];
    }

    /**
     * Run the monthly payout for all active shops.
     *
     * Skips shops that already have a snapshot for (year, month). Each shop's
     * snapshot and wallet credit happen atomically in one DB transaction; a
     * failure rolls back both. Returns processing counts.
     *
     * @return array{processed: int, credited: int, skipped: int, errors: array<int, array{shop_id: int, message: string}>}
     */
    public static function payoutMonth(int $year, int $month): array
    {
        [$start, $end] = self::monthBounds($year, $month);

        // Tree of active shops only (deactivated users are excluded from the
        // network and from payouts; their children were re-parented to roots).
        $shops = Shop::isActive()->select(['id', 'user_id', 'parent_shop_id'])->get()->keyBy('id');
        $orders = Order::whereBetween('created_at', [$start, $end])
            ->where('order_status', OrderStatus::DELIVERED->value)
            ->selectRaw('shop_id, SUM(total_amount) as sales')
            ->groupBy('shop_id')
            ->pluck('sales', 'shop_id');

        $byParent = $shops->groupBy('parent_shop_id');
        $children = fn (int $id) => $byParent[$id] ?? collect();

        $processed = 0;
        $credited = 0;
        $skipped = 0;
        $errors = [];

        foreach ($shops as $shop) {
            // Skip before computing figures: cheap and keeps re-runs a no-op.
            if (ShopMonthlyPayout::where('shop_id', $shop->id)->where('year', $year)->where('month', $month)->exists()) {
                $skipped++;

                continue;
            }

            $personal = self::money((float) ($orders[$shop->id] ?? 0.0));
            $groupSales = $personal;
            $groupSize = 1;

            // Iterative depth-first walk over the subtree (no recursion).
            $stack = $children($shop->id);
            while ($stack->isNotEmpty()) {
                $node = $stack->pop();
                $groupSales = self::money($groupSales + (float) ($orders[$node->id] ?? 0.0));
                $groupSize++;
                foreach ($children($node->id) as $child) {
                    $stack->push($child);
                }
            }

            [$level, $phase2] = self::phase2($groupSales, $groupSize);
            $phase1 = self::money($personal * self::PHASE1_RATE);
            $total = self::money($phase1 + $phase2);

            try {
                DB::transaction(function () use ($shop, $year, $month, $personal, $groupSales, $groupSize, $level, $phase1, $phase2, $total, &$credited) {
                    ShopMonthlyPayout::create([
                        'shop_id' => $shop->id,
                        'year' => $year,
                        'month' => $month,
                        'personal_sales' => $personal,
                        'group_sales' => $groupSales,
                        'group_size' => $groupSize,
                        'level' => $level,
                        'phase1_amount' => $phase1,
                        'phase2_amount' => $phase2,
                        'total_payout' => $total,
                    ]);

                    if ($total > 0) {
                        $wallet = Wallet::firstOrCreate(['user_id' => $shop->user_id], ['balance' => 0]);
                        TransactionRepository::storeByRequest(
                            $wallet,
                            $total,
                            'credit',
                            false, // hasAdminUpdate — must stay false: true credits every admin wallet
                            false, // isCommission — must stay false: admin dashboard sums is_commission=true
                            self::PURPOSE,
                            "Monthly MLM payout for {$year}-{$month} (level ".($level ?? '-').')'
                        );
                        $credited++;
                    }
                });
                $processed++;
            } catch (\Throwable $e) {
                Log::error("mlm payout failed for shop {$shop->id} ({$year}-{$month}): {$e->getMessage()}");
                $errors[] = ['shop_id' => $shop->id, 'message' => $e->getMessage()];
            }
        }

        return compact('processed', 'credited', 'skipped', 'errors');
    }

    /**
     * Build the MLM forest for a month as nested node arrays.
     *
     * Paid months pull values from the shop_monthly_payouts snapshot
     * (authoritative); unpaid months compute a live preview from the current
     * tree and Delivered-order sales. Only active shops appear.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function networkForMonth(int $year, int $month): array
    {
        [$shops, $byParent, $orders, $snapshots] = self::networkData($year, $month);

        $roots = $byParent['root'] ?? collect();

        return $roots->map(fn (Shop $shop) => self::node(
            $shop, $byParent, $orders, $snapshots, $year, $month
        ))->values()->all();
    }

    /**
     * Direct children of one shop (for lazy tree expansion).
     *
     * @return array<int, array<string, mixed>>
     */
    public static function childrenOf(int $shopId, int $year, int $month): array
    {
        [$shops, $byParent, $orders, $snapshots] = self::networkData($year, $month);

        if (! isset($shops[$shopId])) {
            return [];
        }

        return ($byParent[$shopId] ?? collect())
            ->map(fn (Shop $shop) => self::node(
                $shop, $byParent, $orders, $snapshots, $year, $month
            ))->values()->all();
    }

    /**
     * Build the network tree rooted at a specific shop (for shop owner dashboard).
     *
     * @return array<string, mixed>|null
     */
    public static function treeForShop(int $shopId, int $year, int $month): ?array
    {
        [$shops, $byParent, $orders, $snapshots] = self::networkData($year, $month);

        $shop = $shops[$shopId] ?? Shop::find($shopId);
        if ($shop === null) {
            return null;
        }

        return self::node($shop, $byParent, $orders, $snapshots, $year, $month);
    }

    /**
     * Shared tree + sales + snapshot data for the network views.
     *
     * @return array{0: Collection<int, Shop>, 1: Collection, 2: Collection, 3: Collection}
     */
    private static function networkData(int $year, int $month): array
    {
        [$start, $end] = self::monthBounds($year, $month);

        $shops = Shop::isActive()->select(['id', 'user_id', 'parent_shop_id', 'name'])->get()->keyBy('id');
        $orders = Order::whereBetween('created_at', [$start, $end])
            ->where('order_status', OrderStatus::DELIVERED->value)
            ->selectRaw('shop_id, SUM(total_amount) as sales')
            ->groupBy('shop_id')
            ->pluck('sales', 'shop_id');

        // groupBy normalizes a null parent key to '' — use a 'root' sentinel so
        // roots and children lookups share the same key type.
        $byParent = $shops->groupBy(fn (Shop $s) => $s->parent_shop_id ?? 'root');
        $snapshots = ShopMonthlyPayout::where('year', $year)->where('month', $month)
            ->get()->keyBy('shop_id');

        return [$shops, $byParent, $orders, $snapshots];
    }

    /**
     * Build one node (values snapshot-backed for paid months, computed otherwise).
     *
     * @param  Collection  $byParent
     * @param  Collection  $orders
     * @param  Collection  $snapshots
     * @return array<string, mixed>
     */
    private static function node(
        Shop $shop,
        $byParent,
        $orders,
        $snapshots,
        int $year,
        int $month
    ): array {
        $snapshot = $snapshots[$shop->id] ?? null;
        $directChildrenCount = isset($byParent[$shop->id]) ? $byParent[$shop->id]->count() : 0;
        $downlineCount = self::descendants($shop->id, $byParent)->count();

        if ($snapshot !== null) {
            $personal = (float) $snapshot->personal_sales;
            $groupSales = (float) $snapshot->group_sales;
            $groupSize = (int) $snapshot->group_size;
            $level = $snapshot->level;
            $phase1 = (float) $snapshot->phase1_amount;
            $phase2 = (float) $snapshot->phase2_amount;
            $total = (float) $snapshot->total_payout;
        } else {
            $personal = self::money((float) ($orders[$shop->id] ?? 0.0));
            $groupSales = $personal;
            $groupSize = 1;
            foreach (self::descendants($shop->id, $byParent) as $desc) {
                $groupSales = self::money($groupSales + (float) ($orders[$desc->id] ?? 0.0));
                $groupSize++;
            }
            [$level, $phase2] = self::phase2($groupSales, $groupSize);
            $phase1 = self::money($personal * self::PHASE1_RATE);
            $total = self::money($phase1 + $phase2);
        }

        return [
            'shop_id' => $shop->id,
            'shop_name' => $shop->name,
            'owner_name' => $shop->user->name ?? '',
            'level' => $level,
            'personal_sales' => $personal,
            'group_sales' => $groupSales,
            'group_size' => $groupSize,
            'downline_count' => $downlineCount,
            'direct_children_count' => $directChildrenCount,
            'phase1_amount' => $phase1,
            'phase2_amount' => $phase2,
            'total_payout' => $total,
            'has_children' => $directChildrenCount > 0,
        ];
    }

    /**
     * Iterative DFS over a shop's descendants (excludes the shop itself).
     *
     * @param  Collection  $byParent
     */
    private static function descendants(int $shopId, $byParent): Collection
    {
        $result = collect();
        // Copy with values() so pop() does not mutate the shared $byParent map.
        $stack = ($byParent[$shopId] ?? collect())->values();
        while ($stack->isNotEmpty()) {
            $node = $stack->pop();
            $result->push($node);
            foreach (($byParent[$node->id] ?? collect()) as $child) {
                $stack->push($child);
            }
        }

        return $result;
    }

    /**
     * Deactivate members with no order activity in the last 90 days.
     *
     * The shop's position in the MLM tree is cleared: its children become
     * roots and it detaches from its parent, freeing a slot in full groups.
     * Idempotent: already-deactivated users are never candidates again.
     *
     * @return array{deactivated: int, errors: array<int, array{shop_id: int, message: string}>}
     */
    public static function deactivateInactiveMembers(): array
    {
        $cutoff = now()->subDays(self::DEACTIVATION_DAYS);

        // Exclude root/admins: they own reference shops and are never members
        // of the MLM network, so their inactivity must not deactivate the
        // platform's own accounts.
        $candidates = Shop::select(['id', 'user_id', 'parent_shop_id'])
            ->whereHas('user', function ($query) {
                $query->where('is_active', 1)
                    ->whereDoesntHave('roles', function ($query) {
                        $query->whereIn('name', ['root', 'admin']);
                    });
            })
            ->whereDoesntHave('orders', function ($query) use ($cutoff) {
                $query->where('created_at', '>=', $cutoff);
            })
            ->get();

        $deactivated = 0;
        $errors = [];

        foreach ($candidates as $shop) {
            try {
                DB::transaction(function () use ($shop) {
                    Shop::where('parent_shop_id', $shop->id)->update(['parent_shop_id' => null]);
                    Shop::where('id', $shop->id)->update(['parent_shop_id' => null]);
                    User::where('id', $shop->user_id)->update(['is_active' => 0]);
                });
                $deactivated++;
            } catch (\Throwable $e) {
                Log::error("mlm deactivation failed for shop {$shop->id}: {$e->getMessage()}");
                $errors[] = ['shop_id' => $shop->id, 'message' => $e->getMessage()];
            }
        }

        return compact('deactivated', 'errors');
    }

    /**
     * @return array{0: Carbon, 1: Carbon} inclusive month bounds
     */
    private static function monthBounds(int $year, int $month): array
    {
        $start = Carbon::create($year, $month, 1)->startOfDay();
        $end = (clone $start)->endOfMonth()->endOfDay();

        return [$start, $end];
    }

    private static function money(float $value): float
    {
        return round($value, 2);
    }
}
