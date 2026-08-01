<?php

namespace Tests\Feature;

use App\Models\Shop;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class MapIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
        $this->seed(PermissionSeeder::class);
    }

    public function test_shop_registration_page_loads_map_helper_and_container(): void
    {
        $response = $this->get(route('shop.register'));

        $response->assertOk();
        $response->assertSee('janmitram-map-helper.js');
        $response->assertSee('id="map"', false);
        $response->assertSee('latitude');
        $response->assertSee('longitude');
    }

    public function test_admin_shop_create_page_loads_map_picker(): void
    {
        $admin = User::factory()->create(['is_active' => true]);
        $admin->assignRole(Role::findOrCreate('root', 'web'));

        $response = $this->actingAs($admin)
            ->get(route('admin.shop.create'));

        $response->assertOk();
        $response->assertSee('id="map"', false);
        $response->assertSee('initJanmitramMap');
    }

    public function test_admin_shop_edit_page_loads_map_picker_with_coordinates(): void
    {
        $admin = User::factory()->create(['is_active' => true]);
        $admin->assignRole(Role::findOrCreate('root', 'web'));

        $user = User::factory()->create(['is_active' => true]);
        $shop = Shop::create([
            'name' => 'Map Test Shop',
            'user_id' => $user->id,
            'status' => true,
            'latitude' => 21.2514,
            'longitude' => 81.6296,
        ]);

        $response = $this->actingAs($admin)
            ->get(route('admin.shop.edit', $shop->id));

        $response->assertOk();
        $response->assertSee('21.2514');
        $response->assertSee('81.6296');
        $response->assertSee('initJanmitramMap');
    }
}
