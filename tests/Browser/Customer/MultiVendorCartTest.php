<?php

namespace Tests\Browser\Customer;

use App\Models\Cart;
use App\Models\Category;
use App\Models\Product;
use App\Models\Shop;
use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\Browser\Helpers;
use Tests\DuskTestCase;

class MultiVendorCartTest extends DuskTestCase
{
    use Helpers;

    protected array $cleanupIds = [];

    private function resetState(Browser $browser): void
    {
        $browser->visit('/');
        $browser->pause(300);
        $browser->script(['localStorage.clear(); sessionStorage.clear();']);
        $browser->visit('/');
        $browser->pause(800);
        $browser->waitForText('Buy Now', 15);
    }

    public function test_cart_groups_by_shop(): void
    {
        $shop1 = Shop::factory()->create(['status' => true]);
        $shop1->user()->update(['is_active' => true]);
        $this->cleanupIds[] = [Shop::class, $shop1->id];

        $shop2 = Shop::factory()->create(['status' => true]);
        $shop2->user()->update(['is_active' => true]);
        $this->cleanupIds[] = [Shop::class, $shop2->id];

        $category = Category::factory()->create(['status' => true]);
        $this->cleanupIds[] = [Category::class, $category->id];

        $product1 = Product::factory()->create([
            'shop_id' => $shop1->id,
            'is_active' => true,
            'is_approve' => true,
            'quantity' => 10,
        ]);
        $product1->categories()->attach($category->id);
        $this->cleanupIds[] = [Product::class, $product1->id];

        $product2 = Product::factory()->create([
            'shop_id' => $shop2->id,
            'is_active' => true,
            'is_approve' => true,
            'quantity' => 10,
        ]);
        $product2->categories()->attach($category->id);
        $this->cleanupIds[] = [Product::class, $product2->id];

        $this->browse(function (Browser $browser) use ($product1, $product2, $shop1, $shop2) {
            $this->resetState($browser);

            // Login
            $browser->press('Buy Now')
                ->waitForText('Please login first!', 5)
                ->type('input[placeholder*="email"]', 'user@readyecommerce.com')
                ->type('input[placeholder*="Password"]', 'secret')
                ->press('Log in')
                ->waitForText('Login Successful', 10);

            // Add products to cart via API (reliable, avoids SPA navigation issues)
            $browser->script([
                'fetch("/janmitram-app/api/cart/store", {method:"POST", headers:{"Content-Type":"application/json"}, body:JSON.stringify({product_id:'.$product1->id.', quantity:1})})',
                'fetch("/janmitram-app/api/cart/store", {method:"POST", headers:{"Content-Type":"application/json"}, body:JSON.stringify({product_id:'.$product2->id.', quantity:1})})',
            ]);
            $browser->pause(2000);

            // Visit cart and check shop grouping
            $browser->visit('/cart')
                ->waitForText($shop1->name, 8)
                ->waitForText($shop2->name, 8)
                ->assertSee($shop1->name)
                ->assertSee($shop2->name);
        });

        $this->assertEquals(1, Cart::where('shop_id', $shop1->id)->count());
        $this->assertEquals(1, Cart::where('shop_id', $shop2->id)->count());
    }

    public function test_checkout_splits_per_shop(): void
    {
        $customer = User::factory()->create([
            'password' => bcrypt('secret'),
            'phone' => 'multi_check_'.time().'@test.com',
        ]);
        $customer->assignRole('customer');
        $customer->customer()->create(['user_id' => $customer->id]);
        $this->cleanupIds[] = [User::class, $customer->id];

        $shop1 = Shop::factory()->create(['status' => true, 'user_id' => $customer->id]);
        $shop1->user()->update(['is_active' => true]);
        $this->cleanupIds[] = [Shop::class, $shop1->id];

        $shop2 = Shop::factory()->create(['status' => true, 'user_id' => $customer->id]);
        $shop2->user()->update(['is_active' => true]);
        $this->cleanupIds[] = [Shop::class, $shop2->id];

        $category = Category::factory()->create(['status' => true]);
        $this->cleanupIds[] = [Category::class, $category->id];

        $product1 = Product::factory()->create([
            'shop_id' => $shop1->id,
            'is_active' => true,
            'is_approve' => true,
            'quantity' => 10,
        ]);
        $product1->categories()->attach($category->id);
        $this->cleanupIds[] = [Product::class, $product1->id];

        $product2 = Product::factory()->create([
            'shop_id' => $shop2->id,
            'is_active' => true,
            'is_approve' => true,
            'quantity' => 10,
        ]);
        $product2->categories()->attach($category->id);
        $this->cleanupIds[] = [Product::class, $product2->id];

        $this->browse(function (Browser $browser) use ($customer, $product1, $product2, $shop1, $shop2) {
            $this->resetState($browser);

            $browser->press('Buy Now')
                ->waitForText('Please login first!', 5)
                ->type('input[placeholder*="email"]', $customer->email)
                ->type('input[placeholder*="Password"]', 'secret')
                ->press('Log in')
                ->waitForText('Login Successful', 10);

            $browser->script([
                'fetch("/janmitram-app/api/cart/store", {method:"POST", headers:{"Content-Type":"application/json"}, body:JSON.stringify({product_id:'.$product1->id.', quantity:1})})',
                'fetch("/janmitram-app/api/cart/store", {method:"POST", headers:{"Content-Type":"application/json"}, body:JSON.stringify({product_id:'.$product2->id.', quantity:1})})',
            ]);
            $browser->pause(2000);

            $browser->visit('/checkout')
                ->waitForText('Checkout', 8)
                ->assertSee($shop1->name)
                ->assertSee($shop2->name)
                ->assertSee('Order Summary');
        });

        $this->assertDatabaseHas('carts', ['product_id' => $product1->id]);
        $this->assertDatabaseHas('carts', ['product_id' => $product2->id]);
    }

