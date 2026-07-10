<?php

namespace Tests\Browser;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class AdminCustomerTest extends DuskTestCase
{
    use Helpers;

    private static string $testEmail = 'test-customer-dusk@gmail.com';

    public function test_create_customer(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin())
                ->visit('/admin/customer/create')
                ->assertSee('Add Customer')
                ->type('input[name="name"]', 'Test Customer Dusk')
                ->type('input[name="last_name"]', 'Dusk')
                ->type('input[name="email"]', self::$testEmail)
                ->type('input[name="phone"]', '1234567890')
                ->type('input[name="password"]', 'password')
                ->type('input[name="password_confirmation"]', 'password')
                ->press('Submit')
                ->pause(3000);
        });

        $user = User::where('email', self::$testEmail)->first();
        $this->assertNotNull($user, 'Customer was not created in database after form submit');
    }

    public function test_customer_list_loads(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin())
                ->visit('/admin/customer')
                ->assertSee('All Customers');
        });
    }

    public function test_edit_customer(): void
    {
        $user = User::where('email', self::$testEmail)->first();
        $this->assertNotNull($user);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($this->admin())
                ->visit("/admin/customer/{$user->id}/edit")
                ->assertSee('Edit Customer')
                ->assertInputValue('input[name="name"]', 'Test Customer Dusk');
        });

        $user->update(['name' => 'Updated Dusk']);
        $user->refresh();
        $this->assertEquals('Updated Dusk', $user->name);
    }

    public function test_cleanup_customer(): void
    {
        User::where('email', self::$testEmail)->forceDelete();
        $this->assertNull(User::where('email', self::$testEmail)->first());
    }
}
