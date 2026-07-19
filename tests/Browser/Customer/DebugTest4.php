<?php

namespace Tests\Browser\Customer;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\Browser\Helpers;
use Tests\DuskTestCase;

class DebugTest4 extends DuskTestCase
{
    use Helpers;

    public function test_debug_wait_for_login(): void
    {
        $email = 'test'.time().rand(1000, 9999).'@example.com';
        $customer = User::factory()->create([
            'email' => $email,
            'password' => bcrypt('secret'),
        ]);
        $customer->assignRole('customer');
        $customer->customer()->create(['user_id' => $customer->id]);
        $this->cleanupIds[] = [User::class, $customer->id];

        $this->browse(function (Browser $browser) use ($email) {
            $browser->visit('/')
                ->waitForText('Buy Now', 15)
                ->press('Buy Now')
                ->waitForText('Please login first!', 5)
                ->pause(500);

            $browser->script([
                "const emailEl = document.querySelector('input[type=\"text\"]'); emailEl.value = '{$email}'; emailEl.dispatchEvent(new Event('input', {bubbles: true}));",
                "const passEl = document.querySelector('input[type=\"password\"]'); passEl.value = 'secret'; passEl.dispatchEvent(new Event('input', {bubbles: true}));",
            ]);
            $browser->pause(200);

            $browser->press('Log in');

            // Now wait for Login Successful with longer timeout
            $browser->waitForText('Login Successful', 15);
            echo "Found Login Successful!\n";
        });
    }
}
