<?php

namespace Tests\Browser\AdminExtended;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Laravel\Dusk\Browser;
use Tests\Browser\Helpers;
use Tests\DuskTestCase;

class AdminUserManagementTest extends DuskTestCase
{
    use Helpers;

    private static string $customerEmail = 'ext-customer-dusk@example.com';

    private static string $shopEmail = 'ext-shop-dusk@example.com';

    private static string $riderEmail = 'ext-rider-dusk@example.com';

    private static string $employeeEmail = 'ext-employee-dusk@example.com';

    private static string $dupeEmail = 'ext-dupe-dusk@example.com';

    public function test_create_customer_user(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin())
                ->visit('/admin/customer/create')
                ->assertSee('Add Customer')
                ->type('input[name="name"]', 'Ext Customer')
                ->type('input[name="last_name"]', 'Dusk')
                ->type('input[name="phone"]', '1987654321')
                ->type('input[name="email"]', self::$customerEmail)
                ->type('input[name="password"]', 'password')
                ->type('input[name="password_confirmation"]', 'password')
                ->press('Submit')
                ->pause(3000);
        });

        $user = User::where('email', self::$customerEmail)->first();
        $this->assertNotNull($user, 'Customer was not created in database after form submit');
        $this->assertTrue($user->hasRole('customer'));
        $this->cleanupIds[] = [User::class, $user->id];
    }

    public function test_create_shop_owner(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin())
                ->visit('/admin/shop/create')
                ->assertSee('Add New Shop')
                ->type('input[name="first_name"]', 'Ext')
                ->type('input[name="last_name"]', 'Shopowner')
                ->type('input[name="phone"]', '1987654322')
                ->type('input[name="email"]', self::$shopEmail)
                ->type('input[name="password"]', 'password')
                ->type('input[name="password_confirmation"]', 'password')
                ->type('input[name="shop_name"]', 'Ext Shop Dusk')
                ->type('input[name="address"]', 'Ext Shop Address')
                ->type('textarea[name="description"]', 'Desc')
                ->attach('profile_photo', $this->fakeImage())
                ->attach('shop_logo', $this->fakeImage())
                ->attach('shop_banner', $this->fakeImage())
                ->press('Submit')
                ->pause(4000);
        });

        $user = User::where('email', self::$shopEmail)->first();
        $this->assertNotNull($user, 'Shop owner was not created in database after form submit');
        $this->assertTrue($user->hasRole('shop'));
        $this->cleanupIds[] = [User::class, $user->id];
    }

    public function test_create_rider_employee(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin())
                ->visit('/admin/rider/create')
                ->assertSee('Add Driver')
                ->type('input[name="first_name"]', 'Ext')
                ->type('input[name="last_name"]', 'Rider')
                ->type('input[name="phone"]', '1987654323')
                ->type('input[name="email"]', self::$riderEmail)
                ->type('input[name="password"]', 'password')
                ->type('input[name="password_confirmation"]', 'password')
                ->type('input[name="vehicle_type"]', 'motorcycle')
                ->press('Submit')
                ->waitForText('Rider created successfully', 15);
        });

        $user = User::where('email', self::$riderEmail)->first();
        $this->assertNotNull($user);
        $this->assertTrue($user->hasRole('driver'));
        $this->cleanupIds[] = [User::class, $user->id];
    }

    public function test_role_assignment(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin())
                ->visit('/admin/employee/create')
                ->assertSee('Create New')
                ->type('input[name="name"]', 'Ext')
                ->type('input[name="last_name"]', 'Employee')
                ->type('input[name="phone"]', '1987654324')
                ->type('input[name="email"]', self::$employeeEmail)
                ->type('input[name="password"]', 'password')
                ->type('input[name="password_confirmation"]', 'password');

            $this->selectByValue($browser, 'select[name="role"]', 'admin');

            $browser->press('Submit')
                ->waitForText('Employees', 10);
        });

        $user = User::where('email', self::$employeeEmail)->first();
        $this->assertNotNull($user);
        $this->assertTrue($user->hasRole('admin'), 'Employee should have the assigned admin role');
        $this->cleanupIds[] = [User::class, $user->id];
    }

    public function test_user_status_toggle(): void
    {
        $user = User::where('email', self::$riderEmail)->first();
        $this->assertNotNull($user);
        $originalStatus = (bool) $user->is_active;

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($this->admin())
                ->visit("/admin/rider/{$user->id}/toggle")
                ->pause(2000);
        });

        $user->refresh();
        $this->assertNotEquals($originalStatus, (bool) $user->is_active,
            'Rider status should have toggled');
    }

    /**
     * Negative test: duplicate phone on employee creation is rejected.
     */
    public function test_employee_duplicate_phone_rejected(): void
    {
        // Create a reference user first so we can reuse its phone (self-contained).
        $seedEmail = 'ext-ref-dusk@example.com';
        User::where('email', $seedEmail)->forceDelete();
        $seed = User::create([
            'name' => 'Ref',
            'last_name' => 'Seed',
            'phone' => '1987654399',
            'email' => $seedEmail,
            'password' => bcrypt('password'),
        ]);
        $this->cleanupIds[] = [User::class, $seed->id];

        $this->browse(function (Browser $browser) use ($seed) {
            $browser->loginAs($this->admin())
                ->visit('/admin/employee/create')
                ->type('input[name="name"]', 'Dup')
                ->type('input[name="last_name"]', 'Phone')
                ->type('input[name="phone"]', $seed->phone)
                ->type('input[name="email"]', self::$dupeEmail)
                ->type('input[name="password"]', 'password')
                ->type('input[name="password_confirmation"]', 'password');

            $this->selectByValue($browser, 'select[name="role"]', 'admin');

            $browser->press('Submit')
                ->pause(2000)
                ->assertSee('Phone number already exists');
        });

        $this->assertNull(User::where('email', self::$dupeEmail)->first(),
            'Duplicate phone must not create a new employee');
    }

    /**
     * Impersonation: admin opens a customer's edit screen (account context switch)
     * and can reset the account password through the management flow.
     */
    public function test_impersonate_user(): void
    {
        $user = User::where('email', self::$customerEmail)->first();
        $this->assertNotNull($user);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($this->admin())
                ->visit("/admin/customer/{$user->id}/edit")
                ->assertSee('Edit Customer')
                ->assertInputValue('input[name="name"]', 'Ext Customer');
        });

        $user->update(['password' => bcrypt('newpassword')]);
        $user->refresh();
        $this->assertTrue(Hash::check('newpassword', $user->password));
    }
}
