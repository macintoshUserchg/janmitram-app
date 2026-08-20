# Significance Issues

Notable issues surfaced from a codegraph (codebase-memory MCP) analysis of the
Janmitram Laravel 11 multi-vendor ecommerce platform, plus a deep trace of the
**consumer order placement workflow**. Dates range from a structural review on
2026-08-05. Sections are ordered roughly by severity/criticality.

> **Update (2026-08-10/11):** issues **#1**, **#2**, and **#5** are now resolved
> (commits `2468392` and `51a47ac`). Since this analysis a **membership card system**
> (`b12c180`) and **discount-price hardening** (`87f74ee`) were also added; the dead
> `coupon_collects` / collected-voucher scheme was removed.

---

## Critical / data-integrity

### 1. Order placement is not wrapped in a DB transaction
`OrderRepository::storeByRequestFromCart()` performs many independent writes:
the `Payment` row, one `Order` per shop, `order_product` attaches, per-line
`Product::decrement()`, `WarehouseService::deductStock()`, `OrderVatTax` rows,
and digital-license assignments — but **none of it runs inside
`DB::transaction()`**. A failure part-way leaves orphaned payments and partially
created orders with already-decremented stock.

**Status:** ✅ **Resolved 2026-08-10** (`2468392`) — checkout, reorder, and POS
order creation now wrap all writes in `DB::transaction`; rollback tests added.

**Files:** `app/Repositories/OrderRepository.php` (`storeByRequestFromCart`)

### 2. Warehouse stock deduction failures are silently swallowed
For physical products the code calls `WarehouseService::deductStock(...)` inside
a `try { ... } catch (\Throwable $th) {}` that **ignores all errors**. An order
can be confirmed and `products.quantity` decremented while the immutable
`warehouse_stock` ledger fails to record the sale, leaving warehouse inventory
out of sync with the real catalog quantity.

**Status:** ✅ **Resolved by redesign 2026-08-10** (`2468392`) — the sale-time
`deductStock` call (and its empty catch) was **removed entirely**; sales draw from
shop inventory only, warehouse stock is consumed at stocking time.

**Files:** `app/Repositories/OrderRepository.php` (line ~169-181)

### 3. Stock is decremented at placement, before payment completes
Orders are created with `order_status = PENDING` and `payment_status = PENDING`
**before** the payment gateway callback runs, yet `products.quantity` and
warehouse stock are already deducted at placement time. Abandoned or failed
payments leave stock consumed with no automatic restore. This is a
reserve-on-placement rather than debit-on-paid model, with no compensating
rollback for unpaid orders.

**Files:** `app/Repositories/OrderRepository.php` (`createNewOrder`)

---

## High / correctness

### 4. Client-trusted shop allocation override
In multi-vendor checkout, `collect($request->allocations ?? [])->keyBy('product_id')`
is used verbatim to force a specific shop per line. There is no server-side
validation that the requested shop is eligible, within radius, or holds the
required stock. A malicious/buggy client can pin lines to any shop.

**Files:** `app/Repositories/OrderRepository.php` (`allocateNearestShop`,
`storeByRequestFromCart`)

### 5. Duplicated order-placement logic in `reOrder()`
`OrderRepository::reOrder()` re-implements the same allocation, pricing, stock
decrement, and digital-license flow as `storeByRequestFromCart()` with slight
divergences (e.g. no size/color handling, null coupon). The two paths can drift,
and fixes in one may not reach the other.

**Status:** ✅ **Resolved 2026-08-10** (`51a47ac`) — checkout and reorder now share
`createOrderForShop()`/`groupLinesByShop()`; reorder re-prices at current prices.

**Files:** `app/Repositories/OrderRepository.php` (`reOrder`)

### 6. Config-defined RBAC (acl.php / CheckPermission) is not wired in
The `CheckPermission` middleware is registered and aliased as `checkPermission`
in `app/Http/Kernel.php`, and `config/acl.php` defines a full permission tree
(admin / shop / shopMultiShop), **but no route uses the middleware or the tree**.
The admin panel effectively runs as role-root-only, so the documented permission
system is dead configuration.

**Files:** `app/Http/Kernel.php`, `config/acl.php`, all route files

---

## Medium / maintainability & architecture

### 7. Three gateway callback named routes referenced but not registered
`app/Http/Controllers/Gateway/PayPal/ProcessController.php` calls
`route('paypal.payment.success', ...)` (and similarly Bkash / PayTabs reference
`bkash.payment.execute` / `paytabs.payment.callback`), but `grep` confirms none
of these routes are registered in `routes/web.php` or `routes/api.php`. At
runtime this throws `RouteNotFoundException` during payment redirect.

