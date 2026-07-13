# Janmitram E2E Dusk Test Suite — Design Spec

## Context

Janmitram is a Laravel 11 e-commerce app with ~490 routes across 4 surfaces (admin Blade panel, shop Blade panel, customer Vue SPA, 3 mobile APIs). Current test coverage: 2 placeholder PHPUnit tests + 2 Dusk test files (Category CRUD, Theme Color). Zero CI/pipeline.

Goal: comprehensive Laravel Dusk browser test coverage for every feature, organized by surface, producing a maintainable ~200-test suite that catches regressions and proves the app works.

## Architecture

### Test Runner
- **Laravel Dusk 8.x** — browser automation via ChromeDriver + Google Chrome
- ChromeDriver starts via `vendor/laravel/dusk/bin/chromedriver-mac-arm --port=9515`
- Tests run against live MySQL database (no test database isolation — manual cleanup per test)
- All tests navigate relative paths (`/admin/xxx`, `/shop/xxx`) resolved against `APP_URL=http://localhost:8888/janmitram-app`

### Test Organization
~20 test files grouped by feature area, not by controller. Each file covers one domain's CRUD lifecycle.

**Priority tiers:**
- **P0** — Core CRUD surfaces (categories, products, orders, customers, shops, riders, auth)
- **P1** — Important features (settings, content management, coupons, flash sales, shop operations)
- **P2** — Secondary features (subscriptions, support tickets, POS, returns, API, roles)
- **P3** — Nice-to-have (chat, bulk import/export, gallery, subscription billing)

### File Structure
```
tests/Browser/
  Helpers.php                        — Trait with shared helpers
  test-image.png                     — Dummy image for file uploads
  
  # Admin Panel — P0
  AdminLoginTest.php
  AdminCategoryCrudTest.php          — ✅ done
  AdminSubCategoryCrudTest.php
  AdminBrandColorSizeUnitTest.php
  AdminProductTest.php
  AdminOrderTest.php
  AdminCustomerTest.php
  AdminShopTest.php
  AdminRiderTest.php
  
  # Admin Panel — P1
  AdminSettingsTest.php
  AdminThemeColorTest.php            — ✅ done
  AdminBannerAdTest.php
  AdminCouponFlashSaleTest.php
  AdminBlogPageTest.php
  AdminFooterMenuTest.php
  
  # Admin Panel — P2
  AdminSupportTicketTest.php
  AdminSubscriptionTest.php
  AdminEmployeeRoleTest.php
  AdminDeliveryAreaTest.php
  
  # Shop Panel — P0/P1
  ShopLoginTest.php
  ShopProductTest.php
  ShopOrderTest.php
  ShopVoucherBannerTest.php
  ShopEmployeeTest.php
  
  # Shop Panel — P2/P3
  ShopWithdrawTest.php
  ShopReturnOrderTest.php
  ShopPOSTest.php
  ShopChatTest.php
  ShopFlashSaleTest.php
  ShopBulkImportExportTest.php
  
  # API — P1/P2 (Dusk browser)
  CustomerApiTest.php
  SellerApiTest.php
  RiderApiTest.php
  ApiAuthFlowTest.php
```

## Standard Test Patterns

### Pattern 1: Auth
```php
protected function admin(): User
{
    return User::role('root')->first();
}
```
Every admin test starts with `$browser->loginAs($this->admin())`.

### Pattern 2: CRUD Create
```php
$browser->loginAs($this->admin())
    ->visit('/admin/category/create')
    ->assertSee('Create New Category')
    ->type('name', $name)
    ->type('description', $description)
    ->attach('thumbnail', $this->fakeImage())
    ->press('Submit')
    ->waitForText('Category created successfully', 15);

$record = Category::where('name', $name)->first();
$this->assertNotNull($record);
$this->assertEquals($description, $record->description);
```

### Pattern 3: CRUD Update (PUT method)
```php
$browser->loginAs($this->admin())
    ->visit("/admin/category/{$id}/edit")
    ->type('name', $newName)
    ->type('description', $newDesc)
    ->press('Update')
    ->waitForText('Category updated successfully', 10);

$record->refresh();
$this->assertEquals($newName, $record->name);
```

### Pattern 4: Status Toggle (GET back())
```php
$browser->loginAs($this->admin())
    ->visit('/admin/category')                          // set referrer
    ->visit("/admin/category/{$id}/toggle")              // toggle URL
    ->waitForText('Status updated successfully', 10);

$record->refresh();
$this->assertNotEquals($originalStatus, (bool) $record->status);
```

