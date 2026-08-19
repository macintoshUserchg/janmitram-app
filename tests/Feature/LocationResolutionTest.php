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

    public function test_location_resolve_returns_shops_in_user_city_via_ip_geolocation(): void
    {
        $user = User::factory()->create(['is_active' => true]);
        $shop = Shop::create([
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
}
