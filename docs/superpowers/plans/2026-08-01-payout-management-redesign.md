# Payout Management Redesign Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Restructure the Payout Management admin area into three submenus — Payout Network (hierarchical tree analysis), Payout History (audit table), and Run Payout (review-then-confirm) — per the approved design spec.

**Architecture:** Add `PayoutService::networkForMonth()` (reconstructs the MLM forest from `shops.parent_shop_id`, snapshot-backed for paid months / live-computed for unpaid), a lazy-expandable tree view with a recursive Blade partial, an AJAX children endpoint, and a GET run-form page with preview.

**Tech Stack:** Laravel 11, Blade + Bootstrap 5 (collapse), FontAwesome, jQuery (already loaded), PHPUnit feature tests.

## Global Constraints

- No new frontend dependencies (no d3/jstree/echarts).
- Reuse existing `PayoutService::phase2()`, `PHASE1_RATE`, and the tier table — no duplication of payout math.
- Money values formatted with `number_format(..., 2)` in views; floats in DB (existing).
- Follow the Warehouse view conventions (`@extends('layouts.app')`, `header-title`/`header-subtitle`/`content` sections, Bootstrap cards).
- Tree = **active shops only** (`Shop::isActive()`), matching `payoutMonth`.
- Routes inside the existing admin group (`routes/web.php` lines ~406-407).

---

### Task 1: Service — `networkForMonth()` + node builder

**Files:**
- Modify: `app/Services/PayoutService.php` (add methods after `payoutMonth()`)

**Interfaces:**
- Consumes: existing `phase2(float, int): array`, `PHASE1_RATE`, `monthBounds()`, `money()` (all already in PayoutService).
- Produces:
  - `networkForMonth(int $year, int $month): array` — forest of root nodes; each node:
    ```php
    ['shop_id' => int, 'shop_name' => string, 'owner_name' => string,
     'level' => int|null, 'personal_sales' => float, 'group_sales' => float,
     'group_size' => int, 'phase1_amount' => float, 'phase2_amount' => float,
     'total_payout' => float, 'has_children' => bool]
    ```
  - `childrenOf(int $shopId, int $year, int $month): array` — direct children of one shop, same node shape (for the AJAX endpoint).

- [ ] **Step 1: Write the failing test**

`tests/Feature/PayoutNetworkTest.php`:

```php
<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Models\Address;
use App\Models\Area;
use App\Models\Coupon;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Shop;
use App\Models\User;
use App\Services\PayoutService;
use Carbon\Carbon;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PayoutNetworkTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);

        // Reference shop (inactive user) so CouponFactory/OrderFactory random-picks work.
        $this->customer = Customer::factory()->create();
        $this->coupon = Coupon::factory()->create(['shop_id' => Shop::create([
            'name' => 'Ref Shop',
            'user_id' => User::factory()->create(['is_active' => false])->id,
        ])->id]);
        $this->area = Area::factory()->create();
        $this->address = Address::create([
            'customer_id' => $this->customer->id,
            'area_id' => $this->area->id,
            'address_type' => 'Home', 'name' => 'Addr', 'phone' => '1',
        ]);
    }

    private function shop(?Shop $parent = null): Shop
    {
        return Shop::create([
            'name' => 'Shop '.fake()->unique()->word(),
            'user_id' => User::factory()->create(['is_active' => true])->id,
            'parent_shop_id' => $parent?->id,
        ]);
    }

    private function deliveredOrder(Shop $shop, float $amount, int $year, int $month): Order
    {
        $at = Carbon::create($year, $month, 15, 12, 0, 0);
        return Order::factory()->create([
            'shop_id' => $shop->id,
            'customer_id' => $this->customer->id,
            'coupon_id' => $this->coupon->id,
            'address_id' => $this->address->id,
            'total_amount' => $amount,
            'order_status' => OrderStatus::DELIVERED->value,
            'created_at' => $at, 'updated_at' => $at,
        ]);
    }

    public function test_network_returns_root_nodes_with_children(): void
    {
        $root = $this->shop();
        $child = $this->shop($root);
        $this->deliveredOrder($root, 50000, 2026, 7);

        $nodes = PayoutService::networkForMonth(2026, 7);

        $this->assertCount(1, $nodes); // only the root (ref shop is inactive)
        $this->assertSame($root->id, $nodes[0]['shop_id']);
        $this->assertTrue($nodes[0]['has_children']);
        $this->assertSame(50000.0, $nodes[0]['personal_sales']);
        $this->assertSame(5000.0, $nodes[0]['phase1_amount']);
        $this->assertNull($nodes[0]['level']); // group size 2 < 10
    }

    public function test_children_of_returns_direct_children(): void
    {
        $root = $this->shop();
        $child = $this->shop($root);
        $this->deliveredOrder($root, 50000, 2026, 7);

        $nodes = PayoutService::childrenOf($root->id, 2026, 7);

        $this->assertCount(1, $nodes);
        $this->assertSame($child->id, $nodes[0]['shop_id']);
        $this->assertFalse($nodes[0]['has_children']);
    }

    public function test_paid_month_uses_snapshot_values(): void
    {
        $root = $this->shop();
        $this->deliveredOrder($root, 50000, 2026, 7);
        PayoutService::payoutMonth(2026, 7);

        $nodes = PayoutService::networkForMonth(2026, 7);

        $this->assertSame(5000.0, $nodes[0]['total_payout']);
        // Snapshot values win even though sales are still in the DB:
        $this->assertSame(5000.0, $nodes[0]['phase1_amount']);
    }

    public function test_unpaid_month_computes_preview(): void
    {
        $root = $this->shop();
        $this->deliveredOrder($root, 50000, 2026, 7);

        $nodes = PayoutService::networkForMonth(2026, 7);

        $this->assertSame(5000.0, $nodes[0]['phase1_amount']);
        $this->assertSame(0, $nodes[0]['phase2_amount']);
    }
}
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --filter=Network`
Expected: FAIL — `Call to undefined method PayoutService::networkForMonth()`

