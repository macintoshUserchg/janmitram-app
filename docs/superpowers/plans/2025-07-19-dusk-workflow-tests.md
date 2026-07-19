# Implementation Plan: Dusk Browser Test Expansion

## Overview

Launch a multi-agent workflow to create ~12 new Dusk test files covering Customer SPA workflows, Shop Owner dashboard, and Admin gaps. The workflow orchestrates 4 specialized agents in sequence with parallel fan-out.

---

## Agent Architecture

```
Workflow: generate-dusk-tests
├── Phase 1: InfrastructureAgent (sequential)
│   └── Fixes DuskTestCase.php, composer.json, cleans dead code
├── Phase 2: Parallel domain agents (fan-out)
│   ├── CustomerWorkflowAgent (7 files, ~30 tests)
│   ├── ShopOwnerWorkflowAgent (3 files, ~15 tests)
│   └── AdminExtendedAgent (2 files, ~10 tests)
└── Phase 3: TestReviewAgent (sequential)
    └── Adversarial review of ALL new + existing tests
```

---

## Phase 1: InfrastructureAgent

**File**: `tests/DuskTestCase.php`

| Task | Action |
|---|---|
| 1.1 | Add `DUSK_CHROME_BINARY` env var support; fallback to current hardcoded path |
| 1.2 | Add `DUSK_DRIVER_URL` support (already exists, verify) |
| 1.3 | Add helper method `getChromeOptions()` for reusability |

**File**: `composer.json`

| Task | Action |
|---|---|
| 1.4 | Add scripts: `test:dusk`, `test:dusk:filter`, `test:dusk:parallel` |

**File**: `tests/Browser/Pages/` (DELETE)

| Task | Action |
|---|---|
| 1.5 | Remove `HomePage.php` and `Page.php` (unused dead code) |

**Files**: `AdminCategoryCrudTest.php`, `AdminThemeColorTest.php`

| Task | Action |
|---|---|
| 1.6 | Add `use Helpers` trait for consistency |

**Verification**: `php artisan dusk --filter=AdminLoginTest` still passes

---

## Phase 2: CustomerWorkflowAgent

**Output Directory**: `tests/Browser/Customer/`

**Input Research** (agent reads these first):
- `routes/web.php` — customer-facing routes (`/`, `/login`, `/register`, `/cart`, `/checkout`, `/dashboard`, `/wishlist`, `/address`, etc.)
- `routes/api.php` — cart/checkout API endpoints (`CartController@index|store|increment|decrement|destroy|checkout`)
- `app/Http/Controllers/API/CartController.php` — full source
- `app/Http/Controllers/API/CheckoutController.php` — if exists
- `app/Http/Middleware/ShopAuthenticate.php` — for understanding shop owner flow
- `resources/js/pages/Checkout.vue`, `Cart.vue`, `Dashboard.vue`, `Wishlist.vue`, `ManageAddress.vue`, `ProductDetails.vue`, `Products.vue`, `CategoryProduct.vue`, `FlashSale.vue`, `Home.vue`, `ShopDetails.vue`
- Existing factories: `ProductFactory`, `UserFactory`, `ShopFactory`, `CategoryFactory`, `CouponFactory`, `FlashSaleFactory`, `AddressFactory`, `CustomerFactory`

**Files to Create**:

| File | Test Methods | Key Routes/Selectors |
|---|---|---|
| `CartCheckoutTest.php` | `test_guest_can_add_to_cart`, `test_logged_in_customer_cart`, `test_increment_decrement_cart`, `test_remove_from_cart`, `test_buy_now_flow`, `test_checkout_summary_shows_totals`, `test_multi_vendor_cart_grouping` | `/product/{slug}`, `.add-to-cart`, `.cart-count`, `/cart`, `/checkout` |
| `AuthFlowTest.php` | `test_customer_login_valid`, `test_customer_login_invalid`, `test_customer_registration`, `test_email_verification_flow`, `test_password_reset_request`, `test_customer_logout` | `/login`, `/register`, `/password/reset`, `form[name="login"]`, `form[name="register"]` |
| `UserDashboardTest.php` | `test_order_history_list`, `test_order_detail_view`, `test_profile_update`, `test_password_change`, `test_reorder_from_history` | `/dashboard`, `/dashboard/orders`, `/dashboard/orders/{id}`, `/dashboard/profile` |
| `WishlistTest.php` | `test_add_to_wishlist`, `test_remove_from_wishlist`, `test_wishlist_page_loads`, `test_move_wishlist_to_cart` | `.add-to-wishlist`, `/wishlist`, `.wishlist-item` |
| `AddressManagementTest.php` | `test_add_address`, `test_edit_address`, `test_delete_address`, `test_set_default_address`, `test_delivery_charge_calculation` | `/address`, `/address/create`, `/address/{id}/edit` |
| `MultiVendorCartTest.php` | `test_cart_groups_by_shop`, `test_checkout_splits_per_shop`, `test_independent_shop_totals` | `/cart` (shop_wise_products), `/checkout` with `shop_ids` |
| `CouponFlashSaleTest.php` | `test_valid_coupon_applies`, `test_expired_coupon_rejected`, `test_flash_sale_price_shown`, `test_flash_sale_quantity_limit` | `/checkout` with `coupon_code`, product with `flashSales` relation |

