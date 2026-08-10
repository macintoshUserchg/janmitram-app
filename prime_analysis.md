# Deep Analysis — Janmitram App

*Generated: 2026-08-09 · Rechecked: 2026-08-10 (commits `2468392` "Make order placement atomic and stop double-deducting stock", `51a47ac` "Unify reorder with checkout via shared order processor", `166a8ce` "Align flash-sale display reads with order pricing"). Based on full reindexed graph data (codegraph: 1,338 files / 19,655 nodes; codebase-memory MCP: 33,146 nodes / 100,201 edges), source verification, and a live test run. All claims verified against the code on disk, not just docs.*

*Further recheck 2026-08-11:* `87f74ee` (discount-price hardening — order charge
`min()` guard, defensive `getDiscountPercentage`, import clamps) and `b12c180`
(membership **card system** — `cards` table, flat 10%-instead-of-coupon discount at
online/buy-now/POS, admin Cards section, removed the dead `coupon_collects` /
collect-voucher scheme) landed after the table above. Neither fixes a
previously-flagged finding; they add a new discount path and remove a broken one.

---

## Recheck summary (2026-08-10)

The user corrected the code in commits `2468392` (online checkout, reorder, and POS order creation are now transactional; the sale-time `WarehouseService::deductStock` call and its empty catch were removed), `51a47ac` (reorder unified with checkout via a shared order processor — H3), and `166a8ce` (flash-sale display reads aligned with order pricing). Re-verified every finding against the current source:

| Finding | Status after recheck |
|---|---|
| **C1** Order placement not atomic | ✅ **FIXED** — `storeByRequestFromCart`/`reOrder`/`PosCartRepository::storeOrder` now wrap all writes in `DB::transaction`; rollback tests added |
| **C2** Warehouse stock deduction swallowed | ✅ **FIXED (by redesign)** — the empty-catch `deductStock` call was **removed entirely**; sales now draw only from shop inventory, warehouse stock is consumed at stocking time. See note below |
| **C3** Stock debited before payment | ❌ **UNCHANGED** — orders still created `PENDING/PENDING` with stock decremented at placement; no payment-timeout restock |
| **C4** Client-trusted shop allocation | ❌ **UNCHANGED** — `allocations` override still accepted with no eligibility validation |
| **C5** Missing gateway callback routes | ❌ **UNCHANGED** — `paypal.payment.success`, `bkash.payment.execute`, `paytabs.payment.callback` still not registered |
| **C6** Admin RBAC dead config | ❌ **UNCHANGED** — `checkPermission` still not wired into any route; admin panel remains `role:root`-only |
| **H1** N+1 in `ProductResource` | ❌ **UNCHANGED** — per-item `load()` of 8 relations still present |
| **H2** `Schema::hasColumn` per access | ❌ **UNCHANGED** — still in `Order::products()` / `PosCart::products()` |
| **H3** Duplicated `reOrder` logic | ✅ **FIXED** — both checkout and reorder now share `groupLinesByShop` + `createOrderForShop` (commit `51a47ac`); reorder rebuilds cart-like lines incl. size/color ids, so pricing/allocation/stock is one code path |
| **H4** Null-derefs | ⚠️ **PARTIALLY FIXED** — flash-sale null crash in order flow hardened (`$flashSaleProduct?->pivot?->sale_quantity`); **still broken:** `Admin/RiderController::riderLocation` line 142 and `getCartWiseAmounts` lines 446/449 (`->first()->pivot` without `?`) |
| **H5** No order-status transition graph | ❌ **UNCHANGED** — `statusChange` still accepts any status → any status |