- [ ] **Step 3: Implement `networkForMonth()` + `childrenOf()` in PayoutService**

Add after `payoutMonth()`:

```php
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

    $roots = $byParent[null] ?? collect();

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
```

Add the shared data + node builder (private static):

```php
/**
 * @return array{0: \Illuminate\Support\Collection<int, Shop>, 1: \Illuminate\Support\Collection<int, \Illuminate\Support\Collection<int, Shop>>, 2: \Illuminate\Support\Collection, 3: \Illuminate\Support\Collection}
 */
private static function networkData(int $year, int $month): array
{
    [$start, $end] = self::monthBounds($year, $month);

    $shops = Shop::isActive()->select(['id', 'user_id', 'parent_shop_id'])->get()->keyBy('id');
    $orders = Order::whereBetween('created_at', [$start, $end])
        ->where('order_status', OrderStatus::DELIVERED->value)
        ->selectRaw('shop_id, SUM(total_amount) as sales')
        ->groupBy('shop_id')
        ->pluck('sales', 'shop_id');

    $byParent = $shops->groupBy('parent_shop_id');
    $snapshots = ShopMonthlyPayout::where('year', $year)->where('month', $month)
        ->get()->keyBy('shop_id');

    return [$shops, $byParent, $orders, $snapshots];
}

/**
 * Build one node (values snapshot-backed for paid months, computed otherwise).
 *
 * @param  \Illuminate\Support\Collection<int, Shop>  $byParent
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
        'phase1_amount' => $phase1,
        'phase2_amount' => $phase2,
        'total_payout' => $total,
        'has_children' => isset($byParent[$shop->id]) && $byParent[$shop->id]->isNotEmpty(),
    ];
}

/**
 * Iterative DFS over a shop's descendants (excludes the shop itself).
 *
 * @param  \Illuminate\Support\Collection<int, Shop>  $byParent
 * @return \Illuminate\Support\Collection<int, Shop>
 */
private static function descendants(int $shopId, $byParent): \Illuminate\Support\Collection
{
    $result = collect();
    $stack = $byParent[$shopId] ?? collect();
    while ($stack->isNotEmpty()) {
        $node = $stack->pop();
        $result->push($node);
        foreach (($byParent[$node->id] ?? collect()) as $child) {
            $stack->push($child);
        }
    }

    return $result;
}
```

Note: `networkData` queries `ShopMonthlyPayout` — already imported in PayoutService. The `Shop` model's `user` relation is lazy-loaded per node (fine at this scale).

- [ ] **Step 4: Run test to verify it passes**

Run: `php artisan test --filter=Network`
Expected: PASS

- [ ] **Step 5: Commit**