**Convention Checklist per Test**:
- [ ] Extends `DuskTestCase`
- [ ] `use Helpers`
- [ ] `$cleanupIds = []`
- [ ] Uses factories for test data creation
- [ ] Registers created IDs in `$cleanupIds`
- [ ] Asserts UI text (`assertSee`, `waitForText`)
- [ ] Asserts DB state (`assertDatabaseHas`, model refresh)
- [ ] Uses `route()` helper or relative paths
- [ ] Uses `waitForText`/`waitForLocation` not `pause()`

---

## Phase 2: ShopOwnerWorkflowAgent

**Output Directory**: `tests/Browser/ShopOwner/`

**Input Research**:
- `routes/web.php` — `/shop/*` routes under `ShopAuthenticate` middleware
- `app/Http/Controllers/Shop/` — `ProductController`, `OrderController`, `DashboardController`, `ProfileController`, `BannerController`, `VoucherController`
- `app/Http/Middleware/ShopAuthenticate.php`
- `resources/js/pages/` — shop owner Vue pages (if separate from customer)

**Files to Create**:

| File | Test Methods |
|---|---|
| `ShopDashboardTest.php` | `test_dashboard_loads`, `test_recent_orders_widget`, `test_revenue_widget`, `test_pending_approvals` |
| `ProductManagementTest.php` | `test_create_product_with_variants`, `test_product_image_upload`, `test_product_status_toggle`, `test_product_approval_flow`, `test_product_delete` |
| `OrderManagementTest.php` | `test_order_list_filters`, `test_order_detail_view`, `test_update_order_status`, `test_print_invoice` |

**Auth Pattern**: Create shop owner user with `shop` relationship, login as that user (`$browser->loginAs($shopOwner)`), visit `/shop/...` routes.

---

## Phase 2: AdminExtendedAgent

**Output Directory**: `tests/Browser/AdminExtended/`

**Input Research**: Existing `Admin*.php` tests for patterns, `routes/web.php` admin routes not yet covered.

**Files to Create**:

| File | Test Methods |
|---|---|
| `AdminUserManagementTest.php` | `test_create_customer_user`, `test_create_shop_owner`, `test_create_rider_employee`, `test_role_assignment`, `test_user_status_toggle`, `test_impersonate_user` |
| `AdminSettingsExtendedTest.php` | `test_general_settings_save`, `test_business_setup_shop_config`, `test_verification_settings`, `test_theme_color_palette_extended` |

---

## Phase 3: TestReviewAgent

**Input**: All 12 new test files + 19 existing test files

**Verification Script** (run programmatically):

```php
// Checks per file:
// 1. Has 'use Helpers'
// 2. Has protected $cleanupIds = []
// 3. Every browse() call uses $this->admin() for admin, loginAs() for others
// 4. Every mutation test has DB assertion
// 5. No hardcoded '/janmitram-app/' prefix in visit() (use route())
// 6. Uses waitForText/waitForLocation, not pause()
// 7. At least one negative test per file
// 8. tearDown cleans up $cleanupIds (via Helpers trait)
```

**Output**: JSON report with `PASS`/`FAIL` per file + specific line fixes.

---

## Execution Order

```bash
# 1. Run workflow (creates all files)
# 2. Run infra fixes verification
php artisan dusk --filter=AdminLoginTest

# 3. Run new customer tests
php artisan dusk --filter=Customer

# 4. Run shop owner tests
php artisan dusk --filter=ShopOwner

# 5. Run admin extended tests
php artisan dusk --filter=AdminExtended

# 6. Full suite
php artisan dusk
```

---

## Risk Mitigation

| Risk | Mitigation |
|---|---|
| Vue SPA async rendering | Agents use `waitForText` on known markers (e.g., "Add to Cart", "Checkout") |
| Multi-vendor cart complexity | Tests isolate single-shop then multi-shop scenarios |
| Payment gateway externals | Tests assert checkout response structure, not gateway redirect |
| DB pollution | All agents register every created model ID in `$cleanupIds` |
| Flaky selectors | Use stable CSS classes from Vue components (`data-test` if present, else semantic classes) |

---

## Next Action

Launch the workflow with this plan as input.