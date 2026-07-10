# How to Write & Run Laravel Dusk Tests

## Setup (already done)

```bash
# Start ChromeDriver before running tests
vendor/laravel/dusk/bin/chromedriver-mac-arm --port=9515 &

# Run tests
php artisan dusk
```

---

## Anatomy of a Dusk Test

Every Dusk test follows this pattern:

```php
<?php

namespace Tests\Browser;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class YourTest extends DuskTestCase
{
    public function test_something(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::role('root')->first())  // login as admin
                ->visit('/admin/some-page')                 // navigate
                ->assertSee('Page Title')                   // verify content
                ->press('Save')                             // click a button
                ->waitForText('Success')                    // wait for result
                ->assertSee('Success');                     // verify result
        });
    }
}
```

---

## Useful Dusk Methods

### Navigation
| Method | Example |
|---|---|
| `visit('/url')` | Go to a page |
| `visitRoute('route.name')` | Go by named route |
| `back()` | Browser back |
| `refresh()` | Reload page |

### Finding Things on the Page
| Method | Example | What it checks |
|---|---|---|
| `assertSee('text')` | `->assertSee('Save And Update')` | Text in `<body>` |
| `assertSeeIn('selector', 'text')` | `->assertSeeIn('.card-title', 'Current Color')` | Text inside a CSS element |
| `assertDontSee('text')` | | Text NOT present |
| `assertTitleContains('text')` | | `<title>` contains text |
| `assertValue('input[name="x"]', 'value')` | | Input has this value |
| `assertInputValue('name', 'value')` | | Shorthand for `input[name="name"]` |
| `assertPathIs('/url')` | | Current URL path |
| `assertPathBeginsWith('/admin')` | | URL starts with this |

### Interacting with Forms
| Method | Example |
|---|---|
| `type('name', 'value')` | Fill `input[name="name"]` or `textarea` |
| `select('name', 'option')` | Pick `<select>` option |
| `check('name')` / `uncheck('name')` | Checkboxes |
| `radio('name', 'value')` | Radio buttons |
| `attach('name', '/path/to/file')` | File upload |
| `press('Button Text')` | Click a `<button>` or `<input type="submit">` |
| `value('input[name="x"]')` | Read current input value |
| `script(["js code"])` | Run arbitrary JavaScript |

### Clicking Elements
| Method | Example |
|---|---|
| `click('.class-name')` | Click by CSS selector |
| `clickLink('Link Text')` | Click an `<a>` with matching text |
| `click('#element-id')` | Click by element ID |
| `elements('.selector')` | Returns array of matching elements |

### Waiting (crucial for real apps)
| Method | Example | Use when… |
|---|---|---|
| `waitForText('text', seconds)` | `->waitForText('Updated')` | Page updates after form submit |
| `waitForLocation('/url', seconds)` | `->waitForLocation('/admin/dashboard')` | Redirect happens |
| `waitForLink('text', seconds)` | | Link appears after AJAX |
| `waitForReload(seconds)` | | Page reloads |
| `pause(milliseconds)` | `->pause(500)` | Last resort — avoid if possible |

### Working with the Database

Inside a Dusk test, you can use Eloquent directly:

```php
// Assert DB state after form submit
$product = Product::where('name', 'Test Item')->first();
$this->assertNotNull($product);
$this->assertEquals('active', $product->status);
```

---

## Reference: AdminThemeColorTest

```php
<?php

namespace Tests\Browser;

use App\Models\ThemeColor;
use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class AdminThemeColorTest extends DuskTestCase
{
    // 1. Visit page, verify content loads
    public function test_admin_can_see_theme_color_page(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::role('root')->first())
                ->visit('/admin/theme-color')
                ->assertSee('Theme Colors Settings')
                ->assertSee('Current Color')
                ->assertSee('Save And Update');
        });
    }

    // 2. Click an element and verify the form updates
    public function test_clicking_color_palette_updates_form(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::role('root')->first())
                ->visit('/admin/theme-color');

            $initialPrimary = $browser->value('input[name="primary_color"]');

            $palette = $browser->elements('.color-panel:not(.active) .primary-color');
            if (count($palette) > 0) {
                $palette[0]->click();
                $browser->pause(300);

                $newPrimary = $browser->value('input[name="primary_color"]');
                $this->assertNotEquals($initialPrimary, $newPrimary,
                    'Primary color should change after clicking a different palette');
            }
        });
    }

    // 3. Submit the form and verify DB updated
    public function test_saving_color_update_succeeds(): void
    {
        $this->browse(function (Browser $browser) {
            $testPrimary = '#8B5CF6';
            $testSecondary = '#EDE9FE';

            $browser->loginAs(User::role('root')->first())
                ->visit('/admin/theme-color')
                ->click('.color-panel .primary-color')
                ->pause(300);

            // Override hidden inputs with test values
            $browser->script([
                "document.getElementById('primary_color').value = '{$testPrimary}';",
                "document.getElementById('secondary_color').value = '{$testSecondary}';",
            ]);

            $browser->press('Save And Update')
                ->waitForText('Theme color updated successfully', 10)
                ->assertSee('Theme color updated successfully');

            // Verify the database was actually updated
            $defaultTheme = ThemeColor::where('is_default', true)->first();
            $this->assertNotNull($defaultTheme);
            $this->assertEquals(strtolower($testPrimary), strtolower($defaultTheme->primary));
            $this->assertEquals(strtolower($testSecondary), strtolower($defaultTheme->secondary));
        });
    }
}
```

---

## Patterns by Use Case

### CRUD Form (Create)
```php
$browser->visit('/admin/category/create')
    ->type('name', 'Test Category')
    ->press('Save')
    ->waitForText('Category created')
    ->assertSee('Category created');

$category = Category::where('name', 'Test Category')->first();
$this->assertNotNull($category);
```

### CRUD (Update with PUT)
```php
$browser->visit('/admin/category/1/edit')
    ->type('name', 'Updated Name')
    ->press('Update')
    ->waitForText('Updated')
    ->assertSee('Updated');
```

### Toggle Status
```php
$browser->visit('/admin/category')
    ->click('.toggle-btn')      // click toggle for first item
    ->waitForText('Updated')
    ->assertSee('Updated');
```

### Login Then Test
```php
$admin = User::role('root')->first();
$browser->loginAs($admin)
    ->visit('/admin/protected-page')
    ->assertSee('Protected Content');
```

### Read-Only Page
```php
$browser->visit('/admin/order')
    ->assertSee('Order List')
    ->assertSeeIn('table', 'Order ID');
```

---

## Creating a New Test

```bash
# Create the test file
touch tests/Browser/YourFeatureTest.php
```

Then copy the boilerplate above, change the class name and routes, and run:

```bash
php artisan dusk --filter=YourFeatureTest
```

To run a single test method:

```bash
php artisan dusk --filter='test_name_here'
```

---

## Tips

- **Don't re-login in every test** — each `$this->browse()` call gets a fresh browser session, so you need `loginAs()` in each test
- **Use `waitForText`** over `pause()` — it's faster and more reliable
- **Auth as root** → `User::role('root')->first()` for all admin tests
- **Always assert DB changes** — a button click working doesn't mean the data saved. Check with Eloquent after
- **Cleanup test data** — use `RefreshDatabase` trait if tests create records, or delete them manually in `tearDown()`
- **ChromeDriver must be running** — `vendor/laravel/dusk/bin/chromedriver-mac-arm --port=9515 &`