```bash
git add app/Services/PayoutService.php tests/Feature/PayoutNetworkTest.php
git commit -m "feat: add PayoutService networkForMonth/childrenOf tree builder"
```

---

### Task 2: Routes + Controller methods

**Files:**
- Modify: `routes/web.php` (after line 407)
- Modify: `app/Http/Controllers/Admin/PayoutController.php`

**Interfaces:**
- Consumes: `PayoutService::networkForMonth(int, int): array`, `PayoutService::childrenOf(int, int, int): array`
- Produces:
  - `GET admin/payout/network` → `admin.payout.network`
  - `GET admin/payout/network/children/{shop}` → `admin.payout.network.children`
  - `GET admin/payout/run` → `admin.payout.run.form`
  - Controller methods: `network(): View`, `children(Request, Shop): JsonResponse`, `runForm(): View`

- [ ] **Step 1: Add routes**

In `routes/web.php`, after line 407 (`payout/run` POST), inside the admin group:

```php
Route::get('payout/network', [PayoutController::class, 'network'])->name('payout.network');
Route::get('payout/network/children/{shop}', [PayoutController::class, 'children'])->name('payout.network.children');
Route::get('payout/run', [PayoutController::class, 'runForm'])->name('payout.run.form');
```

- [ ] **Step 2: Write failing controller tests**

Add to `tests/Feature/PayoutNetworkTest.php`:

```php
public function test_network_route_renders_tree_page(): void
{
    $root = $this->shop();
    $this->deliveredOrder($root, 50000, 2026, 7);

    $response = $this->get(route('admin.payout.network', ['year' => 2026, 'month' => 7]));
    $response->assertOk();
    $response->assertSee('Payout Network');
}

public function test_children_route_returns_json(): void
{
    $root = $this->shop();
    $child = $this->shop($root);

    $response = $this->getJson(route('admin.payout.network.children', ['shop' => $root->id, 'year' => 2026, 'month' => 7]));
    $response->assertOk();
    $response->assertJsonCount(1);
    $response->assertJsonPath('0.shop_id', $child->id);
}
```

- [ ] **Step 3: Run tests to verify they fail**

Run: `php artisan test --filter=Network`
Expected: FAIL — route not defined

- [ ] **Step 4: Implement controller methods**

Add to `PayoutController.php` (imports: `App\Models\Shop`, `Illuminate\Http\JsonResponse`):

```php
/**
 * Show the MLM network tree for a month.
 */
public function network(): View
{
    [$year, $month] = $this->resolveMonth(request('year'), request('month'));

    $nodes = PayoutService::networkForMonth($year, $month);
    $months = collect(range(1, 12));

    return view('admin.payout.network', compact('nodes', 'months', 'year', 'month'));
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

    return view('admin.payout.run', compact('nodes', 'months', 'year', 'month'));
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
```

- [ ] **Step 5: Run tests to verify they pass**

Run: `php artisan test --filter=Network`
Expected: PASS

- [ ] **Step 6: Commit**

```bash
git add routes/web.php app/Http/Controllers/Admin/PayoutController.php tests/Feature/PayoutNetworkTest.php
git commit -m "feat: add payout network/run-form routes and controller methods"
```

---

### Task 3: Views — network tree + run page + recursive node partial

**Files:**
- Create: `resources/views/admin/payout/network.blade.php`
- Create: `resources/views/admin/payout/run.blade.php`
- Create: `resources/views/admin/payout/_node.blade.php`

**Interfaces:**
- Consumes: `$nodes` (forest array), `$months`, `$year`, `$month` from controller; `route('admin.payout.network.children', ...)`.
- Produces: renderable pages; `_node.blade.php` used recursively by both network and run pages.

- [ ] **Step 1: Create `_node.blade.php` (recursive partial)**

