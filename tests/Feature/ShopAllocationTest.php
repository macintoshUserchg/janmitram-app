<?php

namespace Tests\Feature;

use App\Enums\PaymentMethod;
use App\Exceptions\UnfulfillableOrderException;
use App\Http\Requests\OrderRequest;
use App\Models\Address;
use App\Models\Area;
use App\Models\Brand;
use App\Models\Cart;
use App\Models\Coupon;
use App\Models\Customer;
use App\Models\FlashSale;
use App\Models\GeneraleSetting;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Shop;
use App\Models\Unit;
use App\Models\User;
use App\Models\Warehouse;
use App\Models\WarehouseStock;
use App\Repositories\OrderRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ShopAllocationTest extends TestCase
{
    use RefreshDatabase;

    public function test_address_store_requires_coordinates(): void
    {
        // CustomerFactory::definition() assigns the Spatie 'customer' role to the
        // user it creates; under RefreshDatabase the role must exist first.
        Role::create(['name' => 'customer']);
        $user = User::factory()->create();
        $customer = Customer::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/address/store', [
            'name' => 'Test Customer',
            'phone' => '9800000000',
            'address_line' => '123 Test Street, Jaipur',
            'address_type' => 'home',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['latitude', 'longitude']);
    }

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

    public function test_reorder_allocates_to_nearest_shop(): void
    {
        [$master, $copy, $nearShop, $farShop] = $this->masterWithTwoCopies();
        $this->enableMultiVendor();
        // OrderFactory needs at least one Coupon row to satisfy Coupon::all()->random()
        Coupon::factory()->create(['shop_id' => $nearShop->id]);
        $customerUser = User::factory()->create();
        $customer = Customer::factory()->create(['user_id' => $customerUser->id]);
        $address = Address::factory()->create(['customer_id' => $customer->id, 'latitude' => 26.9, 'longitude' => 75.8]);
        $order = Order::factory()->create([
            'shop_id' => $farShop->id,
            'customer_id' => $customer->id,
            'address_id' => $address->id,
            'order_status' => 'Delivered',
            'payment_status' => 'Paid',
        ]);
        $order->products()->attach($master->id, ['quantity' => 2, 'color' => null, 'size' => null, 'unit' => null, 'price' => 100]);
        $payment = Payment::create(['amount' => 200, 'payment_method' => 'Cash Payment']);
        $this->actingAs($customerUser, 'sanctum');

        $orders = OrderRepository::reOrder($order, $payment);

        $this->assertCount(1, $orders);
        $this->assertSame($nearShop->id, $orders->first()->shop_id);
    }

    public function test_allocation_uses_configured_radius(): void
    {
        [$master, $copy, $nearShop, $farShop] = $this->masterWithTwoCopies();
        GeneraleSetting::create(['shop_allocation_radius_km' => 1]);
        $address = Address::factory()->create(['latitude' => 26.9, 'longitude' => 75.8]);

        $candidates = OrderRepository::candidateShopsForLine($master, 2, $address);

        $this->assertCount(2, $candidates);
        $this->assertFalse($candidates[0]->radius_eligible); // near shop also out of 1km radius
    }

    public function test_order_placement_rolls_back_all_writes_on_failure(): void
    {
        Cache::forget('generale_setting'); // single-vendor
        Role::create(['name' => 'customer']);
        Area::factory()->create();
        $shop = Shop::factory()->create();
        $shop->user->update(['is_active' => true]);
        Brand::create(['name' => 'Test Brand', 'slug' => 'test-brand']);
        $unit = Unit::create(['name' => 'kg', 'shop_id' => $shop->id, 'is_active' => true]);

        $inStock = Product::factory()->create(['shop_id' => $shop->id, 'unit_id' => $unit->id, 'quantity' => 5, 'is_active' => true, 'is_approve' => true]);
        $outOfStock = Product::factory()->create(['shop_id' => $shop->id, 'unit_id' => $unit->id, 'quantity' => 1, 'is_active' => true, 'is_approve' => true]);

        $customerUser = User::factory()->create();
        $customer = Customer::factory()->create(['user_id' => $customerUser->id]);
        $address = Address::factory()->create(['customer_id' => $customer->id, 'latitude' => 26.9, 'longitude' => 75.8]);

        Cart::create(['customer_id' => $customer->id, 'shop_id' => $shop->id, 'product_id' => $inStock->id, 'quantity' => 2]);
        Cart::create(['customer_id' => $customer->id, 'shop_id' => $shop->id, 'product_id' => $outOfStock->id, 'quantity' => 3]);

        try {
            $this->placeOrder($customerUser, $address, $shop->id);
            $this->fail('Expected RuntimeException for out-of-stock line');
        } catch (\RuntimeException $e) {
            $this->assertStringContainsString('no longer available', $e->getMessage());
        }

        $this->assertSame(0, Payment::count());
        $this->assertSame(0, Order::count());
        $this->assertSame(0, DB::table('order_products')->count());
        $this->assertSame(5, (int) $inStock->fresh()->quantity);
        $this->assertSame(1, (int) $outOfStock->fresh()->quantity);
        $this->assertSame(2, Cart::where('customer_id', $customer->id)->count());
    }

    public function test_sale_decrements_shop_inventory_not_warehouse(): void
    {
        Cache::forget('generale_setting');
        Role::create(['name' => 'customer']);
        Area::factory()->create();
        $shop = Shop::factory()->create();
        $shop->user->update(['is_active' => true]);
        Brand::create(['name' => 'Test Brand', 'slug' => 'test-brand']);
        $unit = Unit::create(['name' => 'kg', 'shop_id' => $shop->id, 'is_active' => true]);
        $product = Product::factory()->create(['shop_id' => $shop->id, 'unit_id' => $unit->id, 'quantity' => 5, 'is_active' => true, 'is_approve' => true]);

        $warehouse = Warehouse::create(['name' => 'Central', 'is_default' => true]);
        $shop->update(['warehouse_id' => $warehouse->id]);
        WarehouseStock::create(['warehouse_id' => $warehouse->id, 'product_id' => $product->id, 'quantity' => 5]);

        $customerUser = User::factory()->create();
        $customer = Customer::factory()->create(['user_id' => $customerUser->id]);
        $address = Address::factory()->create(['customer_id' => $customer->id, 'latitude' => 26.9, 'longitude' => 75.8]);
        Cart::create(['customer_id' => $customer->id, 'shop_id' => $shop->id, 'product_id' => $product->id, 'quantity' => 2]);

        $this->placeOrder($customerUser, $address, $shop->id);

        $this->assertSame(1, Order::count());
        $this->assertSame(1, Payment::count());
        $this->assertSame(1, DB::table('order_products')->count());
        $this->assertSame(3, (int) $product->fresh()->quantity); // shop inventory decremented once
        $this->assertSame(5, (int) WarehouseStock::where('product_id', $product->id)->first()->fresh()->quantity); // warehouse untouched
        $this->assertSame(0, DB::table('stock_ledgers')->count()); // no sale-time ledger rows
        $this->assertSame(0, Cart::where('customer_id', $customer->id)->count()); // cart consumed on success
    }

    public function test_reorder_rolls_back_writes_on_oversell(): void
    {
        Role::create(['name' => 'customer']);
        Area::factory()->create();
        $shop = Shop::factory()->create();
        $shop->user->update(['is_active' => true]);
        Brand::create(['name' => 'Test Brand', 'slug' => 'test-brand']);
        $unit = Unit::create(['name' => 'kg', 'shop_id' => $shop->id, 'is_active' => true]);
        Coupon::factory()->create(['shop_id' => $shop->id]); // OrderFactory requires a Coupon row

        $productA = Product::factory()->create(['shop_id' => $shop->id, 'unit_id' => $unit->id, 'quantity' => 5, 'is_active' => true, 'is_approve' => true]);
        $productB = Product::factory()->create(['shop_id' => $shop->id, 'unit_id' => $unit->id, 'quantity' => 1, 'is_active' => true, 'is_approve' => true]);

        $customerUser = User::factory()->create();
        $customer = Customer::factory()->create(['user_id' => $customerUser->id]);
        $address = Address::factory()->create(['customer_id' => $customer->id, 'latitude' => 26.9, 'longitude' => 75.8]);

        $original = Order::factory()->create([
            'shop_id' => $shop->id, 'customer_id' => $customer->id, 'address_id' => $address->id,
            'order_status' => 'Delivered', 'payment_status' => 'Paid',
        ]);
        $original->products()->attach($productA->id, ['quantity' => 2, 'color' => null, 'size' => null, 'unit' => null, 'price' => 100]);
        $original->products()->attach($productB->id, ['quantity' => 3, 'color' => null, 'size' => null, 'unit' => null, 'price' => 100]);
        Payment::create(['amount' => 200, 'payment_method' => 'Cash Payment']);
        $this->actingAs($customerUser, 'sanctum');

        try {
            OrderRepository::reOrder($original, Payment::first());
            $this->fail('Expected RuntimeException for out-of-stock line');
        } catch (\RuntimeException $e) {
            $this->assertStringContainsString('no longer available', $e->getMessage());
        }

        $this->assertSame(1, Order::count()); // only the original remains
        $this->assertSame(2, DB::table('order_products')->count()); // both original attachments remain
        $this->assertSame(5, (int) $productA->fresh()->quantity);
        $this->assertSame(1, (int) $productB->fresh()->quantity);
    }

    public function test_reorder_uses_current_price_when_flash_sale_ended(): void
    {
        Role::create(['name' => 'customer']);
        Area::factory()->create();
        $shop = Shop::factory()->create(['delivery_charge' => 0]);
        $shop->user->update(['is_active' => true]);
        Brand::create(['name' => 'Test Brand', 'slug' => 'test-brand']);
        $unit = Unit::create(['name' => 'kg', 'shop_id' => $shop->id, 'is_active' => true]);

        $product = Product::factory()->create([
            'shop_id' => $shop->id, 'unit_id' => $unit->id,
            'price' => 500, 'discount_price' => 400, 'quantity' => 10, 'is_active' => true, 'is_approve' => true,
        ]);

        Coupon::factory()->create(['shop_id' => $shop->id]); // OrderFactory requires a Coupon row

        // an ended flash sale that used to discount the product to 100
        $flashSale = FlashSale::create([
            'status' => 1,
            'start_date' => now()->subDays(3)->toDateString(),
            'end_date' => now()->subDay()->toDateString(),
            'start_time' => now()->subDays(3),
            'end_time' => now()->subDay(),
        ]);
        $flashSale->products()->attach($product->id, ['price' => 100, 'quantity' => 10, 'discount' => 0, 'sale_quantity' => 0]);

        $customerUser = User::factory()->create();
        $customer = Customer::factory()->create(['user_id' => $customerUser->id]);
        $address = Address::factory()->create(['customer_id' => $customer->id, 'latitude' => 26.9, 'longitude' => 75.8]);

        $original = Order::factory()->create([
            'shop_id' => $shop->id, 'customer_id' => $customer->id, 'address_id' => $address->id,
            'order_status' => 'Delivered', 'payment_status' => 'Paid',
        ]);
        $original->products()->attach($product->id, ['quantity' => 1, 'color' => null, 'size' => null, 'unit' => null, 'price' => 100]);

        $payment = Payment::create(['amount' => 100, 'payment_method' => 'Cash Payment']);
        $this->actingAs($customerUser, 'sanctum');

        $reordered = OrderRepository::reOrder($original, $payment)->first();

        // the ended flash sale must NOT apply: the item is priced at the current discount price
        $this->assertSame(400, (int) DB::table('order_products')->where('order_id', $reordered->id)->value('price'));
        // the order summary reflects the current price (not the old flash price)
        $this->assertSame(400, (int) $reordered->fresh()->payable_amount);
        // the payment is linked and carries the reordered total
        $this->assertTrue($payment->orders()->whereKey($reordered->id)->exists());
        $this->assertSame(400, (int) $payment->fresh()->amount);
        // the ended flash-sale allocation was not consumed
        $this->assertSame(0, (int) DB::table('flash_sale_products')->where('product_id', $product->id)->value('sale_quantity'));
    }

    public function test_reorder_applies_active_flash_sale_and_consumes_allocation(): void
    {
        Role::create(['name' => 'customer']);
        Area::factory()->create();
        $shop = Shop::factory()->create(['delivery_charge' => 0]);
        $shop->user->update(['is_active' => true]);
        Brand::create(['name' => 'Test Brand', 'slug' => 'test-brand']);
        $unit = Unit::create(['name' => 'kg', 'shop_id' => $shop->id, 'is_active' => true]);

        $product = Product::factory()->create([
            'shop_id' => $shop->id, 'unit_id' => $unit->id,
            'price' => 500, 'discount_price' => 400, 'quantity' => 10, 'is_active' => true, 'is_approve' => true,
        ]);

        Coupon::factory()->create(['shop_id' => $shop->id]); // OrderFactory requires a Coupon row

        $flashSale = FlashSale::create([
            'status' => 1,
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDay()->toDateString(),
            'start_time' => now()->subHour(),
            'end_time' => now()->addHours(23),
        ]);
        $flashSale->products()->attach($product->id, ['price' => 100, 'quantity' => 10, 'discount' => 0, 'sale_quantity' => 0]);

        $customerUser = User::factory()->create();
        $customer = Customer::factory()->create(['user_id' => $customerUser->id]);
        $address = Address::factory()->create(['customer_id' => $customer->id, 'latitude' => 26.9, 'longitude' => 75.8]);

        $original = Order::factory()->create([
            'shop_id' => $shop->id, 'customer_id' => $customer->id, 'address_id' => $address->id,
            'order_status' => 'Delivered', 'payment_status' => 'Paid',
        ]);
        $original->products()->attach($product->id, ['quantity' => 2, 'color' => null, 'size' => null, 'unit' => null, 'price' => 100]);

        $payment = Payment::create(['amount' => 200, 'payment_method' => 'Cash Payment']);
        $this->actingAs($customerUser, 'sanctum');

        $reordered = OrderRepository::reOrder($original, $payment)->first();

        // the running flash sale price applies and its allocation is consumed
        $this->assertSame(100, (int) DB::table('order_products')->where('order_id', $reordered->id)->value('price'));
        $this->assertSame(200, (int) $reordered->fresh()->payable_amount);
        $this->assertSame(2, (int) DB::table('flash_sale_products')->where('product_id', $product->id)->value('sale_quantity'));
        $this->assertTrue($payment->orders()->whereKey($reordered->id)->exists());
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
