<?php

namespace Tests\Browser;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class AdminRiderTest extends DuskTestCase
{
    use Helpers;

    private static string $testEmail = 'test-rider-dusk@gmail.com';

    public function test_create_rider(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin())
                ->visit('/admin/rider/create')
                ->assertSee('Add Driver')
                ->type('input[name="first_name"]', 'Test')
                ->type('input[name="last_name"]', 'Rider Dusk')
                ->type('input[name="email"]', self::$testEmail)
                ->type('input[name="phone"]', '1234567899')
                ->type('input[name="password"]', 'password')
                ->type('input[name="password_confirmation"]', 'password')
                ->type('input[name="vehicle_type"]', 'motorcycle')
                ->press('Submit')
                ->waitForText('Rider created successfully', 15);
        });

        $user = User::where('email', self::$testEmail)->first();
        $this->assertNotNull($user);
    }

    public function test_rider_list_loads(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin())
                ->visit('/admin/rider')
                ->assertSee('All Drivers');
        });
    }

    public function test_rider_detail_loads(): void
    {
        $user = User::where('email', self::$testEmail)->first();
        $this->assertNotNull($user);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($this->admin())
                ->visit("/admin/rider/{$user->id}")
                ->assertSee('Rider Details');
        });
    }

    public function test_cleanup_rider(): void
    {
        User::where('email', self::$testEmail)->forceDelete();
        $this->assertNull(User::where('email', self::$testEmail)->first());
    }
}
