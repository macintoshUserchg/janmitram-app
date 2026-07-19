<?php

namespace Tests\Browser\Customer;

use App\Models\Address;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Product;
use App\Models\Shop;
use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\Browser\Helpers;
use Tests\DuskTestCase;

class UserDashboardTest extends DuskTestCase
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

    public function test_order_history_list(): void
    {
        $customer = User::factory()->create([
            'password' => bcrypt('secret'),
            'phone' => 'order_hist_'.time().'@test.com',
        ]);
        $customer->assignRole('customer');
        $customer->customer()->create(['user_id' => $customer->id]);
        $this->cleanupIds[] = [User::class, $customer->id];

        $shop = Shop::factory()->create(['status' => true]);
        $shop->user()->update(['is_active' => true]);
        $this->cleanupIds[] = [Shop::class, $shop->id];

        $address = Address::factory()->create([
            'customer_id' => $customer->customer->id,
        ]);
        $this->cleanupIds[] = [Address::class, $address->id];

        $order = Order::factory()->create([
            'customer_id' => $customer->customer->id,
            'shop_id' => $shop->id,
            'address_id' => $address->id,
        ]);
        $this->cleanupIds[] = [Order::class, $order->id];

        $this->browse(function (Browser $browser) use ($order, $customer) {
            $this->resetState($browser);
            // Login
            $browser->press('Buy Now')
                ->waitForText('Please login first!', 5)
                ->type('input[placeholder*="email"]', $customer->email)
                ->type('input[placeholder*="Password"]', 'secret')
                ->press('Log in')
                ->waitForText('Login Successful', 10);

            $browser->visit('/order-history')
                ->waitForText('Order History', 8)
                ->assertSee($order->order_code);
        });

        $this->assertDatabaseHas('orders', ['id' => $order->id]);
    }

    public function test_order_detail_view(): void
    {
        $customer = User::factory()->create([
            'password' => bcrypt('secret'),
            'phone' => 'order_det_'.time().'@test.com',
        ]);
        $customer->assignRole('customer');
        $customer->customer()->create(['user_id' => $customer->id]);
        $this->cleanupIds[] = [User::class, $customer->id];

        $shop = Shop::factory()->create(['status' => true]);
        $shop->user()->update(['is_active' => true]);
        $this->cleanupIds[] = [Shop::class, $shop->id];

        $address = Address::factory()->create([
            'customer_id' => $customer->customer->id,
        ]);
        $this->cleanupIds[] = [Address::class, $address->id];

        $order = Order::factory()->create([
            'customer_id' => $customer->customer->id,
            'shop_id' => $shop->id,
            'address_id' => $address->id,
            'order_code' => 'UHT-12345',
        ]);
        $this->cleanupIds[] = [Order::class, $order->id];

        $this->browse(function (Browser $browser) use ($order, $customer) {
            $this->resetState($browser);
            $browser->press('Buy Now')
                ->waitForText('Please login first!', 5)
                ->type('input[placeholder*="email"]', $customer->email)
                ->type('input[placeholder*="Password"]', 'secret')
                ->press('Log in')
                ->waitForText('Login Successful', 10);

            $browser->visit("/order-history/{$order->id}")
                ->waitForText('Order Details', 8)
                ->assertSee('UHT-12345');
        });

        $this->assertDatabaseHas('orders', ['id' => $order->id]);
    }

    public function test_profile_update(): void
    {
        $customer = User::factory()->create([
            'password' => bcrypt('secret'),
            'name' => 'Old Name',
            'phone' => 'profile_'.time().'@test.com',
        ]);
        $customer->assignRole('customer');
        $this->cleanupIds[] = [User::class, $customer->id];

        $this->browse(function (Browser $browser) use ($customer) {
            $this->resetState($browser);
            $browser->press('Buy Now')
                ->waitForText('Please login first!', 5)
                ->type('input[placeholder*="email"]', $customer->email)
                ->type('input[placeholder*="Password"]', 'secret')
                ->press('Log in')
                ->waitForText('Login Successful', 10);

            $browser->visit('/profile')
                ->waitForText('My Profile', 8)
                ->type('input[placeholder*="Enter"]', 'New Name')
                ->press('Save')
                ->waitForText('Profile updated successfully', 8)
                ->assertSee('Profile updated successfully');
        });

        $this->assertEquals('New Name', $customer->fresh()->name);
    }

    public function test_password_change(): void
    {
        $customer = User::factory()->create([
            'password' => bcrypt('secret'),
            'phone' => 'pwd_'.time().'@test.com',
        ]);
        $customer->assignRole('customer');
        $this->cleanupIds[] = [User::class, $customer->id];

        $this->browse(function (Browser $browser) {
            $this->resetState($browser);
            $browser->press('Buy Now')
                ->waitForText('Please login first!', 5)
                ->type('input[placeholder*="email"]', 'user@readyecommerce.com')
                ->type('input[placeholder*="Password"]', 'secret')
                ->press('Log in')
                ->waitForText('Login Successful', 10);

            $browser->visit('/change-password')
                ->waitForText('Change Password', 8)
                ->type('input[name="current_password"]', 'secret')
                ->type('input[name="new_password"]', 'newpassword123')
                ->type('input[name="new_password_confirmation"]', 'newpassword123')
                ->press('Update Password')
                ->waitForText('Password updated successfully', 8)
                ->assertSee('Password updated successfully');
        });
    }

    public function test_reorder_from_history(): void
    {
        $customer = User::factory()->create([
            'password' => bcrypt('secret'),
            'phone' => 'reorder_'.time().'@test.com',
        ]);
        $customer->assignRole('customer');
        $customer->customer()->create(['user_id' => $customer->id]);
        $this->cleanupIds[] = [User::class, $customer->id];

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
        ]);
        $product->categories()->attach($category->id);
        $this->cleanupIds[] = [Product::class, $product->id];

        $address = Address::factory()->create([
            'customer_id' => $customer->customer->id,
        ]);
        $this->cleanupIds[] = [Address::class, $address->id];

        $order = Order::factory()->create([
            'customer_id' => $customer->customer->id,
            'shop_id' => $shop->id,
            'address_id' => $address->id,
        ]);
        $this->cleanupIds[] = [Order::class, $order->id];

        OrderProduct::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => 1,
            'price' => $product->price,
            'buying_price' => $product->buy_price,
        ]);

        $this->browse(function (Browser $browser) use ($order, $product, $customer) {
            $this->resetState($browser);
            $browser->press('Buy Now')
                ->waitForText('Please login first!', 5)
                ->type('input[placeholder*="email"]', $customer->email)
                ->type('input[placeholder*="Password"]', 'secret')
                ->press('Log in')
                ->waitForText('Login Successful', 10);

            $browser->visit("/order-history/{$order->id}")
                ->waitForText('Order Details', 8)
                ->press('Reorder')
                ->waitForText('product added to cart', 8)
                ->visit('/cart')
                ->waitForText($product->name, 8)
                ->assertSee($product->name);
        });

        $this->assertDatabaseHas('carts', ['product_id' => $product->id]);
    }
}
