# Dusk Browser Test Expansion — Design Specification

## Overview

Expand Laravel Dusk test coverage from admin-only CRUD (19 files, ~70 tests) to full end-to-end workflows for the customer Vue 3 SPA, shop owner dashboard, and extended admin flows. Produce ~12 new test files with ~55 tests following the project's existing conventions exactly.

---

## Architecture

### Existing Conventions (Must Match)

| Element | Pattern |
|---|---|
| Base class | `Tests\DuskTestCase` |
| Auth helper | `use Helpers; $this->admin()` → `User::role('root')->first()` |
| Navigation | `$browser->visit('/admin/...')` or `$browser->visitRoute('...')` |
| Assertions | UI text (`assertSee`) + DB state (`$this->assertEquals(...)`) |
| Cleanup | `$cleanupIds = [ [Model::class, [ids]] ]; tearDown()` force-deletes |
| Chrome | Hardcoded to `/Applications/Google Chrome.app` (Mac ARM) |
| Runner | `vendor/laravel/dusk/bin/chromedriver-mac-arm --port=9515 &` then `php artisan dusk` |

### Test Organization by Domain

```
tests/Browser/
├── Customer/                    ← NEW: Customer SPA workflows
│   ├── CartCheckoutTest.php
│   ├── AuthFlowTest.php
│   ├── UserDashboardTest.php
│   ├── WishlistTest.php
│   ├── AddressManagementTest.php
│   ├── MultiVendorCartTest.php
│   └── CouponFlashSaleTest.php
├── ShopOwner/                   ← NEW: Shop owner dashboard
│   ├── ShopDashboardTest.php
│   ├── ProductManagementTest.php
│   └── OrderManagementTest.php
├── AdminExtended/               ← NEW: Admin gaps
│   ├── AdminUserManagementTest.php
│   └── AdminSettingsExtendedTest.php
├── Helpers.php                  ← EXISTING: shared trait
├── DuskTestCase.php             ← EXISTING: base class
└── Admin*.php                   ← EXISTING: 19 admin CRUD tests
```

---

## Phase 1: Infrastructure Fixes (Single Agent)

**Agent**: `InfrastructureAgent`

| Fix | File | Action |
|---|---|---|
| Chrome path | `DuskTestCase.php` | Make binary path configurable via `DUSK_CHROME_BINARY` env var; fallback to current hardcoded path |
| Composer scripts | `composer.json` | Add `test:dusk`, `test:dusk:filter` scripts |
| Dead code | `tests/Browser/Pages/` | Delete `HomePage.php` and `Page.php` (unused) |
| Test artifacts | `tests/Browser/console/`, `screenshots/`, `source/` | Add `.gitignore` entries if missing; clean old files |
| Consistency | `AdminCategoryCrudTest.php`, `AdminThemeColorTest.php` | Add `use Helpers` trait |

---

## Phase 2: Domain Agents (Parallel)

Each agent owns one domain, reads existing conventions and relevant routes/controllers, writes all test files for that domain.

### Agent A: CustomerWorkflowAgent

**Scope**: Full customer-facing SPA (Vue 3) — cart, checkout, auth, dashboard, addresses, wishlist, multi-vendor, coupons/flash sales.

**Routes to cover** (from `routes/web.php` + API):
- `GET /` — home
- `GET /login`, `POST /login` — customer login
- `GET /register`, `POST /register` — registration
- `GET /password/reset` — password reset
- `GET /cart`, `POST /cart`, `PATCH /cart`, `DELETE /cart` — cart API (via `CartController@index|store|increment|decrement|destroy`)
- `POST /checkout` — `CartController@checkout`
- `GET /dashboard` — customer dashboard (auth layout)
- `GET /wishlist` — favorites
- `GET /address` — address management
- Multi-vendor: cart grouped by `shop_id` (see `CartRepository::ShopWiseCartProducts`)
- Coupon apply: `CartController@checkout` with `coupon_code`
- Flash sale: product `flashSales` relationship

**Test scenarios** (~30 tests across 7 files):

| File | Tests |
|---|---|
| `CartCheckoutTest` | add to cart, increment/decrement, remove, buy-now, checkout summary, guest vs logged-in token handling |
| `AuthFlowTest` | login valid/invalid, registration, email verification flow, password reset request, logout |
| `UserDashboardTest` | order history, order detail, profile edit, password change |
| `WishlistTest` | add/remove favorite, list shows in dashboard |
| `AddressManagementTest` | add/edit/delete address, set default, delivery charge calc |
| `MultiVendorCartTest` | add products from 2+ shops, shop-wise grouping, independent checkout per shop |
| `CouponFlashSaleTest` | valid coupon applies, expired/invalid rejected, flash sale price shown, quantity limit enforced |

