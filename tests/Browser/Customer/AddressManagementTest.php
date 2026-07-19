<?php

namespace Tests\Browser\Customer;

use App\Models\Address;
use App\Models\Area;
use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\Browser\Helpers;
use Tests\DuskTestCase;

class AddressManagementTest extends DuskTestCase
{
    use Helpers;

    protected array $cleanupIds = [];

    private function resetState(Browser $browser): void
    {
        $browser->visit('/');
        $browser->pause(300);
        $browser->script(['localStorage.clear(); sessionStorage.clear();']);
        $browser->visit('/');
        $browser->pause(800);
        $browser->waitForText('Buy Now', 15);
    }

    private function login(Browser $browser, string $email, string $password): void
    {
        $browser->press('Buy Now')
            ->waitForText('Please login first!', 5)
            ->type('input[placeholder*="email"]', $email)
            ->type('input[placeholder*="Password"]', $password)
            ->press('Log in')
            ->waitForText('Login Successful', 10);
    }

    public function test_add_address(): void
    {
        $customer = User::factory()->create([
            'password' => bcrypt('secret'),
            'phone' => 'add_addr_'.time().'@test.com',
        ]);
        $customer->assignRole('customer');
        $customer->customer()->create(['user_id' => $customer->id]);
        $this->cleanupIds[] = [User::class, $customer->id];

        $area = Area::factory()->create();
        $this->cleanupIds[] = [Area::class, $area->id];

        $this->browse(function (Browser $browser) use ($customer, $area) {
            $this->resetState($browser);
            $this->login($browser, $customer->email, 'secret');

            $browser->visit('/manage-address/new')
                ->waitForText('Add New Address', 5)
                ->type('input[name="name"]', 'John Home')
                ->type('input[name="phone"]', '1234567890')
                ->select('select[name="address_type"]', 'Home')
                ->type('input[name="address_line"]', '123 Main St')
                ->type('input[name="area"]', $area->name)
                ->press('Save')
                ->waitForText('Address added successfully', 5)
                ->assertSee('Address added successfully');
        });

        $this->assertDatabaseHas('addresses', [
            'customer_id' => $customer->customer->id,
            'name' => 'John Home',
            'address_line' => '123 Main St',
        ]);
    }

    public function test_edit_address(): void
    {
        $customer = User::factory()->create([
            'password' => bcrypt('secret'),
            'phone' => 'edit_addr_'.time().'@test.com',
        ]);
        $customer->assignRole('customer');
        $customer->customer()->create(['user_id' => $customer->id]);
        $this->cleanupIds[] = [User::class, $customer->id];

        $area = Area::factory()->create();
        $this->cleanupIds[] = [Area::class, $area->id];

        $address = Address::factory()->create([
            'customer_id' => $customer->customer->id,
            'name' => 'Old Name',
        ]);
        $this->cleanupIds[] = [Address::class, $address->id];

        $this->browse(function (Browser $browser) use ($customer, $address) {
            $this->resetState($browser);
            $this->login($browser, $customer->email, 'secret');

            $browser->visit("/manage-address/{$address->id}/edit")
                ->waitForText('Edit Address', 5)
                ->type('input[name="name"]', 'New Name')
                ->press('Save')
                ->waitForText('Address updated successfully', 5)
                ->assertSee('Address updated successfully');
        });

        $this->assertEquals('New Name', $address->fresh()->name);
    }

    public function test_delete_address(): void
    {
        $customer = User::factory()->create([
            'password' => bcrypt('secret'),
            'phone' => 'del_addr_'.time().'@test.com',
        ]);
        $customer->assignRole('customer');
        $customer->customer()->create(['user_id' => $customer->id]);
        $this->cleanupIds[] = [User::class, $customer->id];

        $address = Address::factory()->create([
            'customer_id' => $customer->customer->id,
        ]);
        $this->cleanupIds[] = [Address::class, $address->id];

        $this->browse(function (Browser $browser) use ($customer) {
            $this->resetState($browser);
            $this->login($browser, $customer->email, 'secret');

            $browser->visit('/manage-address')
                ->waitForText('Manage Address', 5)
                ->press('Delete')
                ->waitForText('Address deleted successfully', 5)
                ->assertSee('Address deleted successfully');
        });

        $this->assertDatabaseMissing('addresses', ['id' => $address->id]);
    }

    public function test_set_default_address(): void
    {
        $customer = User::factory()->create([
            'password' => bcrypt('secret'),
            'phone' => 'def_addr_'.time().'@test.com',
        ]);
        $customer->assignRole('customer');
        $customer->customer()->create(['user_id' => $customer->id]);
        $this->cleanupIds[] = [User::class, $customer->id];

        $address1 = Address::factory()->create([
            'customer_id' => $customer->customer->id,
            'is_default' => true,
        ]);
        $this->cleanupIds[] = [Address::class, $address1->id];

        $address2 = Address::factory()->create([
            'customer_id' => $customer->customer->id,
            'is_default' => false,
        ]);
        $this->cleanupIds[] = [Address::class, $address2->id];

        $this->browse(function (Browser $browser) use ($customer, $address2) {
            $this->resetState($browser);
            $this->login($browser, $customer->email, 'secret');

            $browser->visit('/manage-address')
                ->waitForText('Manage Address', 5)
                ->press("@set-default-{$address2->id}")
                ->waitForText('Default address updated', 5)
                ->assertSee('Default address updated');
        });

        $this->assertTrue($address2->fresh()->is_default);
    }

    public function test_delivery_charge_calculation(): void
    {
        $customer = User::factory()->create([
            'password' => bcrypt('secret'),
            'phone' => 'del_charge_'.time().'@test.com',
        ]);
        $customer->assignRole('customer');
        $customer->customer()->create(['user_id' => $customer->id]);
        $this->cleanupIds[] = [User::class, $customer->id];

        $area = Area::factory()->create(['delivery_amount' => 50]);
        $this->cleanupIds[] = [Area::class, $area->id];

        Address::factory()->create([
            'customer_id' => $customer->customer->id,
            'area_id' => $area->id,
            'is_default' => true,
        ]);

        $this->browse(function (Browser $browser) use ($customer) {
            $this->resetState($browser);
            $this->login($browser, $customer->email, 'secret');

            $browser->visit('/checkout')
                ->waitForText('Checkout', 5)
                ->assertSee('Delivery Charge')
                ->assertSee('50.00');
        });
    }

    public function test_address_validation_empty_fields(): void
    {
        $customer = User::factory()->create([
            'password' => bcrypt('secret'),
            'phone' => 'val_addr_'.time().'@test.com',
        ]);
        $customer->assignRole('customer');
        $customer->customer()->create(['user_id' => $customer->id]);
        $this->cleanupIds[] = [User::class, $customer->id];

        $this->browse(function (Browser $browser) use ($customer) {
            $this->resetState($browser);
            $this->login($browser, $customer->email, 'secret');

            $browser->visit('/manage-address/new')
                ->waitForText('Add New Address', 5)
                ->press('Save')
                ->waitForText('required', 5)
                ->assertSee('required');
        });
    }
}
