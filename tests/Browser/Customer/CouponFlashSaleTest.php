<?php

namespace Tests\Browser\Customer;

use App\Models\Cart;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\FlashSale;
use App\Models\FlashSaleProduct;
use App\Models\Product;
use App\Models\Shop;
use App\Models\User;
use Carbon\Carbon;
use Laravel\Dusk\Browser;
use Tests\Browser\Helpers;
use Tests\DuskTestCase;

class CouponFlashSaleTest extends DuskTestCase
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

    public function test_valid_coupon_applies(): void
    {
        $customer = User::factory()->create([
            'password' => bcrypt('secret'),
            'phone' => 'coupon_'.time().'@test.com',
        ]);
        $customer->assignRole('customer');
        $customer->customer()->create(['user_id' => $customer->id]);
        $this->cleanupIds[] = [User::class, $customer->id];

        $shop = Shop::factory()->create(['status' => true, 'user_id' => $customer->id]);
        $shop->user()->update(['is_active' => true]);
        $this->cleanupIds[] = [Shop::class, $shop->id];

        $coupon = Coupon::factory()->create([
            'code' => 'SAVE10',
            'type' => 'percentage',
            'discount' => 10,
            'is_active' => 1,
            'min_amount' => 50,
            'started_at' => Carbon::now()->subDay(),
            'expired_at' => Carbon::now()->addDay(),
        ]);
        $this->cleanupIds[] = [Coupon::class, $coupon->id];

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

            // Add to cart via API
            $browser->script([
                'fetch("/janmitram-app/api/cart/store", {method:"POST", headers:{"Content-Type":"application/json"}, body:JSON.stringify({product_id:'.$product->id.', quantity:1})})',
            ]);
            $browser->pause(2000);

            // Visit checkout and try applying coupon
            $browser->visit('/checkout')
                ->waitForText('Checkout', 8)
                ->type('input[placeholder*="coupon" i]', 'SAVE10')
                ->press('Apply')
                ->waitForText('Coupon applied', 5)
                ->assertSee('Coupon applied');
        });

        $this->assertDatabaseHas('carts', ['product_id' => $product->id]);
    }

    public function test_expired_coupon_rejected(): void
    {
        $customer = User::factory()->create([
            'password' => bcrypt('secret'),
            'phone' => 'expired_cpn_'.time().'@test.com',
        ]);
        $customer->assignRole('customer');
        $customer->customer()->create(['user_id' => $customer->id]);
        $this->cleanupIds[] = [User::class, $customer->id];

        $shop = Shop::factory()->create(['status' => true, 'user_id' => $customer->id]);
        $shop->user()->update(['is_active' => true]);
        $this->cleanupIds[] = [Shop::class, $shop->id];

        $coupon = Coupon::factory()->create([
            'code' => 'EXPIRED',
            'type' => 'percentage',
            'discount' => 10,
            'is_active' => 1,
            'min_amount' => 50,
            'started_at' => Carbon::now()->subDays(10),
            'expired_at' => Carbon::now()->subDay(),
        ]);
        $this->cleanupIds[] = [Coupon::class, $coupon->id];

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
                ->waitForText('Checkout', 8)
                ->type('input[placeholder*="coupon" i]', 'EXPIRED')
                ->press('Apply')
                ->waitForText('Coupon not applied', 5)
                ->assertSee('Coupon not applied');
        });
    }

    public function test_flash_sale_price_shown(): void
    {
        $shop = Shop::factory()->create(['status' => true]);
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

        $flashSale = FlashSale::create([
            'name' => 'Flash Sale',
            'start_date' => Carbon::now()->subDay()->toDateString(),
            'end_date' => Carbon::now()->addDay()->toDateString(),
            'start_time' => '00:00:00',
            'end_time' => '23:59:59',
            'status' => 1,
        ]);
        $this->cleanupIds[] = [FlashSale::class, $flashSale->id];

        FlashSaleProduct::create([
            'flash_sale_id' => $flashSale->id,
            'product_id' => $product->id,
            'price' => 50,
            'quantity' => 5,
            'discount' => 50,
            'sale_quantity' => 0,
        ]);

        $this->browse(function (Browser $browser) {
            $this->resetState($browser);

            // Navigate to SPA product detail via clicking on home
            $browser->script([
                'document.querySelector("a[href*=\'/products/\'")?.click() || document.querySelector("a[href*=\'/product/\'")?.click()',
            ]);
            $browser->pause(2000);

            $browser->waitForText('Flash Sale', 8)
                ->assertSee('Flash Sale')
                ->assertSee('50.00');
        });
    }

    public function test_flash_sale_quantity_limit(): void
    {
        $shop = Shop::factory()->create(['status' => true]);
        $shop->user()->update(['is_active' => true]);
        $this->cleanupIds[] = [Shop::class, $shop->id];

        $category = Category::factory()->create(['status' => true]);
        $this->cleanupIds[] = [Category::class, $category->id];

        $product = Product::factory()->create([
            'shop_id' => $shop->id,
            'is_active' => true,
            'is_approve' => true,
            'quantity' => 100,
            'price' => 100,
        ]);
        $product->categories()->attach($category->id);
        $this->cleanupIds[] = [Product::class, $product->id];

        $flashSale = FlashSale::create([
            'name' => 'Limited Flash Sale',
            'start_date' => Carbon::now()->subDay()->toDateString(),
            'end_date' => Carbon::now()->addDay()->toDateString(),
            'start_time' => '00:00:00',
            'end_time' => '23:59:59',
            'status' => 1,
        ]);
        $this->cleanupIds[] = [FlashSale::class, $flashSale->id];

        FlashSaleProduct::create([
            'flash_sale_id' => $flashSale->id,
            'product_id' => $product->id,
            'price' => 50,
            'quantity' => 2,
            'discount' => 50,
            'sale_quantity' => 0,
        ]);

        $this->browse(function (Browser $browser) use ($product) {
            $this->resetState($browser);

            $browser->press('Buy Now')
                ->waitForText('Please login first!', 5)
                ->type('input[placeholder*="email"]', 'user@readyecommerce.com')
                ->type('input[placeholder*="Password"]', 'secret')
                ->press('Log in')
                ->waitForText('Login Successful', 10);

            $browser->script([
                'fetch("/janmitram-app/api/cart/store", {method:"POST", headers:{"Content-Type":"application/json"}, body:JSON.stringify({product_id:'.$product->id.', quantity:1})})',
            ]);
            $browser->pause(2000);

            $browser->visit('/cart')
                ->waitForText('+', 8)
                ->press('+')
                ->waitForText('product quantity increased', 5)
                ->press('+')
                ->waitForText('No more stock', 5)
                ->assertSee('No more stock');
        });
    }

    public function test_invalid_coupon_code_rejected(): void
    {
        $customer = User::factory()->create([
            'password' => bcrypt('secret'),
            'phone' => 'inv_cpn_'.time().'@test.com',
        ]);
        $customer->assignRole('customer');
        $customer->customer()->create(['user_id' => $customer->id]);
        $this->cleanupIds[] = [User::class, $customer->id];

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
                ->waitForText('Checkout', 8)
                ->type('input[placeholder*="coupon" i]', 'INVALID123')
                ->press('Apply')
                ->waitForText('Coupon not applied', 5)
                ->assertSee('Coupon not applied');
        });
    }
}
