<?php

namespace Tests\Browser;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class AdminEmployeeRoleTest extends DuskTestCase
{
    use Helpers;

    public function test_role_list_page_loads(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin())
                ->visit('/admin/role')
                ->assertSee('Roles & Permissions');
        });
    }

    public function test_employee_list_page_loads(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin())
                ->visit('/admin/employee')
                ->assertSee('Employees');
        });
    }

    public function test_create_employee(): void
    {
        $email = 'test-employee-dusk@gmail.com';

        $this->browse(function (Browser $browser) use ($email) {
            $browser->loginAs($this->admin())
                ->visit('/admin/employee/create')
                ->assertSee('Create New Employee')
                ->type('name', 'Test')
                ->type('last_name', 'Employee')
                ->type('phone', '1234567890')
                ->type('email', $email);

            // select2 for role
            $this->selectByValue($browser, 'select[name="role"]', 'admin');

            $browser->type('input[name="password"]', 'password')
                ->type('input[name="password_confirmation"]', 'password')
                ->press('Submit')
                ->waitForText('Employees', 10);
        });

        $user = User::where('email', $email)->first();
        $this->assertNotNull($user);
        $this->assertTrue($user->hasRole('admin'));

        $this->cleanupIds[] = [User::class, $user->id];
    }
}
