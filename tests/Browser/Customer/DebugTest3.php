<?php

namespace Tests\Browser\Customer;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\Browser\Helpers;
use Tests\DuskTestCase;

class DebugTest3 extends DuskTestCase
{
    use Helpers;

    public function test_debug_login_response(): void
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

            // Capture the response
            $response = $browser->script([
                "return new Promise((resolve) => {
                    axios.post('/login', { phone: '{$email}', password: 'secret' })
                        .then(r => resolve('SUCCESS: ' + r.data.message))
                        .catch(e => resolve('ERROR: ' + (e.response?.data?.message || 'unknown')));
                });",
            ]);
            echo 'LOGIN RESPONSE: '.json_encode($response)."\n";

            $browser->press('Log in')->pause(3000);
            $html = $browser->driver->getPageSource();
            $hasSuccess = strpos($html, 'Login Successful') !== false;
            echo "HAS 'Login Successful' TEXT: ".($hasSuccess ? 'yes' : 'no')."\n";
        });
    }
}
