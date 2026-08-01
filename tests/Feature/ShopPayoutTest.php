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

class ShopPayoutTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
        $this->seed(PermissionSeeder::class);
    }

    private function shopOwner(): array
    {
        $user = User::factory()->create(['is_active' => true]);
        $user->assignRole(Role::findOrCreate('shop', 'web'));
        $user->givePermissionTo(['shop.payout.index', 'shop.payout.network']);

        $shop = Shop::create([
            'name' => 'Test Seller Shop',
            'user_id' => $user->id,
            'status' => true,
        ]);

        $user->refresh();

        return [$user, $shop];
    }

    public function test_shop_owner_can_access_payout_history(): void
    {
        [$user, $shop] = $this->shopOwner();

        ShopMonthlyPayout::create([
            'shop_id' => $shop->id,
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

        $response = $this->actingAs($user)
            ->get(route('shop.payout.index'));

        $response->assertOk();
        $response->assertSee('My Payouts & Earnings');
        $response->assertSee('1,000.00');
    }

    public function test_shop_owner_can_access_downline_network(): void
    {
        [$user, $shop] = $this->shopOwner();

        $response = $this->actingAs($user)
            ->get(route('shop.payout.network', ['year' => 2026, 'month' => 7]));

        $response->assertOk();
        $response->assertSee('My Downline Network');
        $response->assertSee('Test Seller Shop');
    }
}