```blade
{{-- Node: render one shop in the tree. $node: array shape from networkForMonth(). --}}
<li class="payout-node" data-shop-id="{{ $node['shop_id'] }}"
    data-children-url="{{ $node['has_children'] ? route('admin.payout.network.children', ['shop' => $node['shop_id']]) . '?year=' . $year . '&month=' . $month : '' }}">
    <div class="d-flex align-items-center gap-2 py-2 payout-node-row">
        @if($node['has_children'])
            <button type="button" class="btn btn-sm btn-link p-0 payout-expand"
                data-bs-toggle="collapse" data-bs-target="#node-{{ $node['shop_id'] }}"
                aria-expanded="false">
                <i class="fas fa-chevron-right payout-chevron"></i>
            </button>
        @else
            <span class="d-inline-block" style="width:24px;"></span>
        @endif
        <div class="d-flex align-items-center gap-2 flex-wrap flex-grow-1">
            <span class="fw-bold text-dark">{{ $node['shop_name'] }}</span>
            <span class="text-muted small">{{ $node['owner_name'] }}</span>
            @if($node['level'] !== null)
                <span class="badge bg-success-subtle text-success rounded-pill">L{{ $node['level'] }}</span>
            @else
                <span class="badge bg-light text-secondary border rounded-pill">—</span>
            @endif
        </div>
        <div class="d-flex align-items-center gap-3 text-nowrap small">
            <span class="text-muted">{{ __('Personal') }} <span class="fw-semibold text-dark">{{ number_format($node['personal_sales'], 2) }}</span></span>
            <span class="text-muted">{{ __('Group') }} <span class="fw-semibold text-dark">{{ number_format($node['group_sales'], 2) }}</span></span>
            <span class="text-muted">{{ __('Size') }} <span class="fw-semibold text-dark">{{ $node['group_size'] }}</span></span>
            <span class="text-muted">{{ __('Ph1') }} <span class="fw-semibold">{{ number_format($node['phase1_amount'], 2) }}</span></span>
            <span class="text-muted">{{ __('Ph2') }} <span class="fw-semibold">{{ number_format($node['phase2_amount'], 2) }}</span></span>
            <span class="fw-bold text-success">{{ number_format($node['total_payout'], 2) }}</span>
        </div>
    </div>
    @if($node['has_children'])
        <ul class="list-unstyled ms-4 collapse payout-children" id="node-{{ $node['shop_id'] }}">
            @foreach($node['children'] ?? [] as $child)
                @include('admin.payout._node', ['node' => $child, 'year' => $year, 'month' => $month])
            @endforeach
        </ul>
    @endif
</li>
```

Note: `children` key is only present when the partial is rendered server-side with full children (network page depth-1). The AJAX path appends rendered children fetched as JSON → the partial renders them with `children` empty.

- [ ] **Step 2: Create `network.blade.php`**

