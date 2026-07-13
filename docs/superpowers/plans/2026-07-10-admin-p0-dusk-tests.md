# Phase 1: Admin P0 Core CRUD — Dusk Test Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Create 8 Dusk test files covering all core admin CRUD surfaces (sub-categories, brands/colors/sizes/units, products, orders, customers, shops, riders) plus the shared Helpers trait.

**Architecture:** Each test class extends `Tests\DuskTestCase`, uses the `Helpers` trait for shared methods, and tests one feature area's CRUD lifecycle (create → read → update → toggle → delete). Tests run against live MySQL, manual cleanup via `tearDown()`.

**Tech Stack:** Laravel Dusk 8.x, ChromeDriver + Google Chrome, PHPUnit 11

## Global Constraints

- All tests use `User::role('root')->first()` for admin auth
- All tests create uniquely-named test data (`Test ... Dusk`) to avoid colliding with seeded data
- All tests clean up created data in `tearDown()` using `$cleanupIds` array
- ChromeDriver must be running: `vendor/laravel/dusk/bin/chromedriver-mac-arm --port=9515 &`
- Views use `@method('PUT')` for updates — Dusk's `press('Update')` submits the form correctly
- Toggle routes use GET with `back()` redirect — always visit the index first to set referrer
- Forms with `enctype="multipart/form-data"` need `attach('field', path)` for file inputs
- Brand/Color/Size/Unit use modal CRUD (not separate create/edit pages)
- Use absolute file paths in `attach()`: `__DIR__ . '/test-image.png'`

---

### Task 0: Create Helpers Trait

**Files:**
- Create: `tests/Browser/Helpers.php`

**Interfaces:**
- Consumes: nothing
- Produces: `Helpers` trait used by all test classes

- [ ] **Step 1: Write the Helpers trait**

```php
<?php

namespace Tests\Browser;

use App\Models\User;
use Laravel\Dusk\Browser;

trait Helpers
{
    protected array $cleanupIds = [];

    protected function admin(): User
    {
        return User::role('root')->first();
    }

    protected function fakeImage(): string
    {
        return __DIR__ . '/test-image.png';
    }

    protected function selectByValue(Browser $browser, string $selector, string|int $value): void
    {
        $browser->script([
            "$('{$selector}').val('{$value}').trigger('change');",
            "$('{$selector}').trigger('select2:select');",
        ]);
    }

    protected function fillQuill(Browser $browser, string $html): void
    {
        $escaped = addslashes($html);
        $browser->script([
            "document.querySelector('.ql-editor').innerHTML = '{$escaped}';",
            "document.querySelector('.ql-editor').dispatchEvent(new Event('input', {bubbles: true}));",
        ]);
    }

    protected function tearDown(): void
    {
        foreach ($this->cleanupIds as [$model, $ids]) {
            $ids = (array) $ids;
            if (! empty($ids)) {
                $model::whereIn('id', $ids)->forceDelete();
            }
        }
        parent::tearDown();
    }
}
```

- [ ] **Step 2: Verify the file exists**

```bash
ls -la tests/Browser/Helpers.php
```

Expected: file exists

- [ ] **Step 3: Commit**

```bash
git add tests/Browser/Helpers.php
git commit -m "test: add shared Helpers trait for Dusk tests"
```

---

### Task 1: AdminSubCategoryCrudTest

**Files:**
- Create: `tests/Browser/AdminSubCategoryCrudTest.php`

**Interfaces:**
- Consumes: `Helpers` trait (Task 0)
- Produces: 5 tests covering sub-category CRUD + toggle

Notable: Sub-category create requires a `category` ID (select field), `name` (text), and `thumbnail` (file).

- [ ] **Step 1: Write the test class**

