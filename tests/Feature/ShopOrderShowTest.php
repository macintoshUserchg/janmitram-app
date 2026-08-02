<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\Address;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ShopOrderShowTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        Role::create(['name' => 'shop']);
        Role::create(['name' => 'customer']);
    }

    private function createOrderForShop(Shop $shop): Order
    {
        $customerUser = User::factory()->create();
        $customerUser->assignRole('customer');
        $customer = Customer::create(['user_id' => $customerUser->id]);
        $address = Address::create([
            'user_id' => $customerUser->id,
            'name' => $this->faker->name,
            'phone' => '1234567890',
            'address' => 'Test Street',
        ]);

        return Order::create([
            'shop_id' => $shop->id,
            'customer_id' => $customer->id,
            'address_id' => $address->id,
            'order_code' => '1001',
            'prefix' => 'ORD',
            'total_amount' => 2500,
            'payable_amount' => 2500,
            'order_status' => OrderStatus::PENDING->value,
            'payment_status' => PaymentStatus::PENDING->value,
            'payment_method' => PaymentMethod::CASH->value,
        ]);
    }

    public function test_shop_order_show_route_reaches_order_detail_page(): void
    {
        $user = User::factory()->create();
        $user->assignRole('shop');
        $shop = Shop::factory()->create(['user_id' => $user->id]);
        $order = $this->createOrderForShop($shop);

        session(['shop' => $shop]);

        // Regression test: numeric id must reach the SHOW route (200 + detail view),
        // not be swallowed by the index route's optional {status} param.
        $response = $this->actingAs($user)->get(route('shop.order.show', $order->id));

        $response->assertOk();
    }

    public function test_shop_order_index_status_filter_still_renders(): void
    {
        $user = User::factory()->create();
        $user->assignRole('shop');
        $shop = Shop::factory()->create(['user_id' => $user->id]);
        $this->createOrderForShop($shop);

        session(['shop' => $shop]);

        // String status segment must STILL reach the index route (not be matched by show's numeric constraint).
        $status = str_replace(' ', '_', OrderStatus::PENDING->value);
        $response = $this->actingAs($user)->get(route('shop.order.index', $status));

        $response->assertOk();
    }
}
