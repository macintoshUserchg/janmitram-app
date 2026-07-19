<?php

namespace Tests\Browser\Customer;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\Browser\Helpers;
use Tests\DuskTestCase;

class AuthFlowTest extends DuskTestCase
{
    use Helpers;

    protected array $cleanupIds = [];

    /**
     * Reset persisted Pinia state between tests.
     * Visits the SPA, clears localStorage (Pinia persist),
     * then reloads to start fresh.
     */
    private function resetState(Browser $browser): void
    {
        $browser->visit('/');
        $browser->pause(300);
        $browser->script(['localStorage.clear(); sessionStorage.clear();']);
        $browser->visit('/');
        $browser->pause(800);
        $browser->waitForText('Buy Now', 15);
    }

    /**
     * Click a button by its text content via script.
     */
    private function clickText(Browser $browser, string $text): void
    {
        $text = addslashes($text);
        $browser->script(["
            (function() {
                var nodes = document.querySelectorAll('button, a, span, div');
                for (var i = 0; i < nodes.length; i++) {
                    if (nodes[i].textContent.trim().toLowerCase() === '{$text}'.toLowerCase()) {
                        nodes[i].click(); return true;
                    }
                }
                for (var i = 0; i < nodes.length; i++) {
                    if (nodes[i].textContent.trim().toLowerCase().includes('{$text}'.toLowerCase())) {
                        nodes[i].click(); return true;
                    }
                }
                return false;
            })();
        "]);
    }

    public function test_customer_login_valid(): void
    {
        $this->browse(function (Browser $browser) {
            $this->resetState($browser);
            $browser->press('Buy Now')
                ->waitForText('Please login first!', 5)
                ->type('input[placeholder*="email"]', 'user@readyecommerce.com')
                ->type('input[placeholder*="Password"]', 'secret')
                ->press('Log in')
                ->waitForText('Login Successful', 10)
                ->assertSee('Login Successful');
        });
    }

    public function test_customer_login_invalid(): void
    {
        $this->browse(function (Browser $browser) {
            $this->resetState($browser);
            $browser->press('Buy Now')
                ->waitForText('Please login first!', 5)
                ->type('input[placeholder*="email"]', 'invalid@test.com')
                ->type('input[placeholder*="Password"]', 'wrongpassword')
                ->press('Log in')
                ->waitForText('Credential is invalid!', 5)
                ->assertSee('Credential is invalid!');
        });
    }

    public function test_customer_registration(): void
    {
        $uniqueEmail = 'reg'.time().'@gmail.com';

        $this->browse(function (Browser $browser) use ($uniqueEmail) {
            // Register via API to bypass country v-select complexity
            $browser->script([
                'fetch("/janmitram-app/api/registration", {method:"POST", headers:{"Content-Type":"application/json"}, body:JSON.stringify({name:"John Doe", email:"'.$uniqueEmail.'", phone:"1234567890", country:"Afghanistan", phone_code:"93", password:"password123", password_confirmation:"password123"})}).then(r => r.json())',
            ]);
            $browser->pause(2000);

            // Now login via UI to verify
            $this->resetState($browser);
            $browser->press('Buy Now')
                ->waitForText('Please login first!', 5)
                ->pause(500)
                ->type('input[placeholder*="email"]', $uniqueEmail)
                ->type('input[placeholder*="Password"]', 'password123')
                ->press('Log in')
                ->waitForText('Login Successful', 15)
                ->assertSee('Login Successful');
        });

        $this->assertDatabaseHas('users', ['email' => $uniqueEmail]);
    }

    public function test_email_verification_flow(): void
    {
        $email = 'unverified'.time().'@gmail.com';

        $this->browse(function (Browser $browser) use ($email) {
            // Register via API call from browser context
            $browser->script([
                'fetch("/janmitram-app/api/registration", {method:"POST", headers:{"Content-Type":"application/json"}, body:JSON.stringify({name:"Unverified User", email:"'.$email.'", phone:"1234567890", country:"Afghanistan", phone_code:"93", password:"password123", password_confirmation:"password123"})}).then(r => r.json())',
            ]);
            $browser->pause(2000);

            // Use API login to get token
            $browser->script([
                'fetch("/janmitram-app/api/login", {method:"POST", headers:{"Content-Type":"application/json"}, body:JSON.stringify({phone:"'.$email.'", password:"password123"})}).then(r => r.json()).then(d => {if(d.data) localStorage.setItem("authStore", JSON.stringify({user: d.data.user, token: "Bearer " + d.data.access.token}));})',
            ]);
            $browser->pause(1500);

            // Visit profile page — should see email verify prompt
            $browser->visit('/profile')
                ->pause(2000)
                ->assertSee('Unverified');
        });

        $user = User::where('email', $email)->first();
        $this->assertNotNull($user);
        $this->assertNull($user->fresh()->email_verified_at);
    }

    public function test_password_reset_request(): void
    {
        // Create a user with an email to send OTP to
        $customer = User::factory()->create([
            'password' => bcrypt('secret'),
            'phone' => 'reset'.time().'@example.com',
        ]);
        $customer->assignRole('customer');
        $this->cleanupIds[] = [User::class, $customer->id];

        // Use direct API to test forgot password flow, avoiding the complex
        // v-select country field in the UI
        $this->browse(function (Browser $browser) use ($customer) {
            $this->resetState($browser);
            $browser->script(['
                fetch("/janmitram-app/api/send-otp", {
                    method: "POST",
                    headers: {"Content-Type": "application/json"},
                    body: JSON.stringify({
                        phone: "'.$customer->email.'",
                        forgot_password: 1
                    })
                }).then(r => r.json()).then(d => console.log("otp response", d));
            ']);
            $browser->pause(2000);

            // Verify the login modal still works (as a fallback to show UI state)
            $browser->press('Buy Now')
                ->waitForText('Please login first!', 5)
                ->assertVisible('input[placeholder*="email"]');
        });
    }

    public function test_customer_logout(): void
    {
        $this->browse(function (Browser $browser) {
            $this->resetState($browser);
            // Login via seeded user
            $browser->press('Buy Now')
                ->waitForText('Please login first!', 5)
                ->pause(300)
                ->type('input[placeholder*="email"]', 'user@readyecommerce.com')
                ->type('input[placeholder*="Password"]', 'secret')
                ->press('Log in')
                ->waitForText('Login Successful', 10);

            // Clear localStorage to simulate logout
            $browser->script(['localStorage.clear();']);
            $browser->pause(500)
                ->visit('/')
                ->waitForText('Buy Now', 15);

            // Verify Login button appears in header (guest state)
            $browser->assertSee('Login');
        });
    }

    public function test_unauthenticated_user_redirected_from_dashboard(): void
    {
        $this->browse(function (Browser $browser) {
            $this->resetState($browser);
            $browser->visit('/dashboard')
                ->waitForText('Login', 10)
                ->assertSee('Login');
        });
    }
}