> **Note on C2 (design change, not just a bug fix):** removing the sale-time `deductStock` call means **no `order_sale`/`pos_sale` `StockLedger` rows are written at sale time anymore** — sales draw from Shop Copy Product inventory only; warehouse stock is consumed at stocking/dispatch time. `WORKFLOW_PROJECT.md` was updated (2026-08-10) to match: §1 Strict Ledger Auditing, Phase 6 diagram, the `StockLedger` schema row (reference types now `admin_addition`/`warehouse_transfer`/`shop_request`/`manual_adjustment`), and the Phase 6 operational steps all state sales are not ledgered. `WarehouseService::deductStock()` remains as **dead code** (zero callers). **Still stale:** `project_notes.md` (Structural Guidelines §1 and Known Gaps #10) still describe the removed "silently swallowed" caveat — update or retire that file.

---

## 1. What this system is

A **Laravel 11 multi-vendor ecommerce platform** (PHP 8.2, MySQL via MAMP) with four client surfaces:

| Surface | Tech | Where |
|---|---|---|
| Customer storefront | Vue 3 SPA (Pinia, vue-router, vue-i18n, Pusher) | `resources/js/` (38 pages, 76 components) |
| Admin panel | Blade + Bootstrap 5 + Tailwind | `resources/views/admin/` (61 sections) |
| Seller dashboard | Blade + Bootstrap + Tailwind | `resources/views/shop/` (24 sections) |
| Mobile apps (customer/seller/rider) | Flutter/Dart via Sanctum token API | `routes/api.php` |

The architectural centerpiece is **Option A: Strict Warehouse-Only Stock Management** — a central master catalog, physical stock held in warehouses, vendor shops that *request* stock dispatches from their linked warehouse, shop copy-products cloned from masters, and an immutable `StockLedger` audit trail. There is also a real **MLM payout engine** (`PayoutService`) with referral trees, tiered group-sales bonuses, and 90-day deactivation.

**Health check (rechecked):** 110 feature/unit tests pass (398 assertions) — verified by starting MAMP MySQL and running `php artisan test --compact`. Test coverage is genuinely good for warehouse, MLM payouts, shop allocation, KYC flows, and (since commit `2468392`) order-placement rollback behavior.

---

## 2. What is done well

1. **The warehouse architecture is sound in intent.** `WarehouseService` uses `lockForUpdate()` for race-safe stock math, wraps transfers in DB transactions, and logs every movement to `StockLedger`. The `findStock()` color/size fallback is a thoughtful touch.
2. **`PayoutService` is the best-engineered class in the codebase.** Pure calculation functions (`phase2`, `personalSales`), iterative DFS instead of recursion, snapshot-backed months with idempotent re-runs, per-shop transactions, and careful tier exclusivity design.
3. **Discipline in the request layer:** 72 Form Requests, 59 API Resources, typed Eloquent relationships, backed enums for core states, `declare(strict_types=1)` throughout.
4. **Security hygiene is above average:** `mews/purifier` for HTML, CSRF on web, Sanctum tokens, `$guarded` on most models, and notably — the base `Repository` class is an **audited extraction replacing a malicious composer package** (`joynala/maker`), with provenance documented in the file header. That is a serious, well-handled supply-chain incident.
5. **The test suite is real, not decorative** — warehouse sync, MLM math, allocation, import/export all have meaningful coverage, and the new commit added rollback/behavior tests for atomicity and stock semantics.

---

## 3. Findings — current status (rechecked 2026-08-10)

### C1. Order placement was not atomic — ✅ FIXED

**Was:** `OrderRepository::storeByRequestFromCart()` performed ~10 independent writes with no `DB::transaction()` — a mid-way failure left orphaned payments, partially created orders, and already-decremented stock.

**Now:** `storeByRequestFromCart()` is a thin wrapper:

```php
public static function storeByRequestFromCart(OrderRequest $request, $paymentMethod, $carts): Payment
{
    return DB::transaction(function () use ($request, $paymentMethod, $carts) {
        return self::storeByRequestFromCartInTransaction($request, $paymentMethod, $carts);
    });
}
```

The same wrapper pattern was applied to `reOrder()` and `PosCartRepository::storeOrder()`. Any mid-way failure (e.g. out-of-stock `RuntimeException`) rolls back **every** write — verified by the new `test_order_placement_rolls_back_all_writes_on_failure` test (asserts zero Payments/Orders/order_products rows and untouched product quantities after a failure).

**Files:** `app/Repositories/OrderRepository.php`, `app/Repositories/PosCartRepository.php`, `tests/Feature/ShopAllocationTest.php`

### C2. Warehouse stock deduction was silently swallowed — ✅ FIXED (by redesign)

**Was:** an empty `catch (\Throwable $th) {}` around `WarehouseService::deductStock(..., 'order_sale', ...)` meant an `InsufficientStockException` was ignored — the order completed and `products.quantity` was decremented while the warehouse ledger was not, silently drifting warehouse inventory from the catalog.

**Now:** the sale-time `deductStock` call was **removed entirely** (online + POS paths). Sales draw from shop inventory only; warehouse stock is consumed at stocking/dispatch time (per the commit message "stop double-deducting stock"). The new `test_sale_decrements_shop_inventory_not_warehouse` test asserts: shop `products.quantity` decremented once, `warehouse_stock` untouched, and **no `stock_ledgers` row** on sale.

⚠️ **Implication to decide on:** the docs (`WORKFLOW_PROJECT.md` Phase 6, `project_notes.md` §1) still describe sale-time `order_sale`/`pos_sale` ledger entries — that is now stale. Decide whether to (a) accept the new semantics and update the docs, or (b) add a shop-inventory-level ledger to preserve end-to-end auditability.

**Files:** `app/Repositories/OrderRepository.php`, `app/Repositories/PosCartRepository.php`, `tests/Feature/ShopAllocationTest.php`

### C3. Stock debited at placement, before payment completes — ❌ UNCHANGED

Orders are still created `PENDING/PENDING` with **stock already decremented** before any payment gateway callback runs (verified: `decrement` at line 122, `PaymentStatus::PENDING` at line 344 in the same flow). Abandoned or failed payments consume stock permanently — there is a restock on *admin cancel* but no compensating rollback for unpaid/abandoned orders. Reserve-on-placement with no expiry/restore mechanism.

**Files:** `app/Repositories/OrderRepository.php` (`createNewOrder`)

### C4. Client-trusted shop allocation override — ❌ UNCHANGED

```php
$overrides = collect($request->allocations ?? [])->keyBy('product_id');
...
$allocated = self::allocateNearestShop(..., $overrides->get($cart->product_id)?->shop_id);
```

A client can still pin any line to any shop by ID — no server-side check that the shop is radius-eligible or holds stock. `OrderRequest` only validates the shop exists, not eligibility.

**Files:** `app/Repositories/OrderRepository.php` (`allocateNearestShop`, `storeByRequestFromCart`), `app/Http/Requests/OrderRequest.php`

### C5. Three payment gateway callback routes are missing — ❌ UNCHANGED

`PayPal/ProcessController`, `Bkash/ProcessController`, `PayTabs/ProcessController` still call `route('paypal.payment.success'|'bkash.payment.execute'|'paytabs.payment.callback')` — **none are registered** in `routes/web.php` or `api.php` (verified by grep on current source). PayPal capture, bKash execute, and PayTabs callback throw `RouteNotFoundException` at the return-URL step. These gateways remain broken in production.

**Files:** `app/Http/Controllers/Gateway/{PayPal,Bkash,PayTabs}/ProcessController.php`, `routes/*.php`

### C6. Admin RBAC is dead config; panel is root-only — ❌ UNCHANGED

`CheckPermission` middleware + `config/acl.php` still define a full permission tree, but **no route uses them** (verified: zero references in route files). Admin = `['auth','role:root']`, shop = `['authShop']`. The `admin`/`adminMultiShop` roles cannot reach the admin panel at all.

**Files:** `app/Http/Kernel.php`, `config/acl.php`, `routes/web.php`, `routes/api.php`

### H1. Massive N+1 in product serialization (SPA killer) — ❌ UNCHANGED

`ProductResource::toArray()` still begins with a per-item `load()` of 8 relations plus a `favorites()->exists()` query and a nested flash-sale sub-query, while controllers do **not** pre-eager-load. A 20-product listing page fires ~180+ queries. **New finding — not called out in existing docs.**

**Files:** `app/Http/Resources/ProductResource.php`, `app/Http/Controllers/API/{HomeController,ProductController}.php`

### H2. `Schema::hasColumn()` runs on every relation access — ❌ UNCHANGED

`Order::products()` and `PosCart::products()` still call `Schema::hasColumn()` on every call — an `information_schema` round-trip per access, inside loops in order flows. Should be a cached constant check.

**Files:** `app/Models/Order.php`, `app/Models/PosCart.php`

### H3. Duplicated order-placement logic (`reOrder` vs `storeByRequestFromCart`) — ✅ FIXED

Both checkout and reorder now flow through the same shared helpers — `groupLinesByShop()` (allocation) and `createOrderForShop()` (pricing, stock decrement, product attach, VAT, licenses) — inside `DB::transaction` (commit `51a47ac`). `reOrder()` rebuilds the original order's lines as cart-like objects (resolving size/color ids from the stored pivot names) so a reorder re-prices and re-allocates exactly like a fresh checkout of the same items. One code path, no drift.

**Files:** `app/Repositories/OrderRepository.php` (`reOrder`, `groupLinesByShop`, `createOrderForShop`)

### H4. Null-deref bugs — ⚠️ PARTIALLY FIXED

- ✅ **Fixed:** the flash-sale null crash in the order flow — `$flashSaleProduct?->pivot->quantity - $flashSaleProduct?->pivot->sale_quantity` (the `?` after the second `$flashSaleProduct` was added).
- ❌ **Still broken:** `Admin/RiderController::riderLocation` line 142: `if (! $driver && ! $driver->driverLocation)` — dereferences null when `$driver` is null.
- ❌ **Still broken:** `getCartWiseAmounts` lines 446/449: `->first()->pivot?->price` — missing `?` after `first()` (the twin code in `createOrderForShop` has it right).

**Files:** `app/Http/Controllers/Admin/RiderController.php`, `app/Repositories/OrderRepository.php`

### H5. No order-status transition graph — ❌ UNCHANGED

`statusChange` in admin/shop still accepts **any status → any status** with no validation (verified — just `$request->validate(['status' => 'required'])`). UI guards exist, server doesn't. Cancellation restock only happens in the admin path, not the API cancel path.

**Files:** `app/Http/Controllers/{Admin,Shop}/OrderController.php`

---

## 4. Architecture & maintainability

| Concern | Finding |
|---|---|
| **Repository → Http inversion** | 29 of 53 repositories import `App\Http\Requests` (and some `Resources`) — the "stable core" depends on the HTTP layer; verified in source. |
| **God base class** | `Repository::first` = 295 callers, `Repository::query` = 119 (codegraph hotspots). All repos inherit one generic base. |
| **Fat layer** | `Http` package: fan-in 337 / fan-out 825; controllers duplicate pricing/allocation logic (codegraph boundary analysis). |
| **Dead modules** | `purchase`/`report` nwidart modules are asset-only stubs (no PHP, no `modules_statuses.json`); `module_exists('Purchase')` always false — the `ProductStockOut`/`buyingPrice` branches are dormant. `Modules/` == `modules/` (same inode, case-insensitive FS). |
| **Perf smell in views** | Admin dashboard runs ~20 count queries per render; API order listing runs 8 status-count queries + total + page. |
| **Dead code / debris** | `AddressFormModal copy.vue`, `GuestAddressForm copy.vue`, 7 `DebugTest*.php` Dusk files, commented-out Echo. |
| **No jobs/notifications dirs** | `app/Jobs/`, `app/Notifications/` do not exist; queue is `sync` — all mail/SMS/firebase work is inline, blocking request threads. |

---

## 5. Other notable items

- **No webhooks anywhere** — all 11 gateways verify via redirect/return-URL only (no signed-webhook verification).
- **`PaySafeCard`** composer dep is unused (no controller/enum/seeder entry).
- **`CheckSubscription` middleware is a no-op** passthrough; the subscription tables it guarded were dropped (migration `2026_08_02`). Registered in the web group but does nothing.
- **`DemoModeMiddleware` is inverted**: it blocks **all** API requests whenever `env=local` — but it is only aliased, never wired to a route, so it is dormant. If ever applied, it would break local dev.
- **`OrderRequest`** does not validate `payment_method` against the `PaymentMethod` enum (accepts any string).
- **Security net positives**: `mews/purifier`, CSRF, `$guarded`, no `env()` leakage in app code, no raw user-input SQL (the `whereRaw` flash-sale pricing clauses are parameterized/correlated subqueries — expensive but safe).
- **Cache invalidation exists** for orders/roles/settings but `generale_setting` caches for 30 days and `verify_manage` caches forever — settings changes can go stale.

---

## 6. Prioritized recommendation (updated after recheck)

**Now fixed (do not re-do):** C1 atomicity (transaction wrappers + tests), C2 swallowed-deduction (sale-time deduction removed; `WORKFLOW_PROJECT.md` updated to match — only `project_notes.md` still stale), H3 reorder/checkout unification (shared `groupLinesByShop` + `createOrderForShop`), flash-sale null crash + flash-sale pricing alignment.

**Still open — Week 1 (data integrity):** payment-timeout restock for abandoned orders (C3); register the 3 missing gateway routes (C5).

**Week 2 (correctness):** server-side validation of `allocations` (radius + stock eligibility) (C4); fix the two remaining null-derefs (H4); add an order-status transition map (H5); wire `checkPermission` or retire `acl.php` (C6).

**Week 3 (performance):** eager-load the 8 relations in product listing queries (remove per-item `load()`) (H1); replace `Schema::hasColumn` with a cached flag (H2); add composite indexes for the flash-sale `whereRaw`/`orderByRaw` subqueries; move mail/SMS to a queue worker.

**Decisions to make deliberately:** (a) wire `checkPermission` or retire `acl.php`; (b) C3's stock model (reserve-on-placement with no timeout-restore vs. debit-on-paid) — the recent commits chose shop-inventory-only semantics for C2, but C3 is still open; (c) keep or cut the dormant `purchase`/`report` modules; (d) add webhooks or accept the redirect-verification risk; (e) update or retire `project_notes.md` (stale C2 caveat).

---

## 7. Notes on sources

The existing docs (`project_notes.md`, `significance_issues.md`) were remarkably accurate — every major claim was independently re-verified against source. The one **new** finding beyond the docs is **H1 (per-item `load()` N+1 in `ProductResource`)**, which the existing docs do not call out. After the 2026-08-10 rechecks: **C2's fix created a docs-vs-reality gap that has since been closed in `WORKFLOW_PROJECT.md`** (2026-08-10 — §1, Phase 6 diagram, schema table, Phase 6 operational steps all now state sales are not ledgered and list the actual reference types). **Still stale:** `project_notes.md` (Structural Guidelines §1 and Known Gaps #10) still describe the removed "silently swallowed" sale-time deduction caveat.
