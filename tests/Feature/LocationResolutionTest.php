<?php

namespace Tests\Feature;

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

    public function test_location_resolve_returns_default_hub_when_ip_is_local(): void
    {
        $user = User::factory()->create(['is_active' => true]);
        $shop = Shop::create([
            'name' => 'Jaipur Main Shop',
            'user_id' => $user->id,
            'status' => true,
            'latitude' => 26.9985869,
            'longitude' => 75.7680702,
            'address' => 'Jaipur, Rajasthan',
        ]);

        $response = $this->getJson('/api/location/resolve');

        $response->assertOk()
            ->assertJsonPath('data.city', 'Jaipur')
            ->assertJsonPath('data.state', 'Rajasthan')
            ->assertJsonPath('data.nearest_shop.name', 'Jaipur Main Shop')
            ->assertJsonPath('data.nearest_shop.distance_km', 0.0);
    }

    public function test_location_resolve_ranks_closest_shop_accurately_with_coordinates(): void
    {
        $user = User::factory()->create(['is_active' => true]);

        $jaipurShop = Shop::create([
            'name' => 'Jaipur Shop',
            'user_id' => $user->id,
            'status' => true,
            'latitude' => 26.9985869,
            'longitude' => 75.7680702,
        ]);

        $mumbaiShop = Shop::create([
            'name' => 'Mumbai Shop',
            'user_id' => $user->id,
            'status' => true,
            'latitude' => 18.9899017,
            'longitude' => 72.8942871,
        ]);

        // Query with coordinates close to Mumbai (18.96, 72.82)
        $response = $this->getJson('/api/location/resolve?latitude=18.96&longitude=72.82');

        $response->assertOk()
            ->assertJsonPath('data.nearest_shop.name', 'Mumbai Shop');
    }

    public function test_nearest_shops_endpoint_returns_sorted_shops(): void
    {
        $user = User::factory()->create(['is_active' => true]);

        $shop1 = Shop::create([
            'name' => 'Shop Far',
            'user_id' => $user->id,
            'status' => true,
            'latitude' => 28.6139,
            'longitude' => 77.2090, // Delhi
        ]);

        $shop2 = Shop::create([
            'name' => 'Shop Close',
            'user_id' => $user->id,
            'status' => true,
            'latitude' => 26.9124,
            'longitude' => 75.7873, // Jaipur
        ]);

        $response = $this->getJson('/api/location/nearest-shops?latitude=26.92&longitude=75.79');

        $response->assertOk()
            ->assertJsonPath('data.nearest_shop.name', 'Shop Close');
    }
}