**Data creation**: Use existing factories (`ProductFactory`, `UserFactory`, `ShopFactory`, `CategoryFactory`, `CouponFactory`, `FlashSaleFactory`, `AddressFactory`) + manual model creation where factories don't exist. Register created IDs in `$cleanupIds`.

---

### Agent B: ShopOwnerWorkflowAgent

**Scope**: Shop owner dashboard (authenticated shop user, not root admin).

**Routes**: `/shop/*` (shop middleware `ShopAuthenticate`)

| File | Tests |
|---|---|
| `ShopDashboardTest` | stats widgets, recent orders, revenue chart, pending approvals |
| `ProductManagementTest` | create product with variants (color/size/unit), image upload, status toggle, approval flow, delete |
| `OrderManagementTest` | order list with filters, order detail, status update (confirm/preparing/ready/delivered), print invoice |

---

### Agent C: AdminExtendedAgent

**Scope**: Admin panel gaps not covered by existing 19 test files.

| File | Tests |
|---|---|
| `AdminUserManagementTest` | create customer, create shop owner, create rider/employee, role assignment, toggle status, impersonate |
| `AdminSettingsExtendedTest` | general settings save, business setup (shop config), verification settings, theme color palette (extends existing) |

---

## Phase 3: Verification Agent

**Agent**: `TestReviewAgent` (adversarial)

**Inputs**: All newly created test files + existing test files.

**Checks**:
1. **Convention compliance** — every test uses `use Helpers`, `$this->admin()` for admin, registers cleanup IDs
2. **DB assertions** — every UI action that mutates state has a corresponding Eloquent assertion after
3. **No hardcoded `/janmitram-app/...` URLs** — use `route()` or relative paths consistent with existing tests
4. **Wait strategies** — `waitForText`/`waitForLocation` over `pause()`
5. **Selector stability** — prefer `data-test` attributes or stable CSS over brittle XPaths (note: existing tests use CSS selectors; match that)
6. **Duplicate coverage** — no two tests exercise the exact same happy path
7. **Error paths** — at least one negative test per file (invalid input, unauthorized, not found)

**Output**: Report with `PASS`/`FAIL` per file + specific fixes needed.

---

## Test File Template (All Agents Follow)

```php
<?php

namespace Tests\Browser\Customer;  // or ShopOwner, AdminExtended

use App\Models\{User, Product, Shop, Order, ...};
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Tests\Browser\Helpers;

class CartCheckoutTest extends DuskTestCase
{
    use Helpers;

    protected array $cleanupIds = [];

    public function test_customer_can_add_product_to_cart(): void
    {
        // Setup: create test data via factories
        $product = Product::factory()->create([...]);
        $this->cleanupIds[] = [Product::class, $product->id];

        $this->browse(function (Browser $browser) use ($product) {
            // Guest: uses cartAccessToken from helper
            $browser->visit("/product/{$product->slug}")
                ->press('Add to Cart')
                ->waitForText('product added to cart')
                ->assertSee('product added to cart');
        });

        // DB assertion
        $this->assertDatabaseHas('carts', ['product_id' => $product->id]);
    }

    // tearDown() from Helpers trait cleans up $cleanupIds
}
```

---

## Success Criteria

| Metric | Target |
|---|---|
| New test files | 12 |
| New test methods | ~55 |
| All tests pass | `php artisan dusk` green |
| No orphan DB rows | After full suite, `$cleanupIds` force-deletes all test data |
| Conventions | 100% match existing admin tests |
| Coverage domains | Customer SPA, Shop Owner, Admin gaps |

---

## Risks & Mitigations

| Risk | Mitigation |
|---|---|
| SPA (Vue) async rendering | Use `waitForText` on known UI markers; `waitForLocation` after navigation |
| Multi-vendor cart complexity | Test each shop independently, then combined cart |
| Payment gateway integration | Mock external calls or use sandbox; assert checkout response structure, not gateway |
| Chrome version drift | Pin ChromeDriver version in CI; make binary path configurable |
| Test flakiness | `waitForText` with generous timeout (10-15s); no `pause()` unless absolutely necessary |

---

## Next Step

Upon approval, I'll invoke the multi-agent workflow to generate all tests.