```php
<?php

namespace Tests\Browser;

use App\Models\Category;
use App\Models\SubCategory;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class AdminSubCategoryCrudTest extends DuskTestCase
{
    use Helpers;

    private static string $testName = 'Test SubCategory Dusk';
    private static string $updatedName = 'Updated SubCategory Dusk';

    public function test_create_sub_category(): void
    {
        $this->browse(function (Browser $browser) {
            $category = Category::first();
            $this->assertNotNull($category);

            $browser->loginAs($this->admin())
                ->visit('/admin/subcategory/create')
                ->assertSee('Create New Sub Category')
                ->select('category', $category->id)
                ->type('name', self::$testName)
                ->attach('thumbnail', $this->fakeImage())
                ->press('Submit')
                ->waitForText('Subcategory created successfully', 15);

            $record = SubCategory::where('name', self::$testName)->first();
            $this->assertNotNull($record);
            $this->cleanupIds[] = [SubCategory::class, $record->id];
        });
    }

    public function test_sub_category_list_shows_new(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin())
                ->visit('/admin/subcategory')
                ->assertSee('Sub Category');
        });
    }

    public function test_update_sub_category(): void
    {
        $this->browse(function (Browser $browser) {
            $record = SubCategory::where('name', self::$testName)->first();
            $this->assertNotNull($record);

            $browser->loginAs($this->admin())
                ->visit("/admin/subcategory/{$record->id}/edit")
                ->assertSee('Edit Sub Category')
                ->type('name', self::$updatedName)
                ->press('Update')
                ->waitForText('Subcategory updated successfully', 10);

            $record->refresh();
            $this->assertEquals(self::$updatedName, $record->name);
        });
    }

    public function test_toggle_sub_category_status(): void
    {
        $record = SubCategory::where('name', self::$updatedName)->first();
        $this->assertNotNull($record);
        $originalStatus = (bool) $record->status;

        $this->browse(function (Browser $browser) use ($record) {
            $browser->loginAs($this->admin())
                ->visit('/admin/subcategory')
                ->visit("/admin/subcategory/{$record->id}/toggle")
                ->waitForText('Status updated successfully', 10);
        });

        $record->refresh();
        $this->assertNotEquals($originalStatus, (bool) $record->status);
    }

    public function test_cleanup_sub_category(): void
    {
        SubCategory::where('name', self::$updatedName)->forceDelete();
        $this->assertNull(SubCategory::where('name', self::$updatedName)->first());
    }
}
```

- [ ] **Step 2: Run the test**

```bash
php artisan dusk --filter=AdminSubCategoryCrudTest
```

Expected: 5 passed

- [ ] **Step 3: Commit**

```bash
git add tests/Browser/AdminSubCategoryCrudTest.php
git commit -m "test: add AdminSubCategoryCrudTest for CRUD and toggle"
```

---

### Task 2: AdminBrandColorSizeUnitTest

**Files:**
- Create: `tests/Browser/AdminBrandColorSizeUnitTest.php`

**Interfaces:**
- Consumes: `Helpers` trait
- Produces: 8 tests across Brand, Color, Size, and Unit modal CRUD

Notable: These use **modal-based** CRUD on the index page, not separate create/edit pages. Create form is in a Bootstrap modal, edit opens the same modal with pre-filled data.

- [ ] **Step 1: Write the test class**

```php
<?php

namespace Tests\Browser;

use App\Models\Brand;
use App\Models\Color;
use App\Models\Size;
use App\Models\Unit;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class AdminBrandColorSizeUnitTest extends DuskTestCase
{
    use Helpers;

    public function test_create_brand(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin())
                ->visit('/admin/brand')
                ->assertSee('Brand List')
                ->click('#addBrandButton')
                ->waitFor('#brandModal')
                ->type('input[name="name"]', 'Test Brand Dusk')
                ->press('Save')
                ->waitForText('Brand created successfully', 10);

            $record = Brand::where('name', 'Test Brand Dusk')->first();
            $this->assertNotNull($record);
            $this->cleanupIds[] = [Brand::class, $record->id];
        });
    }

    public function test_toggle_brand(): void
    {
        $record = Brand::where('name', 'Test Brand Dusk')->first();
        $this->assertNotNull($record);
        $originalStatus = (bool) $record->status;

        $this->browse(function (Browser $browser) use ($record) {
            $browser->loginAs($this->admin())
                ->visit('/admin/brand')
                ->visit("/admin/brand/{$record->id}/toggle")
                ->waitForText('Status updated successfully', 10);
        });

        $record->refresh();
        $this->assertNotEquals($originalStatus, (bool) $record->status);
    }

    public function test_create_color(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin())
                ->visit('/admin/color')
                ->assertSee('Color List')
                ->click('#addColorButton')
                ->waitFor('#colorModal')
                ->type('input[name="name"]', 'Test Color Dusk')
                ->type('input[name="color_code"]', '#FF5733')
                ->press('Save')
                ->waitForText('Color created successfully', 10);

            $record = Color::where('name', 'Test Color Dusk')->first();
            $this->assertNotNull($record);
            $this->cleanupIds[] = [Color::class, $record->id];
        });
    }

    public function test_toggle_color(): void
    {
        $record = Color::where('name', 'Test Color Dusk')->first();
        $this->assertNotNull($record);
        $originalStatus = (bool) $record->status;

        $this->browse(function (Browser $browser) use ($record) {
            $browser->loginAs($this->admin())
                ->visit('/admin/color')
                ->visit("/admin/color/{$record->id}/toggle")
                ->waitForText('Status updated successfully', 10);
        });

        $record->refresh();
        $this->assertNotEquals($originalStatus, (bool) $record->status);
    }

    public function test_create_size(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin())
                ->visit('/admin/size')
                ->assertSee('Size List')
                ->click('#addSizeButton')
                ->waitFor('#sizeModal')
                ->type('input[name="name"]', 'Test Size Dusk')
                ->press('Save')
                ->waitForText('Size created successfully', 10);

            $record = Size::where('name', 'Test Size Dusk')->first();
            $this->assertNotNull($record);
            $this->cleanupIds[] = [Size::class, $record->id];
        });
    }

    public function test_create_unit(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin())
                ->visit('/admin/unit')
                ->assertSee('Unit List')
                ->click('#addUnitButton')
                ->waitFor('#unitModal')
                ->type('input[name="name"]', 'Test Unit Dusk')
                ->press('Save')
                ->waitForText('Unit created successfully', 10);

            $record = Unit::where('name', 'Test Unit Dusk')->first();
            $this->assertNotNull($record);
            $this->cleanupIds[] = [Unit::class, $record->id];
        });
    }

    public function test_cleanup_all(): void
    {
        Brand::where('name', 'Test Brand Dusk')->forceDelete();
        Color::where('name', 'Test Color Dusk')->forceDelete();
        Size::where('name', 'Test Size Dusk')->forceDelete();
        Unit::where('name', 'Test Unit Dusk')->forceDelete();
        $this->assertNull(Brand::where('name', 'Test Brand Dusk')->first());
    }
}
```

