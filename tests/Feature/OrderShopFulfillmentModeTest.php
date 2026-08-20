<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Area;
use App\Models\Brand;
use App\Models\Cart;
use App\Models\Category;
use App\Models\Customer;
use App\Models\GeneraleSetting;
use App\Models\Order;
use App\Models\Product;
use App\Models\Shop;
use App\Models\ShopInventory;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class OrderShopFulfillmentModeTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Customer $customer;

    private Address $deliveryAddress;

    private Shop $shopNear;

    private Shop $shopFar;

    private Product $masterProduct;

    private Product $productInNearShop;

    private Product $productInFarShop;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'customer']);
        Role::firstOrCreate(['name' => 'shop']);

        $setting = GeneraleSetting::firstOrCreate([]);
        $setting->update([
            'shop_type' => 'multi',
            'shop_allocation_radius_km' => 50.0,
        ]);

        $this->user = User::factory()->create([
            'email_verified_at' => now(),
        ]);
        $this->user->assignRole('customer');
        $this->customer = Customer::factory()->create(['user_id' => $this->user->id]);

        $area = Area::create(['name' => 'Jaipur Central', 'delivery_amount' => 30]);
        $this->deliveryAddress = Address::create([
            'customer_id' => $this->customer->id,
            'name' => 'Customer Home',
            'phone' => '9999999999',
            'area_id' => $area->id,
            'address_line' => 'MI Road, Jaipur',
            'address_type' => 'home',
            'latitude' => 26.9124,
            'longitude' => 75.7873,
            'is_default' => true,
        ]);

        $shopOwnerNear = User::factory()->create(['is_active' => true]);
        $shopOwnerNear->assignRole('shop');
        $this->shopNear = Shop::factory()->create([
            'user_id' => $shopOwnerNear->id,
            'name' => 'Near Hub Shop (2 km)',
            'latitude' => 26.9150,
            'longitude' => 75.7900, // ~0.4 km away
            'delivery_charge' => 25.0,
        ]);

        $shopOwnerFar = User::factory()->create(['is_active' => true]);
        $shopOwnerFar->assignRole('shop');
        $this->shopFar = Shop::factory()->create([
            'user_id' => $shopOwnerFar->id,
            'name' => 'Far Hub Shop (35 km)',
            'latitude' => 26.6500,
            'longitude' => 75.6500, // ~32 km away
            'delivery_charge' => 60.0,
        ]);

        Category::create(['name' => 'General']);
        $brand = Brand::create(['name' => 'Janmitram Brand', 'slug' => 'janmitram-brand']);
        $unit = Unit::create(['name' => 'pcs', 'shop_id' => $this->shopNear->id, 'is_active' => true]);

        $this->masterProduct = Product::create([
            'name' => 'Master Premium Tea',
            'brand_id' => $brand->id,
            'unit_id' => $unit->id,
            'price' => 250.0,
            'quantity' => 100,
            'is_stock_managed' => true,
            'is_active' => true,
            'is_approve' => true,
            'is_digital' => false,
        ]);

        ShopInventory::create([
            'shop_id' => $this->shopNear->id,
            'product_id' => $this->masterProduct->id,
            'quantity' => 20,
            'is_active' => true,
        ]);

        ShopInventory::create([
            'shop_id' => $this->shopFar->id,
            'product_id' => $this->masterProduct->id,
            'quantity' => 20,
            'is_active' => true,
        ]);

        $this->productInNearShop = $this->masterProduct;
        $this->productInFarShop = $this->masterProduct;
    }

    public function test_strict_mode_delivers_strictly_from_selected_shop_even_if_another_shop_is_closer(): void
    {
        // Add item from the Far Shop into Cart
        Cart::create([
            'customer_id' => $this->customer->id,
            'shop_id' => $this->shopFar->id,
            'product_id' => $this->productInFarShop->id,
            'quantity' => 3,
            'is_buy_now' => false,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')->postJson('/api/place-order', [
            'shop_ids' => [$this->shopFar->id],
            'address_id' => $this->deliveryAddress->id,
            'payment_method' => 'CASH',
            'fulfill_from_nearest_shop' => false, // Strict mode
        ]);

        $response->assertStatus(200);

        // Strict mode must create order strictly for shopFar
        $this->assertDatabaseHas('orders', [
            'shop_id' => $this->shopFar->id,
            'customer_id' => $this->customer->id,
        ]);
        $this->assertDatabaseMissing('orders', [
            'shop_id' => $this->shopNear->id,
        ]);

        // Far shop stock was decremented from 20 -> 17; Near shop remains 20
        $this->assertSame(17, $this->masterProduct->getStockForShop($this->shopFar->id));
        $this->assertSame(20, $this->masterProduct->getStockForShop($this->shopNear->id));
    }

    public function test_auto_nearest_mode_reallocates_to_closer_shop(): void
    {
        // Add item from the Far Shop into Cart
        Cart::create([
            'customer_id' => $this->customer->id,
            'shop_id' => $this->shopFar->id,
            'product_id' => $this->productInFarShop->id,
            'quantity' => 3,
            'is_buy_now' => false,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')->postJson('/api/place-order', [
            'shop_ids' => [$this->shopFar->id],
            'address_id' => $this->deliveryAddress->id,
            'payment_method' => 'CASH',
            'fulfill_from_nearest_shop' => true, // Auto nearest mode (default)
        ]);

        $response->assertStatus(200);

        // Nearest mode reallocates to shopNear because it is closer (~0.4 km vs 32 km)
        $this->assertDatabaseHas('orders', [
            'shop_id' => $this->shopNear->id,
            'customer_id' => $this->customer->id,
        ]);
        $this->assertDatabaseMissing('orders', [
            'shop_id' => $this->shopFar->id,
        ]);

        // Near shop stock was decremented from 20 -> 17; Far shop remains 20
        $this->assertSame(17, $this->masterProduct->getStockForShop($this->shopNear->id));
        $this->assertSame(20, $this->masterProduct->getStockForShop($this->shopFar->id));
    }

    public function test_strict_mode_rejects_order_if_selected_shop_has_insufficient_stock(): void
    {
        // Set far shop stock to 0
        ShopInventory::where('shop_id', $this->shopFar->id)->where('product_id', $this->masterProduct->id)->update(['quantity' => 0]);

        Cart::create([
            'customer_id' => $this->customer->id,
            'shop_id' => $this->shopFar->id,
            'product_id' => $this->productInFarShop->id,
            'quantity' => 2,
            'is_buy_now' => false,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')->postJson('/api/place-order', [
            'shop_ids' => [$this->shopFar->id],
            'address_id' => $this->deliveryAddress->id,
            'payment_method' => 'CASH',
            'fulfill_from_nearest_shop' => false, // Strict mode
        ]);

        $response->assertStatus(422);
        $this->assertStringContainsString('does not have enough stock', $response->json('message'));
        $this->assertDatabaseCount('orders', 0);
    }

    public function test_multi_shop_cart_in_strict_mode_splits_orders_matching_selected_shops_exactly(): void
    {
        // Create second distinct master product
        $product2 = Product::create([
            'name' => 'Master Biscuit Pack',
            'brand_id' => $this->masterProduct->brand_id,
            'unit_id' => $this->masterProduct->unit_id,
            'price' => 50.0,
            'quantity' => 100,
            'is_stock_managed' => true,
            'is_active' => true,
            'is_approve' => true,
            'is_digital' => false,
        ]);

        ShopInventory::create([
            'shop_id' => $this->shopNear->id,
            'product_id' => $product2->id,
            'quantity' => 15,
            'is_active' => true,
        ]);

        $product2InNearShop = $product2;

        // Cart item 1: Tea from Far Shop
        Cart::create([
            'customer_id' => $this->customer->id,
            'shop_id' => $this->shopFar->id,
            'product_id' => $this->productInFarShop->id,
            'quantity' => 2,
            'is_buy_now' => false,
        ]);

        // Cart item 2: Biscuits from Near Shop
        Cart::create([
            'customer_id' => $this->customer->id,
            'shop_id' => $this->shopNear->id,
            'product_id' => $product2InNearShop->id,
            'quantity' => 4,
            'is_buy_now' => false,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')->postJson('/api/place-order', [
            'shop_ids' => [$this->shopFar->id, $this->shopNear->id],
            'address_id' => $this->deliveryAddress->id,
            'payment_method' => 'CASH',
            'fulfill_from_nearest_shop' => false, // Strict mode
        ]);

        $response->assertStatus(200);

        // Exactly 2 sub-orders created: 1 for Far Shop, 1 for Near Shop
        $this->assertDatabaseCount('orders', 2);
        $this->assertDatabaseHas('orders', ['shop_id' => $this->shopFar->id]);
        $this->assertDatabaseHas('orders', ['shop_id' => $this->shopNear->id]);

        $this->assertSame(18, $this->masterProduct->getStockForShop($this->shopFar->id));
        $this->assertSame(11, $product2->getStockForShop($this->shopNear->id));
    }
}