```blade
@extends('layouts.app')

@section('header-title', __('Payout Management'))
@section('header-subtitle', __('Analyse the MLM network payouts hierarchically.'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h1 class="h3 mb-0 text-gray-800 fw-bold">{{ __('Payout Network') }}</h1>
        <p class="text-muted small mb-0">{{ __('Expand nodes to explore downline payouts.') }}</p>
    </div>
    <div class="d-flex gap-2">
        @hasPermission('admin.payout.run')
            <a href="{{ route('admin.payout.run.form', ['year' => $year, 'month' => $month]) }}" class="btn btn-primary shadow-sm">
                <i class="fas fa-play me-1"></i> {{ __('Run Payout') }}
            </a>
        @endhasPermission
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4">{!! nl2br(e(session('error'))) !!}</div>
@endif

{{-- Month / Year Filter --}}
<div class="card border-0 shadow-sm rounded-12 mb-4">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('admin.payout.network') }}" class="row g-2 align-items-end">
            <div class="col-auto">
                <label class="form-label small text-muted mb-1">{{ __('Month') }}</label>
                <select name="month" class="form-select form-select-sm">
                    @foreach($months as $m)
                        <option value="{{ $m }}" @selected((string) $month === (string) $m)>{{ $m }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto">
                <label class="form-label small text-muted mb-1">{{ __('Year') }}</label>
                <select name="year" class="form-select form-select-sm">
                    @for($y = now()->year; $y >= 2024; $y--)
                        <option value="{{ $y }}" @selected((string) $year === (string) $y)>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-outline-primary btn-sm shadow-sm"><i class="fas fa-filter me-1"></i> {{ __('Apply') }}</button>
                <a href="{{ route('admin.payout.network') }}" class="btn btn-outline-secondary btn-sm shadow-sm">{{ __('Reset') }}</a>
            </div>
        </form>
    </div>
</div>

{{-- Tree --}}
<div class="card border-0 shadow-sm rounded-12">
    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0 fw-bold">{{ __('Network for') }} {{ sprintf('%04d-%02d', $year, $month) }}</h5>
        <span class="badge bg-light text-dark border">{{ count($nodes) }} {{ __('roots') }}</span>
    </div>
    <div class="card-body p-3">
        @forelse($nodes as $node)
            <ul class="list-unstyled mb-0">
                @include('admin.payout._node', ['node' => $node, 'year' => $year, 'month' => $month])
            </ul>
        @empty
            <div class="text-center py-5 text-muted">
                <i class="fas fa-sitemap fs-1 mb-3 d-block text-secondary"></i>
                {{ __('No active shops in the network for this month.') }}
            </div>
        @endforelse
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    // Lazy-load children for collapsed nodes.
    document.addEventListener('click', function (e) {
        var btn = e.target.closest('.payout-expand');
        if (!btn) return;
        var li = btn.closest('.payout-node');
        var target = document.querySelector(btn.getAttribute('data-bs-target'));
        if (!target || target.dataset.loaded) return;

        var url = li.dataset.childrenUrl;
        if (!url) return;

        fetch(url)
            .then(function (r) { return r.json(); })
            .then(function (children) {
                if (!children || !children.length) { target.dataset.loaded = '1'; return; }
                var html = '';
                children.forEach(function (node) {
                    html += renderNode(node, {{ $year }}, {{ $month }});
                });
                target.innerHTML = html;
                target.dataset.loaded = '1';
            })
            .catch(function () {
                target.innerHTML = '<li class="text-danger small py-1">{{ __('Failed to load children.') }}</li>';
            });
    });

    function renderNode(node, year, month) {
        var url = node.has_children
            ? '{{ route('admin.payout.network.children', ['shop' => '__ID__']) }}?year=' + year + '&month=' + month
            : '';
        url = url.replace('__ID__', node.shop_id);
        var chevron = node.has_children
            ? '<button type="button" class="btn btn-sm btn-link p-0 payout-expand" data-bs-toggle="collapse" data-bs-target="#node-' + node.shop_id + '" aria-expanded="false"><i class="fas fa-chevron-right payout-chevron"></i></button>'
            : '<span class="d-inline-block" style="width:24px;"></span>';
        var level = node.level !== null
            ? '<span class="badge bg-success-subtle text-success rounded-pill">L' + node.level + '</span>'
            : '<span class="badge bg-light text-secondary border rounded-pill">—</span>';
        var children = node.has_children
            ? '<ul class="list-unstyled ms-4 collapse payout-children" id="node-' + node.shop_id + '"></ul>'
            : '';
        return '<li class="payout-node" data-shop-id="' + node.shop_id + '" data-children-url="' + url + '">'
            + '<div class="d-flex align-items-center gap-2 py-2 payout-node-row">' + chevron
            + '<div class="d-flex align-items-center gap-2 flex-wrap flex-grow-1">'
            + '<span class="fw-bold text-dark">' + esc(node.shop_name) + '</span>'
            + '<span class="text-muted small">' + esc(node.owner_name) + '</span>' + level + '</div>'
            + '<div class="d-flex align-items-center gap-3 text-nowrap small">'
            + '<span class="text-muted">{{ __('Personal') }} <span class="fw-semibold text-dark">' + fmt(node.personal_sales) + '</span></span>'
            + '<span class="text-muted">{{ __('Group') }} <span class="fw-semibold text-dark">' + fmt(node.group_sales) + '</span></span>'
            + '<span class="text-muted">{{ __('Size') }} <span class="fw-semibold text-dark">' + node.group_size + '</span></span>'
            + '<span class="text-muted">{{ __('Ph1') }} <span class="fw-semibold">' + fmt(node.phase1_amount) + '</span></span>'
            + '<span class="text-muted">{{ __('Ph2') }} <span class="fw-semibold">' + fmt(node.phase2_amount) + '</span></span>'
            + '<span class="fw-bold text-success">' + fmt(node.total_payout) + '</span></div></div>' + children + '</li>';
    }
    function fmt(v) { return Number(v).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }); }
    function esc(s) { var d = document.createElement('div'); d.textContent = s || ''; return d.innerHTML; }
})();
</script>
@endpush
```

- [ ] **Step 3: Create `run.blade.php`** — form + read-only preview tree

