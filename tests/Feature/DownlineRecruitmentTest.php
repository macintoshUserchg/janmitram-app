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

class DownlineRecruitmentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
        $this->seed(RoleSeeder::class);
        $this->seed(PermissionSeeder::class);
    }

    private function createShopOwner(string $shopName): array
    {
        $user = User::factory()->create(['is_active' => true]);
        $user->assignRole(Role::findOrCreate('shop', 'web'));
        $user->givePermissionTo(['shop.payout.index', 'shop.payout.network', 'shop.payout.network.create', 'shop.payout.network.store']);

        $shop = Shop::create([
            'name' => $shopName,
            'user_id' => $user->id,
            'status' => true,
        ]);

        $user->refresh();

        return [$user, $shop];
    }

    public function test_public_registration_with_referral_code_sets_parent_shop_id(): void
    {
        [$sponsorUser, $sponsorShop] = $this->createShopOwner('Master Sponsor Shop');

        $referralCode = $sponsorShop->referral_code; // e.g. JAN-00001

        $response = $this->post(route('shop.register.submit'), [
            'first_name' => 'Child',
            'last_name' => 'Seller',
            'email' => 'child@gmail.com',
            'phone' => '9876543210',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'shop_name' => 'Child Retailer Store',
            'profile_photo' => UploadedFile::fake()->image('profile.jpg', 200, 200),
            'shop_logo' => UploadedFile::fake()->image('logo.jpg', 200, 200),
            'shop_banner' => UploadedFile::fake()->image('banner.jpg', 1000, 250),
            'latitude' => '27.005694931660006',
            'longitude' => '75.77754972401056',
            'ref' => $referralCode,
        ]);

        $response->assertRedirect(route('shop.login'));

        $childShop = Shop::where('name', 'Child Retailer Store')->first();
        $this->assertNotNull($childShop);
        $this->assertEquals($sponsorShop->id, $childShop->parent_shop_id);
    }

    public function test_shop_owner_can_directly_add_downline_shop_from_panel(): void
    {
        [$sponsorUser, $sponsorShop] = $this->createShopOwner('Direct Sponsor Shop');

        $response = $this->actingAs($sponsorUser)->post(route('shop.payout.network.store'), [
            'first_name' => 'DirectDownline',
            'last_name' => 'Owner',
            'email' => 'downline@gmail.com',
            'phone' => '9123456789',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'shop_name' => 'Direct Downline Mart',
            'profile_photo' => UploadedFile::fake()->image('profile.jpg', 200, 200),
            'shop_logo' => UploadedFile::fake()->image('logo.jpg', 200, 200),
            'shop_banner' => UploadedFile::fake()->image('banner.jpg', 1000, 250),
            'latitude' => '27.005694931660006',
            'longitude' => '75.77754972401056',
        ]);

        $response->assertRedirect(route('shop.payout.network'));

        $downlineShop = Shop::where('name', 'Direct Downline Mart')->first();
        $this->assertNotNull($downlineShop);
        $this->assertEquals($sponsorShop->id, $downlineShop->parent_shop_id);
    }
}
