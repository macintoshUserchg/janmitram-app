<?php

namespace Tests\Browser\Customer;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\Browser\Helpers;
use Tests\DuskTestCase;

class DebugTest6 extends DuskTestCase
{
    use Helpers;

    public function test_debug_input_selectors(): void
    {
        $email = 'test'.time().rand(1000, 9999).'@example.com';
        $customer = User::factory()->create([
            'email' => $email,
            'password' => bcrypt('secret'),
        ]);
        $customer->assignRole('customer');
        $customer->customer()->create(['user_id' => $customer->id]);
        $this->cleanupIds[] = [User::class, $customer->id];

        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->waitForText('Buy Now', 15)
                ->press('Buy Now')
                ->waitForText('Please login first!', 5)
                ->pause(1000);

            // Check all text inputs and their placeholders
            $inputs = $browser->script([
                "return Array.from(document.querySelectorAll('input[type=\"text\"]')).map(el => ({id: el.id, name: el.name, placeholder: el.placeholder, class: el.className.substring(0,60)}));",
            ]);
            echo 'TEXT INPUTS: '.json_encode($inputs, JSON_PRETTY_PRINT)."\n";

            // Check the modal specifically
            $modalInputs = $browser->script([
                "const modal = document.querySelector('.fixed.inset-0.z-10'); if (!modal) return 'NO MODAL'; return Array.from(modal.querySelectorAll('input')).map(el => ({type: el.type, name: el.name, placeholder: el.placeholder}));",
            ]);
            echo 'MODAL INPUTS: '.json_encode($modalInputs, JSON_PRETTY_PRINT)."\n";
        });
    }
}
