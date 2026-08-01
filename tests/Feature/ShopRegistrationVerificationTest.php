<?php

namespace Tests\Feature;

use App\Models\Shop;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ShopRegistrationVerificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
        $this->seed(RoleSeeder::class);
        $this->seed(PermissionSeeder::class);
    }

    public function test_shop_register_page_renders_wizard_and_map_elements(): void
    {
        $response = $this->get(route('shop.register'));

        $response->assertOk();
        $response->assertSee('janmitram-map-helper.js');
        $response->assertSee('id="map"', false);
        $response->assertSee('validateStep');
        $response->assertSee('validateStep2');
        $response->assertSee('sponsor_ref_input');
    }

    public function test_shop_register_validation_fails_for_invalid_inputs(): void
    {
        $response = $this->post(route('shop.register.submit'), [
            'first_name' => '',
            'email' => 'invalid-email-format',
            'phone' => '123', // too short
            'password' => '123', // too short
            'password_confirmation' => '456',
        ]);

        $response->assertSessionHasErrors(['first_name', 'email', 'phone', 'password', 'shop_name']);
    }

    public function test_shop_register_successful_submission(): void
    {
        $response = $this->post(route('shop.register.submit'), [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.shop@gmail.com',
            'phone' => '9876543210',
            'password' => 'Password@123',
            'password_confirmation' => 'Password@123',
            'shop_name' => 'Janmitram Supermart',
            'profile_photo' => UploadedFile::fake()->image('profile.jpg', 200, 200),
            'shop_logo' => UploadedFile::fake()->image('logo.jpg', 200, 200),
            'shop_banner' => UploadedFile::fake()->image('banner.jpg', 1000, 250),
            'latitude' => '21.2514',
            'longitude' => '81.6296',
        ]);

        $response->assertRedirect(route('shop.login'));
        $response->assertSessionHas('successAlert');

        $user = User::where('email', 'john.shop@gmail.com')->first();
        $this->assertNotNull($user);
        $this->assertFalse((bool) $user->is_active);

        $shop = Shop::where('user_id', $user->id)->first();
        $this->assertNotNull($shop);
        $this->assertEquals('Janmitram Supermart', $shop->name);
        $this->assertEquals('21.2514', $shop->latitude);
        $this->assertEquals('81.6296', $shop->longitude);
    }

    public function test_sponsor_verification_ajax_endpoint(): void
    {
        $user = User::factory()->create(['is_active' => true]);
        $user->assignRole(Role::findOrCreate('shop', 'web'));

        $sponsorShop = Shop::create([
            'name' => 'Verified Master Shop',
            'user_id' => $user->id,
            'status' => true,
        ]);

        $referralCode = $sponsorShop->referral_code;

        $response = $this->getJson(route('shop.verify-sponsor', ['code' => $referralCode]));

        $response->assertOk();
        $response->assertJson([
            'valid' => true,
            'name' => 'Verified Master Shop',
            'id' => $sponsorShop->id,
        ]);
    }
}