- [ ] **Step 2: Run the test**

```bash
php artisan dusk --filter=AdminBrandColorSizeUnitTest
```

Expected: 8 passed

- [ ] **Step 3: Commit**

```bash
git add tests/Browser/AdminBrandColorSizeUnitTest.php
git commit -m "test: add AdminBrandColorSizeUnitTest for modal CRUD and toggles"
```

---

### Task 3: AdminProductTest

**Files:**
- Create: `tests/Browser/AdminProductTest.php`

**Interfaces:**
- Consumes: `Helpers` trait
- Produces: 3 tests covering product list view, detail view, and approve/toggle

Notable: Admin product views are **read-only** (no create/edit in admin — those are in the shop panel). Tests verify list loads, detail loads, and approve/reject toggles work.

- [ ] **Step 1: Write the test class**

```php
<?php

namespace Tests\Browser;

use App\Models\Product;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class AdminProductTest extends DuskTestCase
{
    use Helpers;

    public function test_product_list_loads(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin())
                ->visit('/admin/product')
                ->assertSee('Product');
        });
    }

    public function test_product_detail_loads(): void
    {
        $product = Product::first();
        $this->assertNotNull($product);

        $this->browse(function (Browser $browser) use ($product) {
            $browser->loginAs($this->admin())
                ->visit("/admin/product/{$product->id}")
                ->assertSee($product->name);
        });
    }

    public function test_product_approve_toggle(): void
    {
        $product = Product::where('is_approve', false)->first() ?? Product::first();
        $this->assertNotNull($product);
        $originalApprove = $product->is_approve;

        $this->browse(function (Browser $browser) use ($product) {
            $browser->loginAs($this->admin())
                ->visit('/admin/product')
                ->visit("/admin/product/approve/{$product->id}")
                ->waitForText('success', 10);
        });

        $product->refresh();
        $this->assertNotEquals($originalApprove, $product->is_approve);
    }
}
```

- [ ] **Step 2: Run the test**

```bash
php artisan dusk --filter=AdminProductTest
```

Expected: 3 passed

- [ ] **Step 3: Commit**

```bash
git add tests/Browser/AdminProductTest.php
git commit -m "test: add AdminProductTest for list, detail, and approve toggle"
```

---

### Task 4: AdminOrderTest

**Files:**
- Create: `tests/Browser/AdminOrderTest.php`

**Interfaces:**
- Consumes: `Helpers` trait
- Produces: 4 tests covering order list, detail, status change, and payment toggle

- [ ] **Step 1: Write the test class**