```blade
@extends('layouts.app')

@section('header-title', __('Payout Management'))
@section('header-subtitle', __('Review the month, then confirm the payout run.'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h1 class="h3 mb-0 text-gray-800 fw-bold">{{ __('Run Payout') }}</h1>
        <p class="text-muted small mb-0">{{ __('Select a month, review the preview, then run.') }}</p>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4">{!! nl2br(e(session('error'))) !!}</div>
@endif

{{-- Month / Year + Run --}}
<div class="card border-0 shadow-sm rounded-12 mb-4">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('admin.payout.run.form') }}" class="row g-2 align-items-end">
            <div class="col-auto">
                <label class="form-label small text-muted mb-1">{{ __('Month') }}</label>
                <select name="month" class="form-select form-select-sm">
                    @foreach($months as $m)
                        <option value="{{ $m }}" @selected((string) $month === (string) $m)>{{ $m }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto">
                <label class="form-label small text-muted mb-1">{{ __('Year') }}</label>
                <select name="year" class="form-select form-select-sm">
                    @for($y = now()->year; $y >= 2024; $y--)
                        <option value="{{ $y }}" @selected((string) $year === (string) $y)>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-outline-primary btn-sm shadow-sm"><i class="fas fa-filter me-1"></i> {{ __('Preview') }}</button>
            </div>
        </form>
    </div>
</div>

{{-- Confirmation + Preview --}}
<div class="card border-0 shadow-sm rounded-12 mb-4">
    <div class="card-body py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <div class="fw-bold">{{ __('Confirm run for') }} {{ sprintf('%04d-%02d', $year, $month) }}</div>
            <div class="text-muted small">{{ __('Credits each active shop. Already-paid shops are skipped.') }}</div>
        </div>
        <form method="POST" action="{{ route('admin.payout.run') }}" class="d-inline" onsubmit="return confirm('{{ __('Run the payout for this month?') }}')">
            @csrf
            <input type="hidden" name="month" value="{{ $month }}">
            <input type="hidden" name="year" value="{{ $year }}">
            <button type="submit" class="btn btn-primary shadow-sm"><i class="fas fa-play me-1"></i> {{ __('Run Payout') }}</button>
        </form>
    </div>
</div>

{{-- Preview tree (read-only) --}}
<div class="card border-0 shadow-sm rounded-12">
    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0 fw-bold">{{ __('Preview for') }} {{ sprintf('%04d-%02d', $year, $month) }}</h5>
        <span class="badge bg-light text-dark border">{{ count($nodes) }} {{ __('roots') }}</span>
    </div>
    <div class="card-body p-3">
        @forelse($nodes as $node)
            <ul class="list-unstyled mb-0">
                @include('admin.payout._node', ['node' => $node, 'year' => $year, 'month' => $month])
            </ul>
        @empty
            <div class="text-center py-5 text-muted">
                <i class="fas fa-sitemap fs-1 mb-3 d-block text-secondary"></i>
                {{ __('No active shops for this month.') }}
            </div>
        @endforelse
    </div>
</div>
@endsection
```

Note: The run page renders the same `_node` partial — but it must NOT lazy-load (read-only preview). Since the run page doesn't include the `@push('scripts')` block, the collapse toggles simply won't load children (no fetch handler) — the preview shows depth-1 only, which matches the "review" intent.

- [ ] **Step 4: Run tests to verify the routes render**

Run: `php artisan test --filter=Network`
Expected: PASS (network + children route tests)

Also manually confirm the run page renders:

Run: `php artisan route:list --path=payout`
Expected: 5 routes listed (index, run, network, network.children, run.form)

- [ ] **Step 5: Commit**

```bash
git add resources/views/admin/payout/
git commit -m "feat: add payout network tree and run preview views"
```

---

### Task 4: Sidebar — three submenus

**Files:**
- Modify: `resources/views/layouts/partials/admin-menu.blade.php:220-249`

**Interfaces:**
- Consumes: routes `admin.payout.network`, `admin.payout.index`, `admin.payout.run.form`
- Produces: sidebar with Network / History / Run submenus; Run now links to a real page (not the modal).

- [ ] **Step 1: Replace the payout sidebar block**

Replace lines 220-249 (the `@hasPermission(['admin.payout.index', 'admin.payout.run'])` block) with:

