<?php

namespace Tests\Feature;

use App\Models\GeneraleSetting;
use App\Models\Shop;
use App\Models\ShopKyc;
use App\Models\User;
use App\Models\Withdraw;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminWithdrawalManagementTest extends TestCase
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

    private function rootUser(): User
    {
        $user = User::factory()->create(['email' => 'admin_test_'.uniqid().'@example.com']);
        $user->assignRole('root');

        return $user;
    }

    public function test_admin_can_view_withdrawal_index_with_kpi_summary(): void
    {
        $admin = $this->rootUser();
        $shop = Shop::factory()->create(['name' => 'Jaipur Central Shop']);

        Withdraw::create([
            'shop_id' => $shop->id,
            'amount' => 5000,
            'name' => 'Shop Owner',
            'contact_number' => '9876543210',
            'status' => 'approved',
            'created_at' => now(),
        ]);

        Withdraw::create([
            'shop_id' => $shop->id,
            'amount' => 2500,
            'name' => 'Shop Owner',
            'contact_number' => '9876543210',
            'status' => 'pending',
            'created_at' => now(),
        ]);

        $response = $this->actingAs($admin)->get(route('admin.withdraw.index'));

        $response->assertOk();
        $response->assertSee('Withdrawal Management');
        $response->assertSee('Jaipur Central Shop');
        $response->assertViewHas('summary');
        $summary = $response->viewData('summary');

        $this->assertEquals(2, $summary['total_count']);
        $this->assertEquals(7500.0, $summary['total_amount']);
        $this->assertEquals(1, $summary['pending_count']);
        $this->assertEquals(2500.0, $summary['pending_amount']);
        $this->assertEquals(1, $summary['approved_count']);
        $this->assertEquals(5000.0, $summary['approved_amount']);
    }

    public function test_admin_can_filter_withdrawals_by_status_and_period(): void
    {
        $admin = $this->rootUser();
        $shop = Shop::factory()->create();

        Withdraw::create([
            'shop_id' => $shop->id,
            'amount' => 1000,
            'status' => 'pending',
            'created_at' => now(),
        ]);

        Withdraw::create([
            'shop_id' => $shop->id,
            'amount' => 3000,
            'status' => 'approved',
            'created_at' => now(),
        ]);

        $response = $this->actingAs($admin)->get(route('admin.withdraw.index', ['status' => 'pending']));
        $response->assertOk();

        $withdraws = $response->viewData('withdraws');
        $this->assertCount(1, $withdraws);
        $this->assertEquals('pending', $withdraws->first()->status);
    }

    public function test_admin_can_export_filtered_withdrawals_to_csv(): void
    {
        $admin = $this->rootUser();
        $shop = Shop::factory()->create(['name' => 'Ajmer Organic Hub']);
        ShopKyc::create([
            'shop_id' => $shop->id,
            'bank_name' => 'State Bank of India',
            'account_number' => '123456789012',
            'ifsc' => 'SBIN0001234',
        ]);

        Withdraw::create([
            'shop_id' => $shop->id,
            'amount' => 12500,
            'name' => 'Ajmer Owner',
            'contact_number' => '9988776655',
            'status' => 'approved',
            'reason' => 'Monthly payout settlement',
            'created_at' => now(),
        ]);

        $response = $this->actingAs($admin)->get(route('admin.withdraw.export', ['status' => 'approved']));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');

        $content = $response->streamedContent();
        $this->assertStringContainsString('Request ID', $content);
        $this->assertStringContainsString('Ajmer Organic Hub', $content);
        $this->assertStringContainsString('State Bank of India', $content);
        $this->assertStringContainsString('12500.00', $content);
    }
}
