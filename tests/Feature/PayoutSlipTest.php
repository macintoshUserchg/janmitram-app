<?php

namespace Tests\Feature;

use App\Models\Shop;
use App\Models\ShopMonthlyPayout;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PayoutSlipTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
        $this->seed(PermissionSeeder::class);
    }

    private function createShopOwner(string $shopName): array
    {
        $user = User::factory()->create(['is_active' => true]);
        $user->assignRole(Role::findOrCreate('shop', 'web'));
        $user->givePermissionTo(['shop.payout.index', 'shop.payout.network', 'shop.payout.slip']);

        $shop = Shop::create([
            'name' => $shopName,
            'user_id' => $user->id,
            'status' => true,
        ]);

        $user->refresh();

        return [$user, $shop];
    }

    public function test_shop_owner_can_access_own_payout_slip(): void
    {
        [$user, $shop] = $this->createShopOwner('Alpha Seller Shop');

        $payout = ShopMonthlyPayout::create([
            'shop_id' => $shop->id,
            'year' => 2026,
            'month' => 7,
            'personal_sales' => 15000,
            'group_sales' => 50000,
            'group_size' => 4,
            'level' => 1,
            'phase1_amount' => 1500,
            'phase2_amount' => 2500,
            'total_payout' => 4000,
        ]);

        $response = $this->actingAs($user)
            ->get(route('shop.payout.slip', $payout->id));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/pdf');
    }

    public function test_shop_owner_cannot_access_other_shops_payout_slip(): void
    {
        [$user1, $shop1] = $this->createShopOwner('Seller 1');
        [$user2, $shop2] = $this->createShopOwner('Seller 2');

        $payout = ShopMonthlyPayout::create([
            'shop_id' => $shop2->id,
            'year' => 2026,
            'month' => 7,
            'personal_sales' => 10000,
            'group_sales' => 10000,
            'group_size' => 1,
            'level' => null,
            'phase1_amount' => 1000,
            'phase2_amount' => 0,
            'total_payout' => 1000,
        ]);

        $response = $this->actingAs($user1)
            ->get(route('shop.payout.slip', $payout->id));

        $response->assertForbidden();
    }

    public function test_admin_can_access_any_shop_payout_slip(): void
    {
        $admin = User::factory()->create(['is_active' => true]);
        $admin->assignRole(Role::findOrCreate('root', 'web'));

        [$user, $shop] = $this->createShopOwner('Beta Seller Shop');

        $payout = ShopMonthlyPayout::create([
            'shop_id' => $shop->id,
            'year' => 2026,
            'month' => 7,
            'personal_sales' => 20000,
            'group_sales' => 20000,
            'group_size' => 2,
            'level' => null,
            'phase1_amount' => 2000,
            'phase2_amount' => 0,
            'total_payout' => 2000,
        ]);

        $response = $this->actingAs($admin)
            ->get(route('admin.payout.slip', $payout->id));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/pdf');
    }
}
