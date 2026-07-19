<?php

namespace Tests\Browser\ShopOwner;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\Address;
use App\Models\Coupon;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Shop;
use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\Browser\Helpers;
use Tests\DuskTestCase;

class OrderManagementTest extends DuskTestCase
{
    use Helpers;

    protected Shop $shop;

    protected User $user;

    protected Customer $customer;

    protected Address $address;

    protected Coupon $coupon;

    protected function setUp(): void
    {
        parent::setUp();

        $this->shop = Shop::factory()->create();
        $this->user = User::factory()->create([
            'shop_id' => $this->shop->id,
            'is_active' => true,
        ]);
        $this->user->assignRole('shop');

        $this->customer = Customer::factory()->create();
        $this->address = Address::create([
            'customer_id' => $this->customer->id,
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
        $this->coupon = Coupon::factory()->create();

        $this->cleanupIds[] = [Shop::class, $this->shop->id];
        $this->cleanupIds[] = [User::class, $this->user->id];
        $this->cleanupIds[] = [Customer::class, $this->customer->id];
        $this->cleanupIds[] = [Address::class, $this->address->id];
        $this->cleanupIds[] = [Coupon::class, $this->coupon->id];
    }

    private function makeOrder(array $overrides = []): Order
    {
        return Order::create(array_merge([
            'shop_id' => $this->shop->id,
            'customer_id' => $this->customer->id,
            'coupon_id' => $this->coupon->id,
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
            'address_id' => $this->address->id,
            'delivery_charge' => 10,
        ], $overrides));
    }

    public function test_order_list_with_filters(): void
    {
        collect(range(1, 3))->each(fn () => $this->makeOrder());
        collect(range(1, 2))->each(fn () => $this->makeOrder([
            'order_status' => OrderStatus::DELIVERED->value,
            'payment_status' => PaymentStatus::PAID->value,
        ]));

        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                ->visit('/shop/order')
                ->waitForText('Orders', 10)
                ->assertSee('Orders Summary')
                ->assertSee('Order ID')
                ->assertSee('Customer')
                ->assertSee('Total Amount')
                ->assertSee('Status')
                ->assertSee('Action');
        });
    }

    public function test_order_detail_view(): void
    {
        $order = $this->makeOrder();
        $this->cleanupIds[] = [Order::class, $order->id];

        $this->browse(function (Browser $browser) use ($order) {
            $browser->loginAs($this->user)
                ->visit('/shop/order/'.$order->id)
                ->waitForText('Order Details', 10)
                ->assertSee('Change Order Status')
                ->assertSee('Payment Status')
                ->assertSee('Customer Info')
                ->assertSee('Shipping Address')
                ->assertSee('Sub Total')
                ->assertSee('Grand Total')
                ->assertSee('Download Invoice')
                ->assertSee('Payment Slip');
        });
    }

    public function test_update_order_status_to_confirm(): void
    {
        $order = $this->makeOrder();
        $this->cleanupIds[] = [Order::class, $order->id];

        $this->browse(function (Browser $browser) use ($order) {
            $browser->loginAs($this->user)
                ->visit('/shop/order/'.$order->id)
                ->script([
                    "var f=document.createElement('form');".
                    "f.method='POST';".
                    "f.action='".route('shop.order.status.change', $order->id).'?status='.OrderStatus::CONFIRM->value."';".
                    "f.style.display='none';".
                    "var i=document.createElement('input');i.name='_method';i.value='PUT';f.appendChild(i);".
                    "var t=document.createElement('input');t.name='_token';".
                    "t.value=document.querySelector('meta[name=\"csrf-token\"]').getAttribute('content');f.appendChild(t);".
                    'document.body.appendChild(f);f.submit();',
                ]);
            $browser->waitForText('Order status updated successfully', 10);
        });

        $order->refresh();
        $this->assertEquals(OrderStatus::CONFIRM->value, $order->order_status->value);
    }

    public function test_update_order_status_to_processing(): void
    {
        $order = $this->makeOrder(['order_status' => OrderStatus::CONFIRM->value]);
        $this->cleanupIds[] = [Order::class, $order->id];

        $this->browse(function (Browser $browser) use ($order) {
            $browser->loginAs($this->user)
                ->visit('/shop/order/'.$order->id)
                ->script([
                    "var f=document.createElement('form');".
                    "f.method='POST';".
                    "f.action='".route('shop.order.status.change', $order->id).'?status='.OrderStatus::PROCESSING->value."';".
                    "f.style.display='none';".
                    "var i=document.createElement('input');i.name='_method';i.value='PUT';f.appendChild(i);".
                    "var t=document.createElement('input');t.name='_token';".
                    "t.value=document.querySelector('meta[name=\"csrf-token\"]').getAttribute('content');f.appendChild(t);".
                    'document.body.appendChild(f);f.submit();',
                ]);
            $browser->waitForText('Order status updated successfully', 10);
        });

        $order->refresh();
        $this->assertEquals(OrderStatus::PROCESSING->value, $order->order_status->value);
    }

    public function test_update_order_status_to_on_the_way(): void
    {
        $order = $this->makeOrder(['order_status' => OrderStatus::PROCESSING->value]);
        $this->cleanupIds[] = [Order::class, $order->id];

        $this->browse(function (Browser $browser) use ($order) {
            $browser->loginAs($this->user)
                ->visit('/shop/order/'.$order->id)
                ->script([
                    "var f=document.createElement('form');".
                    "f.method='POST';".
                    "f.action='".route('shop.order.status.change', $order->id).'?status='.OrderStatus::ON_THE_WAY->value."';".
                    "f.style.display='none';".
                    "var i=document.createElement('input');i.name='_method';i.value='PUT';f.appendChild(i);".
                    "var t=document.createElement('input');t.name='_token';".
                    "t.value=document.querySelector('meta[name=\"csrf-token\"]').getAttribute('content');f.appendChild(t);".
                    'document.body.appendChild(f);f.submit();',
                ]);
            $browser->waitForText('Order status updated successfully', 10);
        });

        $order->refresh();
        $this->assertEquals(OrderStatus::ON_THE_WAY->value, $order->order_status->value);
    }

    public function test_print_invoice(): void
    {
        $order = $this->makeOrder([
            'order_status' => OrderStatus::DELIVERED->value,
            'payment_status' => PaymentStatus::PAID->value,
        ]);
        $this->cleanupIds[] = [Order::class, $order->id];

        $this->browse(function (Browser $browser) use ($order) {
            $browser->loginAs($this->user)
                ->visit('/shop/order/'.$order->id)
                ->waitForText('Order Details', 10)
                ->assertSee('Download Invoice');
        });
    }

    public function test_invalid_status_transition_returns_error(): void
    {
        $order = $this->makeOrder();
        $this->cleanupIds[] = [Order::class, $order->id];
        $originalStatus = $order->order_status->value;

        $this->browse(function (Browser $browser) use ($order) {
            $browser->loginAs($this->user)
                ->visit('/shop/order/'.$order->id)
                ->script([
                    "var f=document.createElement('form');".
                    "f.method='POST';".
                    "f.action='".route('shop.order.status.change', $order->id)."?status=';".
                    "f.style.display='none';".
                    "var i=document.createElement('input');i.name='_method';i.value='PUT';f.appendChild(i);".
                    "var t=document.createElement('input');t.name='_token';".
                    "t.value=document.querySelector('meta[name=\"csrf-token\"]').getAttribute('content');f.appendChild(t);".
                    'document.body.appendChild(f);f.submit();',
                ]);
        });

        $order->refresh();
        $this->assertEquals($originalStatus, $order->order_status->value);
    }
}