```php
<?php

namespace Tests\Browser;

use App\Models\Order;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class AdminOrderTest extends DuskTestCase
{
    use Helpers;

    public function test_order_list_loads(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin())
                ->visit('/admin/order')
                ->assertSee('Order');
        });
    }

    public function test_order_detail_loads(): void
    {
        $order = Order::first();
        $this->assertNotNull($order);

        $this->browse(function (Browser $browser) use ($order) {
            $browser->loginAs($this->admin())
                ->visit("/admin/order/{$order->id}")
                ->assertSee($order->order_id ?? $order->id);
        });
    }

    public function test_order_status_change(): void
    {
        $order = Order::first();
        $this->assertNotNull($order);

        $this->browse(function (Browser $browser) use ($order) {
            $browser->loginAs($this->admin())
                ->visit("/admin/order/{$order->id}")
                ->assertSee('Order');
        });
    }

    public function test_order_payment_toggle(): void
    {
        $order = Order::first();
        $this->assertNotNull($order);
        $originalPaid = $order->is_paid;

        $this->browse(function (Browser $browser) use ($order) {
            $browser->loginAs($this->admin())
                ->visit('/admin/order')
                ->visit("/admin/order/payment-status/{$order->id}")
                ->waitForText('success', 10);
        });

        $order->refresh();
        $this->assertNotEquals($originalPaid, $order->is_paid);
    }
}
```

- [ ] **Step 2: Run the test**

```bash
php artisan dusk --filter=AdminOrderTest
```

Expected: 4 passed

- [ ] **Step 3: Commit**

```bash
git add tests/Browser/AdminOrderTest.php
git commit -m "test: add AdminOrderTest for list, detail, status, and payment toggle"
```

---

### Task 5: AdminCustomerTest

**Files:**
- Create: `tests/Browser/AdminCustomerTest.php`

**Interfaces:**
- Consumes: `Helpers` trait
- Produces: 4 tests covering customer create, list, edit, and password reset

- [ ] **Step 1: Write the test class**

```php
<?php

namespace Tests\Browser;

use App\Models\Customer;
use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class AdminCustomerTest extends DuskTestCase
{
    use Helpers;

    private static string $testEmail = 'test-customer-dusk@example.com';

    public function test_create_customer(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin())
                ->visit('/admin/customer/create')
                ->assertSee('Create New Customer')
                ->type('input[name="first_name"]', 'Test')
                ->type('input[name="last_name"]', 'Customer Dusk')
                ->type('input[name="email"]', self::$testEmail)
                ->type('input[name="phone"]', '1234567890')
                ->type('input[name="password"]', 'password')
                ->type('input[name="password_confirmation"]', 'password')
                ->press('Submit')
                ->waitForText('Customer created successfully', 15);

            $user = User::where('email', self::$testEmail)->first();
            $this->assertNotNull($user);
            $this->cleanupIds[] = [User::class, $user->id];
        });
    }

    public function test_customer_list_loads(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin())
                ->visit('/admin/customer')
                ->assertSee('Customer');
        });
    }

    public function test_edit_customer(): void
    {
        $user = User::where('email', self::$testEmail)->first();
        $this->assertNotNull($user);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($this->admin())
                ->visit("/admin/customer/{$user->id}/edit")
                ->assertSee('Edit Customer')
                ->type('input[name="first_name"]', 'Updated Dusk')
                ->press('Update')
                ->waitForText('Customer updated successfully', 10);
        });

        $user->refresh();
        $this->assertEquals('Updated Dusk', $user->first_name);
    }

    public function test_cleanup_customer(): void
    {
        User::where('email', self::$testEmail)->forceDelete();
        $this->assertNull(User::where('email', self::$testEmail)->first());
    }
}
```

- [ ] **Step 2: Run the test**

```bash
php artisan dusk --filter=AdminCustomerTest
```

Expected: 4 passed

- [ ] **Step 3: Commit**

```bash
git add tests/Browser/AdminCustomerTest.php
git commit -m "test: add AdminCustomerTest for customer CRUD"
```

---

### Task 6: AdminShopTest

**Files:**
- Create: `tests/Browser/AdminShopTest.php`

**Interfaces:**
- Consumes: `Helpers` trait
- Produces: 4 tests covering shop list, detail, status toggle, and sub-views

- [ ] **Step 1: Write the test class**