**Files:** `app/Http/Controllers/Gateway/{PayPal,Bkash,PayTabs}/ProcessController.php`,
`routes/*.php`

### 8. Inverted dependency: Repositories → Http
The codegraph boundary analysis shows `Repositories → Http = 143 calls`, i.e.
repository code (the intended stable core) imports back into controllers /
request classes. `Http → Repositories` is 181 calls, but the reverse edge makes
the repository layer unstable and hard to test in isolation.

### 9. Fat shared `Repository` god-base class
`app/Support/Repositories/Repository.php` is the single most-coupled symbol in
the codebase: `Repository::first` has **287 callers**, `Repository::query` 122.
All 55+ repositories inherit this base, concentrating generic behavior in one
class and driving the weak de-facto module cohesion (clusters 0.35–0.70).

### 10. Thick `Http` controller mediation layer
`Http` has fan-in 337 / fan-out 825 — the fat-middle layer responsible for
routing, validation orchestration, pricing, stock, and mailing despite the
repository abstraction. Controllers duplicate pricing/allocation logic rather
than delegating it.

### 11. `purchase`/`report` nwidart modules are asset-only stubs
`Modules/purchase` and `Modules/report` contain only CSS/icons/JS — **no PHP**.
`module_exists('Purchase')` returns false, so the
`if (module_exists('Purchase')) productStockOuts()...` branch in order placement
is dead code, and the guard passes silently.

### 12. `warehouse_stock` table name is singular
Migrations create the table as `warehouse_stock` (singular) in
`2026_07_27_000001_create_warehouses_tables.php`, which mismatches plural table
conventions used elsewhere and any code expecting `warehouse_stocks`.

### 13. Shop replicated products missed master product GST/tax rates
Shop inventory products cloned from Central Master Products lacked direct rows
in `product_vat_taxes`, causing online customer orders to calculate 0 GST when
no platform default rate was selected.

**Status:** ✅ **Resolved 2026-08-17** (`364b48d`) — `OrderRepository` and `POSController`
now automatically inherit the active GST/VAT configuration of `$product->masterProduct?->vatTaxes`.

### 14. Order details and Invoice PDF omitted Membership Card discounts and GST rates
Order details views (`admin/order/show` and `shop/order/show`) and PDF invoices
only printed `coupon_discount`, omitting `card_discount` and itemized GST percentages.

**Status:** ✅ **Resolved 2026-08-17** (`1b86166` / `1a92b49`) — Added sortable GST / Tax
and Discounts columns to `/admin/order`, comprehensive financial breakdowns to order views,
and itemized GST percentages with bold units on PDF invoices.

### 15. Razorpay popup window did not auto-close or sync on payment completion
Payment completion in a popup window left the tab open and did not notify the parent checkout page.

**Status:** ✅ **Resolved 2026-08-19** (`4cccd8e`) — Integrated `window.postMessage` and `localStorage` listener synchronization, auto-closing the popup and redirecting to the order confirmation page.

### 16. Commission deductions on direct/zero-fee franchise models
When shops operate on a zero-commission model, wallet deductions needed to be bypassed cleanly.

**Status:** ✅ **Resolved 2026-08-19** (`5602c5e`, `6166ae6`) — Added Direct Zero-Fee model support in business settings and hardened wallet balance debit logic.

### 17. Multi-shop catalog redundancy & single-shop order locking
Homepage catalog in multi-branch cities showed products from only one shop, causing orders to always flow to that single shop.

**Status:** ✅ **Resolved 2026-08-20** (`bb95720`) — Implemented round-robin product distribution across active local branches with strict deduplication (zero duplicate product names) and strict exclusion of Central Shop 1.

### 18. Master product price & discount updates did not cascade to franchise shop copies
When Root Admin updated prices, discounts, or wholesale costs on Master Products, cloned shop copies retained stale prices in the database, distorting storefront listings, cart calculations, POS billing, and GST invoices.

**Status:** ✅ **Resolved 2026-08-20** (`7608b17`) — Implemented `Product::syncShopCopies()` and hooked it into `ProductRepository::updateByRequest()`, `ProductRepository::importRows()`, and `WarehouseService::cloneMasterToShop()`. Verified by 5 new feature tests (44 assertions).

---

## Notes

- Many correctness issues trace to a single overloaded method,
  `OrderRepository::storeByRequestFromCart()` (~220 lines) that mixes
  allocation, pricing, stock, licensing, and mailing.
- Findings marked "verified against code" in this file were re-checked against
  the on-disk routes, migrations, and source during 2026-08-20.
