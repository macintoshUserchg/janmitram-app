<?php

namespace Tests\Feature;

use App\Models\GeneraleSetting;
use App\Models\Shop;
use App\Models\ShopMonthlyPayout;
use App\Models\User;
use App\Models\Wallet;
use App\Models\Withdraw;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShopWithdrawalPayoutIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\RoleSeeder::class);
        $this->seed(\Database\Seeders\PermissionSeeder::class);

        GeneraleSetting::create([
            'name' => 'Janmitram',
            'currency' => '₹',
            'min_withdraw' => 100,
            'max_withdraw' => 50000,
            'withdraw_request' => 0,
        ]);
    }

    public function test_shop_owner_can_view_withdrawal_page_with_financial_metrics(): void
    {
        $user = User::factory()->create();
        $user->assignRole('shop');
        $wallet = Wallet::create(['user_id' => $user->id, 'balance' => 5000]);
        $shop = Shop::factory()->create(['user_id' => $user->id]);

        ShopMonthlyPayout::create([
            'shop_id' => $shop->id,
            'year' => 2026,
            'month' => 8,
            'personal_sales' => 10000,
            'group_sales' => 10000,
            'group_size' => 1,
            'level' => 0,
            'phase1_amount' => 1000,
            'phase2_amount' => 3000,
            'total_payout' => 4000,
        ]);

        Withdraw::create([
            'shop_id' => $shop->id,
            'amount' => 1000,
            'name' => 'Test User',
            'contact_number' => '1234567890',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($user)->get(route('shop.withdraw.index'));

        $response->assertStatus(200);
        $response->assertViewHas('walletBalance', 5000.0);
        $response->assertViewHas('pendingWithdraws', 1000.0);
        $response->assertViewHas('withdrawableBalance', 4000.0);
        $response->assertViewHas('lifetimePayouts', 4000.0);
    }

    public function test_pending_withdrawals_reserve_balance_and_prevent_excess_requests(): void
    {
        $user = User::factory()->create();
        $user->assignRole('shop');
        $wallet = Wallet::create(['user_id' => $user->id, 'balance' => 2000]);
        $shop = Shop::factory()->create(['user_id' => $user->id]);

        // Pending withdrawal of 1500 -> Withdrawable balance is 500
        Withdraw::create([
            'shop_id' => $shop->id,
            'amount' => 1500,
            'name' => 'Test User',
            'contact_number' => '1234567890',
            'status' => 'pending',
        ]);

        // Requesting 1000 should fail because remaining available balance is only 500
        $response = $this->actingAs($user)->postJson(route('shop.withdraw.store'), [
            'amount' => 1000,
            'name' => 'Test User',
            'contact_number' => '1234567890',
        ]);

        $response->assertStatus(422);
        $response->assertJsonFragment(['message' => 'Sorry! Insufficient balance!']);
    }

    public function test_admin_approval_debits_wallet_and_logs_payout_withdraw_transaction(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('root');

        $user = User::factory()->create();
        $user->assignRole('shop');
        $wallet = Wallet::create(['user_id' => $user->id, 'balance' => 3000]);
        $shop = Shop::factory()->create(['user_id' => $user->id]);

        $withdraw = Withdraw::create([
            'shop_id' => $shop->id,
            'amount' => 1200,
            'name' => 'Test User',
            'contact_number' => '1234567890',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($admin)->put(route('admin.withdraw.update', $withdraw->id), [
            'status' => 'approved',
            'reason' => 'Approved by admin',
        ]);

        $response->assertRedirect();
        $this->assertEquals('approved', $withdraw->fresh()->status);
        $this->assertEquals(1800.0, $wallet->fresh()->balance);

        $this->assertDatabaseHas('transactions', [
            'wallet_id' => $wallet->id,
            'amount' => 1200,
            'type' => 'debit',
            'purpose' => 'payout_withdraw',
        ]);
    }
}
