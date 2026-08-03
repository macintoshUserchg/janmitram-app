<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Warehouse;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminShopCreateTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    public function test_admin_can_view_shop_create_page(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('root');

        $response = $this->actingAs($admin)->get(route('admin.shop.create'));

        $response->assertStatus(200);
        $response->assertSee('Add New Shop');
    }

    public function test_admin_can_store_new_shop(): void
    {
        Storage::fake('public');

        $admin = User::factory()->create();
        $admin->assignRole('root');

        $warehouse = Warehouse::create([
            'name' => 'Central Hub',
            'code' => 'WH-CENTRAL',
            'is_default' => true,
            'status' => true,
        ]);

        $response = $this->actingAs($admin)->post(route('admin.shop.store'), [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'phone' => '1234567890',
            'gender' => 'male',
            'email' => 'shopowner@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'shop_name' => 'Test New Shop',
            'address' => '123 Market St',
            'description' => 'Test shop description',
            'latitude' => 27.005694931660006,
            'longitude' => 75.77754972401056,
            'warehouse_id' => $warehouse->id,
            'profile_photo' => UploadedFile::fake()->image('profile.jpg'),
            'shop_logo' => UploadedFile::fake()->image('logo.jpg'),
            'shop_banner' => UploadedFile::fake()->image('banner.jpg'),
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('admin.shop.index'));
        $this->assertDatabaseHas('shops', ['name' => 'Test New Shop']);
    }
}
