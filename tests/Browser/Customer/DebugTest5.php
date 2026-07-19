<?php

namespace Tests\Browser\Customer;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\Browser\Helpers;
use Tests\DuskTestCase;

class DebugTest5 extends DuskTestCase
{
    use Helpers;

    public function test_debug_toast_content(): void
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
            $browser->pause(600);

            $toastHtml = $browser->script([
                "const toast = document.querySelector('.Vue-Toastification__toast'); return toast ? toast.textContent : 'NO TOAST';",
            ]);
            echo 'TOAST TEXT: '.json_encode($toastHtml)."\n";

            $allHtml = $browser->script([
                "const c = document.querySelector('.Vue-Toastification__container'); return c ? c.innerHTML.substring(0, 1000) : 'NO CONTAINER';",
            ]);
            echo 'CONTAINER HTML: '.json_encode($allHtml)."\n";
        });
    }
}
