# Payout Management Redesign — Design Spec

Date: 2026-08-01
Status: Approved (approach 1)

## Context

The current `/admin/payout` page is a flat paginated table of monthly payout snapshots with a Run Payout modal. Shop owners are organized in an MLM tree (`shops.parent_shop_id`), and payouts are computed per month from that tree. The user needs a **hierarchical way to clearly analyse and process payouts** — see the network, review what each member earns, and run the monthly payout with confidence.

The side bar "Payout Management" section will be restructured into **three submenus**: **Payout Network**, **Payout History**, **Run Payout**.

## Goals

- See the MLM network as an expandable tree for any month, with each node showing the shop's payout breakdown (level, personal/group sales, group size, phase 1, phase 2, total).
- For **paid months**, nodes show the authoritative snapshot values (what was actually credited). For **unpaid months**, nodes show a **live preview** recomputed from the current tree and Delivered orders (nothing is written).
- Run the monthly payout as a **review-then-confirm** flow: preview the tree + totals for a month, then confirm the run.
- Keep the payout history as an audit table.
- No new frontend dependencies (Bootstrap 5 + FontAwesome + jQuery, already present).

## Design

### Sidebar (admin-menu.blade.php)

Replace the current two-submenu block (History + Run modal) with three submenus:

- **Payout Network** → `admin.payout.network`
- **Payout History** → `admin.payout.index`
- **Run Payout** → `admin.payout.run.form` (GET form page; POST submits to existing `admin.payout.run`)

Active-state matching: `request()->routeIs('admin.payout.*')` on the parent, `request()->routeIs('admin.payout.network')` / `('admin.payout.index')` / `('admin.payout.run*')` on submenus.

### Routes (routes/web.php, inside admin group)

```php
Route::get('payout/network', [PayoutController::class, 'network'])->name('payout.network');
Route::get('payout/network/children/{shop}', [PayoutController::class, 'children'])->name('payout.network.children'); // AJAX
Route::get('payout/run', [PayoutController::class, 'runForm'])->name('payout.run.form'); // GET preview
// existing: GET payout.index, POST payout.run
```

### Service: PayoutService::networkForMonth() + node shape

New method returning the forest for a month:

```php
public static function networkForMonth(int $year, int $month): array
// returns array of root nodes, each:
// ['shop_id', 'shop_name', 'owner_name', 'level'|null, 'personal_sales', 'group_sales',
//  'group_size', 'phase1_amount', 'phase2_amount', 'total_payout', 'children' => [...]]
```

Behavior:
- Load all **active** shops (`id, user_id, parent_shop_id`) — same tree as `payoutMonth`.
- Load the month's Delivered-order sales grouped by shop (reuse the same query as `payoutMonth`).
- Determine if the month is **paid**: a snapshot exists for any shop in (year, month).
- For each root (parent null), build the subtree **recursively in memory**, computing per node:
  - **Paid month**: pull values from the `ShopMonthlyPayout` snapshot for that shop (authoritative).
  - **Unpaid month**: compute via the same tier logic (`PayoutService::phase2`) + phase 1 rate.
- `children` array contains full subtree (recursion). For the lazy-load endpoint, a variant returns only the direct children of one shop.

This reuses the tier table and `phase2()` already in the service — no duplication of payout math.

### Controller (PayoutController)

- `network()`: GET month/year (defaults: latest month with any snapshot, else previous month) → `PayoutService::networkForMonth()` → view `admin.payout.network`.
- `children(Shop $shop)`: AJAX — returns JSON of the shop's direct children (each with the same node shape) for the requested month/year (from query string). Used for lazy expand.
- `runForm()`: GET — month/year selects + a **preview** (same tree, read-only) + confirm button; POST to existing `admin.payout.run` with the same validation. Redirects to History/Network after run with the existing flash summary.

### Views

- `admin/payout/network.blade.php` — header + month/year filter + tree container. Root nodes render recursively via a **partial** `admin/payout/_node.blade.php` (recursive include for children). Node markup: Bootstrap collapse for expand/collapse, `data-url` for lazy children fetch, level badge, inline sales/payout values.
- `admin/payout/history` — reuse existing `index.blade.php` (unchanged table).
- `admin/payout/run.blade.php` — form + preview tree (read-only) + confirm.

### JS

- Small inline script on the network page: intercept expand toggles → if node has `data-url` and children not yet loaded, fetch children JSON → append rendered partial → mark loaded. No new library.

### Error handling

- Children fetch: on failure, show a small inline error in the node and allow retry.
- Run: existing `payoutMonth` per-shop try/catch; flash success/error summary on redirect.

### Testing

- Unit: `networkForMonth()` — paid month returns snapshot values; unpaid month returns computed preview; recursion depth; root detection; level null for non-qualifying.
- Feature: routes return 200; children endpoint returns JSON with the shop's children; runForm shows preview; run redirects with flash.
- Existing payout tests must stay green (service is additive).

## Out of scope

- Editing the tree (no member add/remove UI this round).
- Per-shop/subtree payout runs (only global monthly run).
- Chart/graph visualization (tree list is sufficient).
