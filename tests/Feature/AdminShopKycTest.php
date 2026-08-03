<?php

namespace Tests\Feature;

use App\Models\Shop;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminShopKycTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
        $this->seed(RoleSeeder::class);
    }

    private function admin(): User
    {
        $admin = User::factory()->create();
        $admin->assignRole('root');

        return $admin;
    }

    private function validShopPayload(): array
    {
        return [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'phone' => '1234567890',
            'gender' => 'male',
            'email' => 'kycadmin@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'shop_name' => 'KYC Admin Shop',
            'address' => '123 Market St',
            'latitude' => 27.005694931660006,
            'longitude' => 75.77754972401056,
            'profile_photo' => UploadedFile::fake()->image('profile.jpg'),
            'shop_logo' => UploadedFile::fake()->image('logo.jpg'),
            'shop_banner' => UploadedFile::fake()->image('banner.jpg'),
            'aadhaar_card' => UploadedFile::fake()->image('aadhaar.jpg'),
            'aadhaar_number' => '123456789012',
            'pan_card' => UploadedFile::fake()->image('pan.jpg'),
            'pan_number' => 'ABCDE1234F',
            'date_of_birth' => '1990-01-15',
            'qualification' => 'B.Com',
            'bank_name' => 'HDFC Bank',
            'ifsc' => 'HDFC0001234',
            'account_number' => '12345678901234',
        ];
    }

    public function test_admin_shop_create_requires_kyc_fields(): void
    {
        $payload = $this->validShopPayload();
        unset(
            $payload['aadhaar_card'], $payload['aadhaar_number'],
            $payload['pan_card'], $payload['pan_number'],
            $payload['date_of_birth'], $payload['qualification'],
            $payload['bank_name'], $payload['ifsc'], $payload['account_number']
        );

        $response = $this->actingAs($this->admin())->post(route('admin.shop.store'), $payload);

        $response->assertSessionHasErrors([
            'aadhaar_card', 'aadhaar_number', 'pan_card', 'pan_number',
            'date_of_birth', 'qualification', 'bank_name', 'ifsc', 'account_number',
        ]);
    }

    public function test_admin_shop_create_persists_kyc(): void
    {
        $response = $this->actingAs($this->admin())->post(route('admin.shop.store'), $this->validShopPayload());

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('admin.shop.index'));

        $shop = Shop::where('name', 'KYC Admin Shop')->first();
        $this->assertNotNull($shop);
        $this->assertDatabaseHas('shop_kyc', [
            'shop_id' => $shop->id,
            'aadhaar_number' => '123456789012',
            'pan_number' => 'ABCDE1234F',
            'bank_name' => 'HDFC Bank',
            'ifsc' => 'HDFC0001234',
            'qualification' => 'B.Com',
        ]);
    }
}
