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

class CartCheckoutTest extends DuskTestCase
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

    private function createTestProduct(): Product
    {
        $category = Category::factory()->create(['status' => true]);
        $this->cleanupIds[] = [Category::class, $category->id];

        $shop = Shop::factory()->create(['status' => true]);
        $shop->user()->update(['is_active' => true]);
        $this->cleanupIds[] = [Shop::class, $shop->id];

        $product = Product::factory()->create([
            'shop_id' => $shop->id,
            'is_active' => true,
            'is_approve' => true,
            'quantity' => 10,
        ]);
        $this->cleanupIds[] = [Product::class, $product->id];

        $product->categories()->attach($category->id);

        return $product;
    }

    private function createTestCustomer(): User
    {
        $customer = User::factory()->create([
            'email' => 'test'.time().rand(1000, 9999).'@gmail.com',
            'password' => bcrypt('secret'),
        ]);
        $customer->assignRole('customer');
        $customer->customer()->create(['user_id' => $customer->id]);
        $this->cleanupIds[] = [User::class, $customer->id];

        return $customer;
    }

    private function apiAddToCart(Browser $browser, Product $product, int $quantity = 1, bool $buyNow = false): void
    {
        $browser->script([
            'fetch("/janmitram-app/api/cart/store", {method:"POST", headers:{"Content-Type":"application/json"}, body:JSON.stringify({product_id:'.$product->id.', quantity:'.$quantity.', is_buy_now:'.($buyNow ? 'true' : 'false').'})})',
        ]);
        $browser->pause(2000);
    }

    public function test_guest_buy_now_opens_login_modal(): void
    {
        $product = $this->createTestProduct();

        $this->browse(function (Browser $browser) {
            $this->resetState($browser);
            $browser->press('Buy Now')
                ->waitForText('Please login first!', 5)
                ->assertSee('Please login first!')
                ->assertVisible('input[placeholder*="email"]')
                ->assertVisible('input[placeholder*="Password"]');
        });
    }

    public function test_guest_login_then_add_to_cart(): void
    {
        $product = $this->createTestProduct();
        $customer = $this->createTestCustomer();

        $this->browse(function (Browser $browser) use ($product, $customer) {
            $this->resetState($browser);

            // Login via modal
            $browser->press('Buy Now')
                ->waitForText('Please login first!', 5)
                ->type('input[placeholder*="email"]', $customer->email)
                ->type('input[placeholder*="Password"]', 'secret')
                ->press('Log in')
                ->waitForText('Login Successful', 10);

            // Add product to cart via API
            $browser->script([
                'fetch("/janmitram-app/api/cart/store", {method:"POST", headers:{"Content-Type":"application/json"}, body:JSON.stringify({product_id:'.$product->id.', quantity:1})})',
            ]);
            $browser->pause(2000);

            // Verify cart in SPA
            $browser->visit('/cart')
                ->waitForText($product->name, 8)
                ->assertSee($product->name);

            // Verify checkout in SPA
            $browser->visit('/checkout')
                ->waitForText('Checkout', 8)
                ->assertSee('Checkout');
        });

        $this->assertDatabaseHas('carts', ['product_id' => $product->id, 'customer_id' => $customer->customer->id]);
    }

    public function test_logged_in_customer_add_to_cart(): void
    {
        $customer = $this->createTestCustomer();
        $product = $this->createTestProduct();

        $this->browse(function (Browser $browser) use ($customer, $product) {
            $this->resetState($browser);

            $browser->press('Buy Now')
                ->waitForText('Please login first!', 5)
                ->type('input[placeholder*="email"]', $customer->email)
                ->type('input[placeholder*="Password"]', 'secret')
                ->press('Log in')
                ->waitForText('Login Successful', 10);

            // Add product to cart via API
            $browser->script([
                'fetch("/janmitram-app/api/cart/store", {method:"POST", headers:{"Content-Type":"application/json"}, body:JSON.stringify({product_id:'.$product->id.', quantity:1})})',
            ]);
            $browser->pause(2000);

            $browser->visit('/cart')
                ->waitForText($product->name, 8)
                ->assertSee($product->name);
        });

        $this->assertDatabaseHas('carts', [
            'product_id' => $product->id,
            'customer_id' => $customer->customer->id,
        ]);
    }

    public function test_increment_decrement_cart_quantity(): void
    {
        $customer = $this->createTestCustomer();
        $product = $this->createTestProduct();

        $this->browse(function (Browser $browser) use ($customer, $product) {
            $this->resetState($browser);

            $browser->press('Buy Now')
                ->waitForText('Please login first!', 5)
                ->type('input[placeholder*="email"]', $customer->email)
                ->type('input[placeholder*="Password"]', 'secret')
                ->press('Log in')
                ->waitForText('Login Successful', 10);

            // Add to cart via API
            $browser->script([
                'fetch("/janmitram-app/api/cart/store", {method:"POST", headers:{"Content-Type":"application/json"}, body:JSON.stringify({product_id:'.$product->id.', quantity:1})})',
            ]);
            $browser->pause(2000);

            // Increment
            $browser->script([
                'fetch("/janmitram-app/api/cart/increment", {method:"POST", headers:{"Content-Type":"application/json"}, body:JSON.stringify({product_id:'.$product->id.'})})',
            ]);
            $browser->pause(1000);

            // Verify quantity in SPA cart
            $browser->visit('/cart')
                ->waitForText($product->name, 8)
                ->waitForText('2', 8);
        });

        $cart = Cart::where('product_id', $product->id)->first();
        $this->assertNotNull($cart);
        $this->assertEquals(2, $cart->quantity);
    }

    public function test_remove_from_cart(): void
    {
        $customer = $this->createTestCustomer();
        $product = $this->createTestProduct();

        $this->browse(function (Browser $browser) use ($customer, $product) {
            $this->resetState($browser);

            $browser->press('Buy Now')
                ->waitForText('Please login first!', 5)
                ->type('input[placeholder*="email"]', $customer->email)
                ->type('input[placeholder*="Password"]', 'secret')
                ->press('Log in')
                ->waitForText('Login Successful', 10);

            // Add to cart via API
            $browser->script([
                'fetch("/janmitram-app/api/cart/store", {method:"POST", headers:{"Content-Type":"application/json"}, body:JSON.stringify({product_id:'.$product->id.', quantity:1})})',
            ]);
            $browser->pause(2000);

            // Remove from cart via API
            $browser->script([
                'fetch("/janmitram-app/api/cart/delete", {method:"POST", headers:{"Content-Type":"application/json"}, body:JSON.stringify({product_id:'.$product->id.'})})',
            ]);
            $browser->pause(1000);

            // Verify removed in SPA cart
            $browser->visit('/cart')
                ->waitForText('Cart is empty', 8);
        });

        $this->assertDatabaseMissing('carts', ['product_id' => $product->id]);
    }

    public function test_buy_now_flow(): void
    {
        $customer = $this->createTestCustomer();
        $product = $this->createTestProduct();

        $this->browse(function (Browser $browser) use ($customer, $product) {
            $this->resetState($browser);

            $browser->press('Buy Now')
                ->waitForText('Please login first!', 5)
                ->type('input[placeholder*="email"]', $customer->email)
                ->type('input[placeholder*="Password"]', 'secret')
                ->press('Log in')
                ->waitForText('Login Successful', 10);

            // Use Buy Now via API
            $browser->script([
                'fetch("/janmitram-app/api/cart/store", {method:"POST", headers:{"Content-Type":"application/json"}, body:JSON.stringify({product_id:'.$product->id.', quantity:1, is_buy_now:true})})',
            ]);
            $browser->pause(2000);

            // Verify buy now
            $browser->visit('/buynow')
                ->waitForText('Checkout', 8)
                ->assertSee('Checkout');
        });

        $this->assertDatabaseHas('carts', [
            'product_id' => $product->id,
            'is_buy_now' => true,
        ]);
    }

    public function test_checkout_summary_shows_totals(): void
    {
        $customer = $this->createTestCustomer();
        $shop = Shop::factory()->create(['status' => true, 'user_id' => $customer->id]);
        $shop->user()->update(['is_active' => true]);
        $this->cleanupIds[] = [Shop::class, $shop->id];

        $category = Category::factory()->create(['status' => true]);
        $this->cleanupIds[] = [Category::class, $category->id];

        $product = Product::factory()->create([
            'shop_id' => $shop->id,
            'is_active' => true,
            'is_approve' => true,
            'quantity' => 10,
            'price' => 100,
        ]);
        $product->categories()->attach($category->id);
        $this->cleanupIds[] = [Product::class, $product->id];

        $this->browse(function (Browser $browser) use ($customer, $product) {
            $this->resetState($browser);

            $browser->press('Buy Now')
                ->waitForText('Please login first!', 5)
                ->type('input[placeholder*="email"]', $customer->email)
                ->type('input[placeholder*="Password"]', 'secret')
                ->press('Log in')
                ->waitForText('Login Successful', 10);

            $browser->script([
                'fetch("/janmitram-app/api/cart/store", {method:"POST", headers:{"Content-Type":"application/json"}, body:JSON.stringify({product_id:'.$product->id.', quantity:1})})',
            ]);
            $browser->pause(2000);

            $browser->visit('/checkout')
                ->waitForText('Order Summary', 8)
                ->assertSee('Order Summary')
                ->assertSee('Cash on delivery');
        });
    }
}
