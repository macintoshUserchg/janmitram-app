<?php

namespace Tests\Feature;

use App\Models\Area;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminCityDeliveryRateTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $role = Role::create(['name' => 'root']);
        $this->admin = User::factory()->create();
        $this->admin->assignRole($role);
    }

    public function test_admin_can_view_city_delivery_rates(): void
    {
        Area::create(['name' => 'Jaipur', 'delivery_amount' => 30, 'is_active' => true]);

        $response = $this->actingAs($this->admin)->get(route('admin.area.index'));

        $response->assertStatus(200)
            ->assertSee('City Delivery Rates')
            ->assertSee('Jaipur')
            ->assertSee('30.00');
    }

    public function test_admin_can_create_city_delivery_rate(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.area.store'), [
            'name' => 'Udaipur',
            'delivery_amount' => 45.50,
        ]);

        $response->assertRedirect(route('admin.area.index'));
        $this->assertDatabaseHas('areas', [
            'name' => 'Udaipur',
            'delivery_amount' => 45.50,
        ]);
    }

    public function test_admin_can_update_city_delivery_rate(): void
    {
        $rate = Area::create(['name' => 'Jodhpur', 'delivery_amount' => 40, 'is_active' => true]);

        $response = $this->actingAs($this->admin)->put(route('admin.area.update', $rate->id), [
            'name' => 'Jodhpur City',
            'delivery_amount' => 50.00,
        ]);

        $response->assertRedirect(route('admin.area.index'));
        $this->assertDatabaseHas('areas', [
            'id' => $rate->id,
            'name' => 'Jodhpur City',
            'delivery_amount' => 50.00,
        ]);
    }

    public function test_admin_can_toggle_city_delivery_rate(): void
    {
        $rate = Area::create(['name' => 'Kota', 'delivery_amount' => 35, 'is_active' => true]);

        $response = $this->actingAs($this->admin)->get(route('admin.area.toggle', $rate->id));

        $response->assertStatus(302);
        $this->assertDatabaseHas('areas', [
            'id' => $rate->id,
            'is_active' => false,
        ]);
    }

    public function test_admin_can_delete_city_delivery_rate(): void
    {
        $rate = Area::create(['name' => 'Bikaner', 'delivery_amount' => 40, 'is_active' => true]);

        $response = $this->actingAs($this->admin)->delete(route('admin.area.destroy', $rate->id));

        $response->assertRedirect(route('admin.area.index'));
        $this->assertDatabaseMissing('areas', [
            'id' => $rate->id,
        ]);
    }
}
