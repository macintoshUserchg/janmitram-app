<?php

namespace Tests\Browser\ShopOwner;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\Address;
use App\Models\Coupon;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\Shop;
use App\Models\User;
use App\Models\Withdraw;
use Laravel\Dusk\Browser;
use Tests\Browser\Helpers;
use Tests\DuskTestCase;

class ShopDashboardTest extends DuskTestCase
{
    use Helpers;

    protected Shop $shop;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->shop = Shop::factory()->create();
        $this->user = User::factory()->create([
            'shop_id' => $this->shop->id,
            'is_active' => true,
        ]);
        $this->user->assignRole('shop');

        $this->cleanupIds[] = [Shop::class, $this->shop->id];
        $this->cleanupIds[] = [User::class, $this->user->id];
    }

    public function test_dashboard_loads_with_stats(): void
    {
        $products = Product::factory()->count(5)->create([
            'shop_id' => $this->shop->id,
            'is_active' => true,
            'is_approve' => true,
            'quantity' => 10,
        ]);

        $coupon = Coupon::factory()->create();
        $customer = Customer::factory()->create();
        $address = Address::create([
            'customer_id' => $customer->id,
            'name' => 'Test User',
            'phone' => '1234567890',
            'address_type' => 'Home',
            'road_no' => 'Test Road',
            'house_no' => '123',
            'flat_no' => '1',
            'area' => 'Test Area',
            'address_line' => 'Test Address',
            'address_line2' => '',
            'post_code' => '12345',
            'latitude' => '0',
            'longitude' => '0',
            'is_default' => true,
        ]);

        $orders = collect(range(1, 3))->map(fn () => Order::create([
            'shop_id' => $this->shop->id,
            'customer_id' => $customer->id,
            'coupon_id' => $coupon->id,
            'coupon_discount' => 10,
            'order_code' => rand(1000, 9999),
            'prefix' => 'RC',
            'discount' => 5,
            'pick_date' => now()->format('Y-m-d H:i:s'),
            'delivery_date' => now()->format('Y-m-d H:i:s'),
            'payable_amount' => 100,
            'total_amount' => 100,
            'payment_status' => PaymentStatus::PENDING->value,
            'payment_method' => PaymentMethod::CASH->value,
            'order_status' => OrderStatus::PENDING->value,
            'address_id' => $address->id,
            'delivery_charge' => 10,
        ]));

        $withdraws = collect(range(1, 3))->map(fn () => Withdraw::create([
            'shop_id' => $this->shop->id,
            'user_id' => $this->user->id,
            'amount' => 100,
            'status' => 'pending',
            'withdraw_method' => 'bank',
            'contact_number' => '1234567890',
            'name' => 'Test Withdraw',
        ]));

        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                ->visit('/shop/dashboard')
                ->waitForText('Welcome Back', 10)
                ->assertSee('Total Products')
                ->assertSee('Total Orders')
                ->assertSee('Total Categories')
                ->assertSee('Total Brands')
                ->assertSee('Shop Wallet')
                ->assertSee('Pending Withdraw')
                ->assertSee('Already Withdraw')
                ->assertSee('Rejected Withdraw')
                ->assertSee('Total Withdraw')
                ->assertSee('Delivery Charge Collected')
                ->assertSee('Total Pos Sales')
                ->assertSee('Order Summary')
                ->assertSee('Top Selling Products')
                ->assertSee('Top Rating Products')
                ->assertSee('Most Favorite Products');
        });

        foreach ($products as $product) {
            $this->cleanupIds[] = [Product::class, $product->id];
        }
        foreach ($orders as $order) {
            $this->cleanupIds[] = [Order::class, $order->id];
        }
        foreach ($withdraws as $withdraw) {
            $this->cleanupIds[] = [Withdraw::class, $withdraw->id];
        }
        $this->cleanupIds[] = [Coupon::class, $coupon->id];
        $this->cleanupIds[] = [Customer::class, $customer->id];
        $this->cleanupIds[] = [Address::class, $address->id];
    }

    public function test_recent_orders_widget(): void
    {
        $coupon = Coupon::factory()->create();
        $customer = Customer::factory()->create();
        $address = Address::create([
            'customer_id' => $customer->id,
            'name' => 'Test User',
            'phone' => '1234567890',
            'address_type' => 'Home',
            'road_no' => 'Test Road',
            'house_no' => '123',
            'flat_no' => '1',
            'area' => 'Test Area',
            'address_line' => 'Test Address',
            'address_line2' => '',
            'post_code' => '12345',
            'latitude' => '0',
            'longitude' => '0',
            'is_default' => true,
        ]);

        $orders = collect(range(1, 8))->map(fn () => Order::create([
            'shop_id' => $this->shop->id,
            'customer_id' => $customer->id,
            'coupon_id' => $coupon->id,
            'coupon_discount' => 10,
            'order_code' => rand(1000, 9999),
            'prefix' => 'RC',
            'discount' => 5,
            'pick_date' => now()->format('Y-m-d H:i:s'),
            'delivery_date' => now()->format('Y-m-d H:i:s'),
            'payable_amount' => 100,
            'total_amount' => 100,
            'payment_status' => PaymentStatus::PENDING->value,
            'payment_method' => PaymentMethod::CASH->value,
            'order_status' => OrderStatus::PENDING->value,
            'address_id' => $address->id,
            'delivery_charge' => 10,
        ]));

        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                ->visit('/shop/dashboard')
                ->waitForText('Order Summary', 10);

            // Verify the table shows the expected number of order rows
            $browser->assertSee('Order ID');
            $orderRows = $browser->elements('.table.dashboard tbody tr');
            $this->assertCount(8, $orderRows);
        });

        foreach ($orders as $order) {
            $this->cleanupIds[] = [Order::class, $order->id];
        }
        $this->cleanupIds[] = [Coupon::class, $coupon->id];
        $this->cleanupIds[] = [Customer::class, $customer->id];
        $this->cleanupIds[] = [Address::class, $address->id];
    }

    public function test_revenue_widget(): void
    {
        $this->user->wallet()->updateOrCreate(
            ['user_id' => $this->user->id],
            ['balance' => 5000.00]
        );

        $withdraws = collect([
            Withdraw::create([
                'shop_id' => $this->shop->id,
                'user_id' => $this->user->id,
                'amount' => 500,
                'status' => 'pending',
                'withdraw_method' => 'bank',
                'contact_number' => '1234567890',
                'name' => 'Test Withdraw',
            ]),
            Withdraw::create([
                'shop_id' => $this->shop->id,
                'user_id' => $this->user->id,
                'amount' => 1000,
                'status' => 'approved',
                'withdraw_method' => 'bank',
                'contact_number' => '1234567890',
                'name' => 'Test Withdraw',
            ]),
        ]);

        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                ->visit('/shop/dashboard')
                ->waitForText('Shop Wallet', 10)
                // showCurrency() prepends currency symbol without number formatting
                ->assertSee('$5000')
                ->assertSee('$500')
                ->assertSee('$1000')
                ->assertSee('$1500');
        });

        foreach ($withdraws as $withdraw) {
            $this->cleanupIds[] = [Withdraw::class, $withdraw->id];
        }
    }

    public function test_pending_approvals_widget(): void
    {
        $coupon = Coupon::factory()->create();
        $customer = Customer::factory()->create();
        $address = Address::create([
            'customer_id' => $customer->id,
            'name' => 'Test User',
            'phone' => '1234567890',
            'address_type' => 'Home',
            'road_no' => 'Test Road',
            'house_no' => '123',
            'flat_no' => '1',
            'area' => 'Test Area',
            'address_line' => 'Test Address',
            'address_line2' => '',
            'post_code' => '12345',
            'latitude' => '0',
            'longitude' => '0',
            'is_default' => true,
        ]);

        $orders = collect(range(1, 4))->map(fn () => Order::create([
            'shop_id' => $this->shop->id,
            'customer_id' => $customer->id,
            'coupon_id' => $coupon->id,
            'coupon_discount' => 10,
            'order_code' => rand(1000, 9999),
            'prefix' => 'RC',
            'discount' => 5,
            'pick_date' => now()->format('Y-m-d H:i:s'),
            'delivery_date' => now()->format('Y-m-d H:i:s'),
            'payable_amount' => 100,
            'total_amount' => 100,
            'payment_status' => PaymentStatus::PENDING->value,
            'payment_method' => PaymentMethod::CASH->value,
            'order_status' => OrderStatus::PENDING->value,
            'address_id' => $address->id,
            'delivery_charge' => 10,
        ]));

        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                ->visit('/shop/dashboard')
                ->waitForText('Order Analytics', 10)
                ->assertSee('Pending')
                ->assertSee('Confirm')
                ->assertSee('Processing')
                ->assertSee('Delivered')
                ->assertSee('Cancelled');
        });

        foreach ($orders as $order) {
            $this->cleanupIds[] = [Order::class, $order->id];
        }
        $this->cleanupIds[] = [Coupon::class, $coupon->id];
        $this->cleanupIds[] = [Customer::class, $customer->id];
        $this->cleanupIds[] = [Address::class, $address->id];
    }

    public function test_dashboard_handles_empty_data_gracefully(): void
    {
        // No products, orders, or withdraws seeded — dashboard must still render.
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                ->visit('/shop/dashboard')
                ->waitForText('Welcome Back', 10)
                ->assertSee('Total Products')
                ->assertSee('Total Orders')
                ->assertSee('Shop Wallet')
                ->assertSee('Order Summary');
        });
    }

    public function test_negative_dashboard_no_shop_access(): void
    {
        $otherShop = Shop::factory()->create();
        $otherUser = User::factory()->create([
            'shop_id' => $otherShop->id,
            'is_active' => true,
        ]);
        $otherUser->assignRole('shop');
        $this->cleanupIds[] = [User::class, $otherUser->id];
        $this->cleanupIds[] = [Shop::class, $otherShop->id];

        $this->browse(function (Browser $browser) use ($otherUser) {
            $browser->loginAs($otherUser)
                ->visit('/shop/dashboard')
                ->waitForText('Welcome Back', 10)
                ->assertSee('Total Products')
                ->assertSee('Total Orders');
        });
    }
}
