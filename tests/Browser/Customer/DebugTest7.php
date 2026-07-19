<?php

namespace Tests\Browser\Customer;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\Browser\Helpers;
use Tests\DuskTestCase;

class DebugTest7 extends DuskTestCase
{
    use Helpers;

    public function test_login_with_type(): void
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
                ->pause(500)
                // Use placeholder to target the correct input
                ->type('input[placeholder*="email"]', $email)
                ->type('input[placeholder*="Password"]', 'secret')
                ->press('Log in')
                ->pause(2000);

            $toastText = $browser->script([
                "const t = document.querySelector('.Vue-Toastification__toast'); return t ? t.textContent : 'NO TOAST';",
            ]);
            echo 'TOAST: '.json_encode($toastText)."\n";

            $pageHtml1 = $browser->driver->getPageSource();
            $hasLoginSuccess = strpos($pageHtml1, 'Login Successful') !== false;
            echo "HAS 'Login Successful' text: ".($hasLoginSuccess ? 'yes' : 'no')."\n";

            // Check if modal is gone
            $modalPresent = $browser->script([
                "return document.querySelector('.fixed.inset-0.z-10') !== null;",
            ]);
            echo 'MODAL PRESENT: '.json_encode($modalPresent)."\n";
        });
    }
}
