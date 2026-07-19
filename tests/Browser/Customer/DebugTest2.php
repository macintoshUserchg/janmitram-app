<?php

namespace Tests\Browser\Customer;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\Browser\Helpers;
use Tests\DuskTestCase;

class DebugTest2 extends DuskTestCase
{
    use Helpers;

    public function test_debug_second_test(): void
    {
        // Create test product (like test 2 does)
        $product = Product::factory()->create([
            'is_active' => true,
            'is_approve' => true,
            'quantity' => 10,
        ]);
        $category = Category::factory()->create(['status' => true]);
        $product->categories()->attach($category->id);
        $this->cleanupIds[] = [Product::class, $product->id];
        $this->cleanupIds[] = [Category::class, $category->id];

        // Create customer
        $customer = User::factory()->create([
            'email' => 'test'.time().rand(1000, 9999).'@example.com',
            'password' => bcrypt('secret'),
        ]);
        $customer->assignRole('customer');
        $customer->customer()->create(['user_id' => $customer->id]);
        $this->cleanupIds[] = [User::class, $customer->id];

        $this->browse(function (Browser $browser) use ($product, $customer) {
            echo "Before visit: product id={$product->id}, name={$product->name}\n";

            $browser->visit('/')
                ->waitForText('Buy Now', 15)
                ->pause(1000);

            // Check how many Buy Now buttons
            $html = $browser->driver->getPageSource();
            $count = substr_count($html, 'Buy Now');
            echo "Buy Now count on page: $count\n";

            // Click Buy Now
            $browser->press('Buy Now')
                ->waitForText('Please login first!', 5)
                ->pause(500);

            // Fill login
            $browser->script([
                "const emailEl = document.querySelector('input[type=\"text\"]'); emailEl.value = '{$customer->email}'; emailEl.dispatchEvent(new Event('input', {bubbles: true}));",
                "const passEl = document.querySelector('input[type=\"password\"]'); passEl.value = 'secret'; passEl.dispatchEvent(new Event('input', {bubbles: true}));",
            ]);
            $browser->pause(200);

            $browser->press('Log in')
                ->waitForText('Login Successful', 10);
        });
    }
}
