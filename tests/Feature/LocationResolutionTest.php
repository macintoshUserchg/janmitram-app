<?php

namespace Tests\Feature;

use App\Models\GeneraleSetting;
use App\Models\Product;
use App\Models\Shop;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocationResolutionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
        $this->seed(PermissionSeeder::class);
    }

    public function test_location_resolve_returns_shops_in_user_city_via_ip_geolocation(): void
    {
        $user = User::factory()->create(['is_active' => true]);
        Shop::create([
            'name' => 'Jaipur Main Shop',
            'user_id' => $user->id,
            'status' => true,
            'address' => 'Badharna road harmada jaipur',
        ]);

        $response = $this->getJson('/api/location/resolve');

        $response->assertOk()
            ->assertJsonPath('data.city', 'Jaipur')
            ->assertJsonPath('data.state', 'Rajasthan')
            ->assertJsonPath('data.nearest_shop.name', 'Jaipur Main Shop');
    }

    public function test_location_resolve_filters_by_user_selected_city(): void
    {
        $user = User::factory()->create(['is_active' => true]);

        Shop::create([
            'name' => 'Jaipur Shop',
            'user_id' => $user->id,
            'status' => true,
            'address' => 'Sanganer, Jaipur',
        ]);

        Shop::create([
            'name' => 'Mumbai Shop',
            'user_id' => $user->id,
            'status' => true,
            'address' => 'Andheri West, Mumbai',
        ]);

        $response = $this->getJson('/api/location/resolve?city=Mumbai');

        $response->assertOk()
            ->assertJsonPath('data.city', 'Mumbai')
            ->assertJsonPath('data.nearest_shop.name', 'Mumbai Shop');
    }

    public function test_pincode_resolution_returns_matching_city_shops(): void
    {
        $user = User::factory()->create(['is_active' => true]);

        Shop::create([
            'name' => 'Jaipur Main Shop',
            'user_id' => $user->id,
            'status' => true,
            'address' => 'Harmada Jaipur',
        ]);

        $response = $this->getJson('/api/location/by-pincode?pincode=302013');

        $response->assertOk()
            ->assertJsonPath('data.city', 'Jaipur')
            ->assertJsonPath('data.nearest_shop.name', 'Jaipur Main Shop');
    }

    public function test_home_popular_products_excludes_main_shop_and_distributes_across_branch_shops(): void
    {
        GeneraleSetting::create(['shop_type' => 'multiple']);

        $user1 = User::factory()->create(['is_active' => true]);
        $mainShop = Shop::create([
            'name' => 'Main Janmitram Shop',
            'user_id' => $user1->id,
            'status' => true,
            'address' => 'Jaipur Harmada',
        ]);

        $user2 = User::factory()->create(['is_active' => true]);
        $branchShop1 = Shop::create([
            'name' => 'Janmitram Sanganer',
            'user_id' => $user2->id,
            'status' => true,
            'address' => 'Sanganer Jaipur',
        ]);

        $user3 = User::factory()->create(['is_active' => true]);
        $branchShop2 = Shop::create([
            'name' => 'Janmitram Pratap Nagar',
            'user_id' => $user3->id,
            'status' => true,
            'address' => 'Pratap Nagar Jaipur',
        ]);

        // Product on Main Shop (ID 1 / mainShop) - MUST NOT BE SHOWN
        Product::create([
            'name' => 'Main Shop Product Excluded',
            'slug' => 'main-shop-product-excluded',
            'shop_id' => $mainShop->id,
            'is_active' => true,
            'is_approve' => true,
            'price' => 100,
            'buy_price' => 80,
            'quantity' => 50,
        ]);

        // Products on Branch Shops
        Product::create([
            'name' => 'Sanganer Rice',
            'slug' => 'sanganer-rice',
            'shop_id' => $branchShop1->id,
            'is_active' => true,
            'is_approve' => true,
            'price' => 120,
            'buy_price' => 90,
            'quantity' => 50,
        ]);

        Product::create([
            'name' => 'Pratap Nagar Dal',
            'slug' => 'pratap-nagar-dal',
            'shop_id' => $branchShop2->id,
            'is_active' => true,
            'is_approve' => true,
            'price' => 140,
            'buy_price' => 110,
            'quantity' => 50,
        ]);

        $response = $this->getJson('/api/home?city=Jaipur');

        $response->assertOk();
        $popularProducts = $response->json('data.popular_products');

        $productNames = collect($popularProducts)->pluck('name')->toArray();
        $shopIds = collect($popularProducts)->pluck('shop_id')->toArray();

        // Main shop product is strictly excluded
        $this->assertNotContains('Main Shop Product Excluded', $productNames);
        $this->assertNotContains($mainShop->id, $shopIds);

        // Branch shop products are included
        $this->assertContains('Sanganer Rice', $productNames);
        $this->assertContains('Pratap Nagar Dal', $productNames);
    }
}