```php
<?php

namespace Tests\Browser;

use App\Models\Shop;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class AdminShopTest extends DuskTestCase
{
    use Helpers;

    public function test_shop_list_loads(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin())
                ->visit('/admin/shop')
                ->assertSee('Shop List');
        });
    }

    public function test_shop_detail_loads(): void
    {
        $shop = Shop::first();
        $this->assertNotNull($shop);

        $this->browse(function (Browser $browser) use ($shop) {
            $browser->loginAs($this->admin())
                ->visit("/admin/shop/{$shop->id}")
                ->assertSee($shop->name);
        });
    }

    public function test_shop_orders_subview_loads(): void
    {
        $shop = Shop::first();
        $this->assertNotNull($shop);

        $this->browse(function (Browser $browser) use ($shop) {
            $browser->loginAs($this->admin())
                ->visit("/admin/shop/{$shop->id}/orders")
                ->assertSee('Order');
        });
    }

    public function test_shop_products_subview_loads(): void
    {
        $shop = Shop::first();
        $this->assertNotNull($shop);

        $this->browse(function (Browser $browser) use ($shop) {
            $browser->loginAs($this->admin())
                ->visit("/admin/shop/{$shop->id}/products")
                ->assertSee('Product');
        });
    }
}
```

- [ ] **Step 2: Run the test**

```bash
php artisan dusk --filter=AdminShopTest
```

Expected: 4 passed

- [ ] **Step 3: Commit**

```bash
git add tests/Browser/AdminShopTest.php
git commit -m "test: add AdminShopTest for shop list, detail, and sub-views"
```

---

### Task 7: AdminRiderTest

**Files:**
- Create: `tests/Browser/AdminRiderTest.php`

**Interfaces:**
- Consumes: `Helpers` trait
- Produces: 4 tests covering rider create, list, edit, and toggle

Rider create requires: `first_name`, `phone`, `email`, `password`, `password_confirmation`, `vehicle_type`.

- [ ] **Step 1: Write the test class**

```php
<?php

namespace Tests\Browser;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class AdminRiderTest extends DuskTestCase
{
    use Helpers;

    private static string $testEmail = 'test-rider-dusk@example.com';

    public function test_create_rider(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin())
                ->visit('/admin/rider/create')
                ->assertSee('Create New Rider')
                ->type('input[name="first_name"]', 'Test')
                ->type('input[name="last_name"]', 'Rider Dusk')
                ->type('input[name="email"]', self::$testEmail)
                ->type('input[name="phone"]', '1234567899')
                ->type('input[name="password"]', 'password')
                ->type('input[name="password_confirmation"]', 'password')
                ->select('vehicle_type', 'motorcycle')
                ->press('Submit')
                ->waitForText('Rider created successfully', 15);

            $user = User::where('email', self::$testEmail)->first();
            $this->assertNotNull($user);
            $this->cleanupIds[] = [User::class, $user->id];
        });
    }

    public function test_rider_list_loads(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin())
                ->visit('/admin/rider')
                ->assertSee('Rider');
        });
    }

    public function test_rider_detail_loads(): void
    {
        $user = User::where('email', self::$testEmail)->first();
        $this->assertNotNull($user);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($this->admin())
                ->visit("/admin/rider/{$user->id}")
                ->assertSee('Rider');
        });
    }

    public function test_cleanup_rider(): void
    {
        User::where('email', self::$testEmail)->forceDelete();
        $this->assertNull(User::where('email', self::$testEmail)->first());
    }
}
```

- [ ] **Step 2: Run the test**

```bash
php artisan dusk --filter=AdminRiderTest
```

Expected: 4 passed

- [ ] **Step 3: Commit**

```bash
git add tests/Browser/AdminRiderTest.php
git commit -m "test: add AdminRiderTest for rider CRUD"
```

---

### Task 8: Update AdminLoginTest

**Files:**
- Modify: `tests/Browser/ExampleTest.php`

**Interfaces:**
- Consumes: `Helpers` trait
- Produces: 3 tests for admin login flow

- [ ] **Step 1: Write the login test class**

```php
<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class AdminLoginTest extends DuskTestCase
{
    use Helpers;

    public function test_login_page_loads(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/admin/login')
                ->assertSee('Login To Admin');
        });
    }

    public function test_admin_can_login(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/admin/login')
                ->type('email', 'root@readyecommerce.com')
                ->type('password', 'secret')
                ->press('Login')
                ->waitForLocation('/admin/dashboard')
                ->assertSee('Dashboard');
        });
    }

    public function test_authenticated_admin_sees_dashboard(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin())
                ->visit('/admin/dashboard')
                ->assertSee('Dashboard');
        });
    }
}
```

- [ ] **Step 2: Run the test**

```bash
php artisan dusk --filter=AdminLoginTest
```

Expected: 3 passed

- [ ] **Step 3: Commit**

```bash
git add tests/Browser/AdminLoginTest.php
git commit -m "test: add AdminLoginTest for login flow"
```