### Pattern 5: Modal CRUD (Brand, Color, Size, Unit)
These are in-index modals, not separate pages:
```php
$browser->loginAs($this->admin())
    ->visit('/admin/brand')
    ->click('#addBrandButton')                          // open modal
    ->waitFor('#brandModal')
    ->type('input[name="name"]', $name)
    ->press('Save')
    ->waitForText('Brand created successfully', 10);
```

### Pattern 6: Select2 Handling
Select2 replaces `<select>` with JS-rendered UI. Use script execution:
```php
$browser->script([
    "$('select[name=\"category\"]').val('{$catId}').trigger('change');",
    "$('select[name=\"category\"]').trigger('select2:select');",
]);
```

### Pattern 7: Quill.js Rich Text
Quill uses a hidden `<textarea>` and a rendered `.ql-editor` div:
```php
$browser->script([
    "document.querySelector('.ql-editor').innerHTML = '<p>{$content}</p>';",
    // trigger Quill's change event to sync to hidden textarea
    "document.querySelector('.ql-editor').dispatchEvent(new Event('input', {bubbles: true}));",
]);
```

### Pattern 8: Select/Radio With visible text
```php
$browser->select('unit', $unitId)       // standard select
       ->radio('currency_position', 'prefix')  // radio group
       ->check('show_footer');                  // checkbox
```

## Shared Helpers (`Helpers.php` trait)

```php
<?php

namespace Tests\Browser;

use App\Models\User;

trait Helpers
{
    protected function admin(): User
    {
        return User::role('root')->first();
    }

    protected function fakeImage(): string
    {
        return __DIR__ . '/test-image.png';
    }

    protected function select2(Browser $browser, string $selector, string|int $value): void
    {
        $browser->script([
            "$('{$selector}').val('{$value}').trigger('change');",
            "$('{$selector}').trigger('select2:select');",
        ]);
    }

    protected function quill(Browser $browser, string $html): void
    {
        $browser->script([
            "document.querySelector('.ql-editor').innerHTML = '" . addslashes($html) . "';",
            "document.querySelector('.ql-editor').dispatchEvent(new Event('input', {bubbles: true}));",
        ]);
    }
}
```

## Cleanup Strategy

Each test class that creates data uses a `$cleanupIds` array:

```php
protected array $cleanupIds = [];

protected function tearDown(): void
{
    foreach ($this->cleanupIds as [$model, $ids]) {
        $model::whereIn('id', (array) $ids)->forceDelete();
    }
    parent::tearDown();
}

// In a test:
$category = Category::create([...]);
$this->cleanupIds[] = [Category::class, $category->id];
```

For existing seeded data (categories, products, etc.), tests **read only** and do NOT modify seeded records. Test data uses unique names (e.g. `'Test Category Dusk'`) that are distinguishable from seed data.

## Test Execution

```bash
# Terminal 1: Start ChromeDriver
vendor/laravel/dusk/bin/chromedriver-mac-arm --port=9515 &

# Run all Dusk tests
php artisan dusk

# Run a specific test class
php artisan dusk --filter=AdminCategoryCrudTest

# Run a single test method
php artisan dusk --filter=test_create_category
```

Expected runtime: ~5-15 seconds per test, ~15-45 minutes for full suite.

## Implementation Order

### Phase 1: P0 Admin Core (9 files)
AdminLoginTest, AdminCategoryCrudTest (done), AdminSubCategoryCrudTest, AdminBrandColorSizeUnitTest, AdminProductTest, AdminOrderTest, AdminCustomerTest, AdminShopTest, AdminRiderTest

### Phase 2: P1 Admin Features (6 files)
AdminSettingsTest, AdminThemeColorTest (done), AdminBannerAdTest, AdminCouponFlashSaleTest, AdminBlogPageTest, AdminFooterMenuTest

### Phase 3: P2 Admin Features (4 files)
AdminSupportTicketTest, AdminSubscriptionTest, AdminEmployeeRoleTest, AdminDeliveryAreaTest

### Phase 4: P0/P1 Shop Panel (5 files)
ShopLoginTest, ShopProductTest, ShopOrderTest, ShopVoucherBannerTest, ShopEmployeeTest

### Phase 5: P2/P3 Shop Panel (6 files)
ShopWithdrawTest, ShopReturnOrderTest, ShopPOSTest, ShopChatTest, ShopFlashSaleTest, ShopBulkImportExportTest

### Phase 6: API Tests (4 files)
CustomerApiTest, SellerApiTest, RiderApiTest, ApiAuthFlowTest