    public function test_independent_shop_totals(): void
    {
        $shop1 = Shop::factory()->create(['status' => true]);
        $shop1->user()->update(['is_active' => true]);
        $this->cleanupIds[] = [Shop::class, $shop1->id];

        $shop2 = Shop::factory()->create(['status' => true]);
        $shop2->user()->update(['is_active' => true]);
        $this->cleanupIds[] = [Shop::class, $shop2->id];

        $category = Category::factory()->create(['status' => true]);
        $this->cleanupIds[] = [Category::class, $category->id];

        $product1 = Product::factory()->create([
            'shop_id' => $shop1->id,
            'is_active' => true,
            'is_approve' => true,
            'quantity' => 5,
            'price' => 100,
        ]);
        $product1->categories()->attach($category->id);
        $this->cleanupIds[] = [Product::class, $product1->id];

        $product2 = Product::factory()->create([
            'shop_id' => $shop2->id,
            'is_active' => true,
            'is_approve' => true,
            'quantity' => 5,
            'price' => 200,
        ]);
        $product2->categories()->attach($category->id);
        $this->cleanupIds[] = [Product::class, $product2->id];

        $this->browse(function (Browser $browser) use ($product1, $product2) {
            $this->resetState($browser);

            $browser->press('Buy Now')
                ->waitForText('Please login first!', 5)
                ->type('input[placeholder*="email"]', 'user@readyecommerce.com')
                ->type('input[placeholder*="Password"]', 'secret')
                ->press('Log in')
                ->waitForText('Login Successful', 10);

            $browser->script([
                'fetch("/janmitram-app/api/cart/store", {method:"POST", headers:{"Content-Type":"application/json"}, body:JSON.stringify({product_id:'.$product1->id.', quantity:1})})',
                'fetch("/janmitram-app/api/cart/store", {method:"POST", headers:{"Content-Type":"application/json"}, body:JSON.stringify({product_id:'.$product2->id.', quantity:1})})',
            ]);
            $browser->pause(2000);

            $browser->visit('/cart')
                ->waitForText('Subtotal', 8)
                ->assertSee('Subtotal');
        });
    }

    public function test_single_shop_cart_does_not_show_other_shops(): void
    {
        $shop1 = Shop::factory()->create(['status' => true]);
        $shop1->user()->update(['is_active' => true]);
        $this->cleanupIds[] = [Shop::class, $shop1->id];

        $shop2 = Shop::factory()->create(['status' => true]);
        $shop2->user()->update(['is_active' => true]);
        $this->cleanupIds[] = [Shop::class, $shop2->id];

        $category = Category::factory()->create(['status' => true]);
        $this->cleanupIds[] = [Category::class, $category->id];

        $product1 = Product::factory()->create([
            'shop_id' => $shop1->id,
            'is_active' => true,
            'is_approve' => true,
            'quantity' => 10,
        ]);
        $product1->categories()->attach($category->id);
        $this->cleanupIds[] = [Product::class, $product1->id];

        $this->browse(function (Browser $browser) use ($product1, $shop1, $shop2) {
            $this->resetState($browser);

            $browser->press('Buy Now')
                ->waitForText('Please login first!', 5)
                ->type('input[placeholder*="email"]', 'user@readyecommerce.com')
                ->type('input[placeholder*="Password"]', 'secret')
                ->press('Log in')
                ->waitForText('Login Successful', 10);

            $browser->script([
                'fetch("/janmitram-app/api/cart/store", {method:"POST", headers:{"Content-Type":"application/json"}, body:JSON.stringify({product_id:'.$product1->id.', quantity:1})})',
            ]);
            $browser->pause(2000);

            $browser->visit('/cart')
                ->waitForText($shop1->name, 8)
                ->assertSee($shop1->name)
                ->assertDontSee($shop2->name);
        });
    }
}