```blade
@hasPermission(['admin.payout.index', 'admin.payout.network', 'admin.payout.run'])
    <!--- Payout Management --->
    <li>
        <a class="menu {{ request()->routeIs('admin.payout.*') ? 'active' : '' }}"
            data-bs-toggle="collapse" href="#payoutMenu">
            <span>
                <img class="menu-icon" src="{{ asset('assets/icons-admin/payout.svg') }}" alt="icon" loading="lazy" />
                {{ __('Payout Management') }}
            </span>
            <img src="{{ asset('assets/icons-admin/caret-down.svg') }}" alt="icon" class="downIcon">
        </a>
        <div class="collapse dropdownMenuCollapse {{ $request->routeIs('admin.payout.*') ? 'show' : '' }}"
            id="payoutMenu">
            <div class="listBar">
                @hasPermission('admin.payout.network')
                    <a href="{{ route('admin.payout.network') }}"
                        class="subMenu hasCount {{ request()->routeIs('admin.payout.network') ? 'active' : '' }}">
                        {{ __('Payout Network') }}
                    </a>
                @endhasPermission
                @hasPermission('admin.payout.index')
                    <a href="{{ route('admin.payout.index') }}"
                        class="subMenu hasCount {{ request()->routeIs('admin.payout.index') ? 'active' : '' }}">
                        {{ __('Payout History') }}
                    </a>
                @endhasPermission
                @hasPermission('admin.payout.run')
                    <a href="{{ route('admin.payout.run.form') }}"
                        class="subMenu hasCount {{ request()->routeIs('admin.payout.run*') ? 'active' : '' }}">
                        {{ __('Run Payout') }}
                    </a>
                @endhasPermission
            </div>
        </div>
    </li>
@endhasPermission
```

- [ ] **Step 2: Verify**

Run: `php artisan view:cache` — no Blade compile errors.

- [ ] **Step 3: Commit**

```bash
git add resources/views/layouts/partials/admin-menu.blade.php
git commit -m "feat: restructure payout sidebar into network/history/run submenus"
```

---

### Task 5: Cleanup — remove the modal from history page, wire run flash redirects

**Files:**
- Modify: `resources/views/admin/payout/index.blade.php` (remove the Run Payout modal + button; keep the table)
- Modify: `app/Http/Controllers/Admin/PayoutController.php` (redirect `run()` to network after success)

**Interfaces:**
- Consumes: `admin.payout.run.form` (new page replaces the modal).
- Produces: History page = table only; run redirects to the network page (which now shows fresh data).

- [ ] **Step 1: Remove the Run Payout modal + button from index.blade.php**

Delete the `@hasPermission('admin.payout.run')` header button block (lines ~12-18) and the entire Run Payout Modal block (lines ~137-179). Keep the filter + table + pagination. The history page becomes a pure audit table.

- [ ] **Step 2: Update `run()` redirects**

In `PayoutController::run()`, change both `redirect()->route('admin.payout.index')` to `redirect()->route('admin.payout.network', ['year' => $data['year'], 'month' => $data['month']])` so the user lands on the network page showing the just-processed month.

- [ ] **Step 3: Verify**

Run: `php artisan test` — full suite green (history page still renders via `admin.payout.index`; no test asserted the modal).

- [ ] **Step 4: Commit**

```bash
git add resources/views/admin/payout/index.blade.php app/Http/Controllers/Admin/PayoutController.php
git commit -m "refactor: run redirects to network page; history page is table-only"
```

---

## Self-Review

- **Spec coverage**: Network tree (Task 1-3) ✓, History preserved (Task 5) ✓, Run review-then-confirm (Task 2-3) ✓, sidebar 3 submenus (Task 4) ✓, no new deps (constraints) ✓, paid/unpaid snapshot-vs-preview (Task 1) ✓, lazy expansion (Task 3 JS) ✓.
- **Placeholders**: all steps contain concrete code; no TBD/TODO.
- **Type consistency**: `networkForMonth(int,int): array`, `childrenOf(int,int,int): array`, node shape consistent across Tasks 1-3 (`shop_id`, `shop_name`, `owner_name`, `level`, `personal_sales`, `group_sales`, `group_size`, `phase1_amount`, `phase2_amount`, `total_payout`, `has_children`).

## Execution Handoff

**Plan complete and saved to `docs/superpowers/plans/2026-08-01-payout-management-redesign.md`.** Two execution options:

1. **Subagent-Driven (recommended)** — I dispatch a fresh subagent per task, review between tasks, fast iteration.
2. **Inline Execution** — I execute tasks in this session using executing-plans, batch execution with checkpoints.

Which approach?
