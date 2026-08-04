<?php

namespace Tests\Feature;

use App\Enums\PaymentMethod;
use App\Exceptions\UnfulfillableOrderException;
use App\Http\Requests\OrderRequest;
use App\Models\Address;
use App\Models\Area;
use App\Models\Brand;
use App\Models\Cart;
use App\Models\Customer;
use App\Models\GeneraleSetting;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Shop;
use App\Models\Unit;
use App\Models\User;
use App\Repositories\OrderRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ShopAllocationTest extends TestCase
{
    use RefreshDatabase;

    public function test_haversine_km_returns_expected_distance(): void
    {
        $this->assertSame(0.0, haversineKm(26.9, 75.8, 26.9, 75.8));

        // Delhi (28.6139, 77.2090) -> Jaipur (26.9124, 75.7873) ≈ 239 km
        $km = haversineKm(28.6139, 77.2090, 26.9124, 75.7873);
        $this->assertEqualsWithDelta(239.0, $km, 5.0);
    }

    private function masterWithTwoCopies(): array
    {
        $nearShop = Shop::factory()->create(['latitude' => 26.91, 'longitude' => 75.79, 'delivery_charge' => 20]);
        $farShop = Shop::factory()->create(['latitude' => 28.61, 'longitude' => 77.21, 'delivery_charge' => 80]);
        // shops factory must have an active user; ensure is_active on both users
        $nearShop->user->update(['is_active' => true]);
        $farShop->user->update(['is_active' => true]);

        // Factory prerequisites under RefreshDatabase (no seeders run):
        // ProductFactory needs a Brand row (Brand::all()->random()) and a Unit
        // (FK-constrained unit_id); AddressFactory needs a Customer (which needs
        // the 'customer' role) and an Area.
        Role::create(['name' => 'customer']);
        Customer::factory()->create();
        Area::factory()->create();
        Brand::create(['name' => 'Test Brand', 'slug' => 'test-brand']);
        $unit = Unit::create(['name' => 'kg', 'shop_id' => $nearShop->id, 'is_active' => true]);

        $master = Product::factory()->create([
            'shop_id' => $nearShop->id,
            'unit_id' => $unit->id,
            'quantity' => 10,
            'is_active' => true,
            'is_approve' => true,
        ]);
        $copy = Product::factory()->create([
            'shop_id' => $farShop->id,
            'master_product_id' => $master->id,
            'unit_id' => $unit->id,
            'quantity' => 10,
            'is_active' => true,
            'is_approve' => true,
        ]);

        return [$master, $copy, $nearShop, $farShop];
    }

    public function test_candidate_shops_are_ranked_by_distance(): void
    {
        [$master, $copy, $nearShop, $farShop] = $this->masterWithTwoCopies();
        $address = Address::factory()->create(['latitude' => 26.9, 'longitude' => 75.8]);

        $candidates = OrderRepository::candidateShopsForLine($master, 2, $address);

        $this->assertCount(2, $candidates);
        $this->assertSame($nearShop->id, $candidates[0]->shop_id);
        $this->assertSame($farShop->id, $candidates[1]->shop_id);
        $this->assertTrue($candidates[0]->radius_eligible);
        $this->assertFalse($candidates[1]->radius_eligible);
        $this->assertSame(20.0, $candidates[0]->delivery_charge);
    }

    public function test_allocate_nearest_shop_picks_in_radius_copy(): void
    {
        [$master, $copy, $nearShop, $farShop] = $this->masterWithTwoCopies();
        $address = Address::factory()->create(['latitude' => 26.9, 'longitude' => 75.8]);

        $allocated = $this->invokePrivate('allocateNearestShop', [$master, 2, $address]);

        $this->assertNotNull($allocated);
        $this->assertSame($nearShop->id, $allocated->shop_id);
    }

    public function test_allocate_honours_override_pick(): void
    {
        [$master, $copy, $nearShop, $farShop] = $this->masterWithTwoCopies();
        $address = Address::factory()->create(['latitude' => 26.9, 'longitude' => 75.8]);

        $allocated = $this->invokePrivate('allocateNearestShop', [$master, 2, $address, $farShop->id]);

        $this->assertNotNull($allocated);
        $this->assertSame($farShop->id, $allocated->shop_id);
    }

    public function test_order_goes_to_allocated_shop_and_uses_its_delivery_charge(): void
    {
        [$master, $copy, $nearShop, $farShop] = $this->masterWithTwoCopies();
        $this->enableMultiVendor();
        $customerUser = User::factory()->create();
        $customer = Customer::factory()->create(['user_id' => $customerUser->id]);
        $address = Address::factory()->create([
            'customer_id' => $customer->id,
            'latitude' => 26.9,
            'longitude' => 75.8,
        ]);
        Cart::create([
            'customer_id' => $customer->id,
            'shop_id' => $farShop->id,   // pinned to the FAR shop at add-to-cart
            'product_id' => $master->id,
            'quantity' => 2,
        ]);

        $payment = $this->placeOrder($customerUser, $address, $nearShop->id);

        $order = $payment->orders->first();
        $this->assertNotNull($order);
        $this->assertSame($nearShop->id, $order->shop_id);          // allocated to NEAR shop
        $this->assertSame(20.0, (float) $order->delivery_charge);   // near shop's charge
        $this->assertSame(2, (int) $order->products->first()->pivot->quantity);
    }

    public function test_unfulfillable_line_throws_with_candidates(): void
    {
        [$master, $copy, $nearShop, $farShop] = $this->masterWithTwoCopies();
        $this->enableMultiVendor();
        // move the near shop copy out of stock
        $master->update(['quantity' => 0]);
        $customerUser = User::factory()->create();
        $customer = Customer::factory()->create(['user_id' => $customerUser->id]);
        $address = Address::factory()->create([
            'customer_id' => $customer->id,
            'latitude' => 26.9,
            'longitude' => 75.8,
        ]);
        Cart::create([
            'customer_id' => $customer->id,
            'shop_id' => $nearShop->id,
            'product_id' => $master->id,
            'quantity' => 2,
        ]);

        try {
            $this->placeOrder($customerUser, $address, $nearShop->id);
            $this->fail('Expected UnfulfillableOrderException');
        } catch (UnfulfillableOrderException $e) {
            $this->assertArrayHasKey($master->id, $e->unfulfillable);
        }
    }

    public function test_atomic_decrement_rejects_oversell(): void
    {
        $shop = Shop::factory()->create();
        $shop->user->update(['is_active' => true]);
        Brand::create(['name' => 'Test Brand', 'slug' => 'test-brand']);
        $unit = Unit::create(['name' => 'kg', 'shop_id' => $shop->id, 'is_active' => true]);
        $product = Product::factory()->create([
            'shop_id' => $shop->id,
            'unit_id' => $unit->id,
            'quantity' => 3,
            'is_active' => true,
            'is_approve' => true,
        ]);

        $first = Product::query()
            ->whereKey($product->id)->where('quantity', '>=', 3)->decrement('quantity', 3);
        $second = Product::query()
            ->whereKey($product->id)->where('quantity', '>=', 3)->decrement('quantity', 3);

        $this->assertSame(1, $first);
        $this->assertSame(0, $second);
        $this->assertSame(0, (int) $product->fresh()->quantity);
    }

    public function test_place_order_requires_address_coordinates(): void
    {
        [$master, $copy, $nearShop, $farShop] = $this->masterWithTwoCopies();
        $customerUser = User::factory()->create();
        $customer = Customer::factory()->create(['user_id' => $customerUser->id]);
        $address = Address::factory()->create(['customer_id' => $customer->id, 'latitude' => null, 'longitude' => null]);
        Cart::create(['customer_id' => $customer->id, 'shop_id' => $nearShop->id, 'product_id' => $master->id, 'quantity' => 2]);

        $response = $this->actingAs($customerUser, 'sanctum')->postJson('/api/place-order', [
            'shop_ids' => [$nearShop->id],
            'address_id' => $address->id,
            'payment_method' => 'Cash Payment',
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('message', 'Please set your delivery location on the map before placing the order');
    }

    public function test_place_order_returns_candidates_for_unfulfillable_line(): void
    {
        [$master, $copy, $nearShop, $farShop] = $this->masterWithTwoCopies();
        $this->enableMultiVendor(); // must be multi-vendor for per-line shop allocation
        $master->update(['quantity' => 0]); // near shop out of stock; far shop has stock but out of radius
        $customerUser = User::factory()->create();
        $customer = Customer::factory()->create(['user_id' => $customerUser->id]);
        $address = Address::factory()->create(['customer_id' => $customer->id, 'latitude' => 26.9, 'longitude' => 75.8]);
        Cart::create(['customer_id' => $customer->id, 'shop_id' => $nearShop->id, 'product_id' => $master->id, 'quantity' => 2]);

        $response = $this->actingAs($customerUser, 'sanctum')->postJson('/api/place-order', [
            'shop_ids' => [$nearShop->id],
            'address_id' => $address->id,
            'payment_method' => 'Cash Payment',
        ]);

        $response->assertStatus(422);
        $unfulfillable = $response->json('data.unfulfillable');
        $this->assertArrayHasKey((string) $master->id, $unfulfillable);
        $this->assertSame($farShop->id, $unfulfillable[(string) $master->id][0]['shop_id']);
    }

    public function test_shop_candidates_endpoint_returns_ranked_shops(): void
    {
        [$master, $copy, $nearShop, $farShop] = $this->masterWithTwoCopies();
        $customerUser = User::factory()->create();
        $customer = Customer::factory()->create(['user_id' => $customerUser->id]);
        $address = Address::factory()->create(['customer_id' => $customer->id, 'latitude' => 26.9, 'longitude' => 75.8]);

        $response = $this->actingAs($customerUser, 'sanctum')->postJson('/api/shop-candidates', [
            'address_id' => $address->id,
            'products' => [['product_id' => $master->id, 'quantity' => 2]],
        ]);

        $response->assertOk();
        $candidates = $response->json('data.shop_candidates.'.$master->id);
        $this->assertCount(2, $candidates);
        $this->assertSame($nearShop->id, $candidates[0]['shop_id']);
    }

    private function placeOrder(User $customerUser, Address $address, int $shopId): Payment
    {
        $request = Request::create('/api/place-order', 'POST', [
            'shop_ids' => [$shopId],
            'address_id' => $address->id,
            'payment_method' => 'cash',
        ]);

        $this->actingAs($customerUser);
        $orderRequest = OrderRequest::createFromBase($request);
        $this->app->instance('request', $orderRequest);

        $carts = userCart($orderRequest)->get();

        return OrderRepository::storeByRequestFromCart(
            $orderRequest,
            PaymentMethod::CASH,
            $carts,
        );
    }

    private function enableMultiVendor(): void
    {
        Cache::forget('generale_setting');
        GeneraleSetting::create(['shop_type' => 'multi']);
    }

    private function invokePrivate(string $method, array $args)
    {
        $ref = new \ReflectionMethod(OrderRepository::class, $method);
        $ref->setAccessible(true);

        return $ref->invokeArgs(null, $args);
    }
}
