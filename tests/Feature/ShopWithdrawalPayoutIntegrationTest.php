<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\Address;
use App\Models\Customer;
use App\Models\GeneraleSetting;
use App\Models\Order;
use App\Models\Shop;
use App\Models\ShopMonthlyPayout;
use App\Models\User;
use App\Models\Wallet;
use App\Models\Withdraw;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShopWithdrawalPayoutIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleSeeder::class);
        $this->seed(PermissionSeeder::class);

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

    public function test_multi_shop_owner_pending_withdrawals_are_scoped_across_all_owned_shops(): void
    {
        $user = User::factory()->create();
        $user->assignRole('shop');
        $wallet = Wallet::create(['user_id' => $user->id, 'balance' => 3000]);

        $shopA = Shop::factory()->create(['user_id' => $user->id, 'name' => 'Shop A']);
        $shopB = Shop::factory()->create(['user_id' => $user->id, 'name' => 'Shop B']);

        // Request 2000 from Shop A -> Total wallet balance 3000 -> Withdrawable remaining is 1000
        Withdraw::create([
            'shop_id' => $shopA->id,
            'amount' => 2000,
            'name' => 'Multi Shop Owner',
            'contact_number' => '1234567890',
            'status' => 'pending',
        ]);

        // Requesting 1500 from Shop B should fail because remaining available balance across all shops is only 1000
        session(['shop' => $shopB]);
        $response = $this->actingAs($user)->postJson(route('shop.withdraw.store'), [
            'amount' => 1500,
            'name' => 'Multi Shop Owner',
            'contact_number' => '1234567890',
        ]);

        $response->assertStatus(422);
        $response->assertJsonFragment(['message' => 'Sorry! Insufficient balance!']);
    }

    public function test_order_delivery_credits_wallet_and_creates_order_sale_transaction(): void
    {
        $user = User::factory()->create();
        $user->assignRole('shop');
        $wallet = Wallet::create(['user_id' => $user->id, 'balance' => 0]);
        $shop = Shop::factory()->create(['user_id' => $user->id]);

        $customerUser = User::factory()->create();
        $customerUser->assignRole('customer');
        $customer = Customer::create(['user_id' => $customerUser->id]);
        $address = Address::create([
            'user_id' => $customerUser->id,
            'name' => 'John Doe',
            'phone' => '1234567890',
            'address' => 'Test Street',
        ]);

        $order = Order::create([
            'shop_id' => $shop->id,
            'customer_id' => $customer->id,
            'address_id' => $address->id,
            'order_code' => '1001',
            'prefix' => 'ORD',
            'total_amount' => 2500,
            'payable_amount' => 2500,
            'order_status' => OrderStatus::PENDING->value,
            'payment_status' => PaymentStatus::PENDING->value,
            'payment_method' => PaymentMethod::CASH->value,
        ]);

        session(['shop' => $shop]);
        $response = $this->actingAs($user)->put(route('shop.order.status.change', $order->id), [
            'status' => OrderStatus::DELIVERED->value,
        ]);

        $this->assertEquals(2250.0, $wallet->fresh()->balance);
        $this->assertDatabaseHas('transactions', [
            'wallet_id' => $wallet->id,
            'amount' => 2500,
            'type' => 'credit',
            'purpose' => 'order_sale',
        ]);
    }
}
