<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class AdminLoginTest extends DuskTestCase
{
    use Helpers;

    public function test_login_page_loads(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/admin/login')
                ->assertSee('Login To Admin');
        });
    }

    public function test_admin_can_login(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/admin/login')
                ->type('email', 'root@readyecommerce.com')
                ->type('password', 'secret')
                ->press('Login')
                ->pause(3000)
                ->screenshot('after-login')
                ->assertSee('Dashboard');
        });
    }

    public function test_authenticated_admin_sees_dashboard(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin())
                ->visit('/admin/dashboard')
                ->assertSee('Dashboard');
        });
    }
}
