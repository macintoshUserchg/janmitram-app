<?php

namespace Tests\Browser;

use App\Models\SubscriptionPlan;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class AdminSubscriptionTest extends DuskTestCase
{
    use Helpers;

    public function test_subscription_plan_list_loads(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin())
                ->visit('/admin/subscription-plan')
                ->waitForText('Subscription Plans', 5)
                ->assertSee('Subscription Plans');
        });
    }

    public function test_create_subscription_plan(): void
    {
        $this->browse(function (Browser $browser) {
            $name = 'Plan Dusk '.time();
            $browser->loginAs($this->admin())
                ->visit('/admin/subscription-plan/create')
                ->assertSee('Create New Plan')
                ->value('input[name="name"]', $name)
                ->value('textarea[name="short_description"]', 'Short description for dusk test')
                ->value('input[name="price"]', '10')
                ->value('input[name="duration"]', '30')
                ->value('input[name="sale_limit"]', '100');

            $this->fillQuill($browser, '<p>Test description</p>');

            $browser->script(["document.getElementById('is_popular').checked = true;"]);
            $browser->press('Submit')
                ->waitForText('Created successfully', 10);

            $record = SubscriptionPlan::where('name', $name)->first();
            $this->assertNotNull($record);
            $this->cleanupIds[] = [SubscriptionPlan::class, $record->id];
        });
    }

    public function test_subscription_list_loads(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin())
                ->visit('/admin/subscription-plan/subscriptions')
                ->assertSee('Subscription List');
        });
    }

    public function test_toggle_plan_status(): void
    {
        $record = SubscriptionPlan::create([
            'name' => 'Plan Toggle Dusk '.time(),
            'short_description' => 'Toggle test',
            'description' => '<p>Toggle test</p>',
            'price' => 10,
            'duration' => 30,
            'sale_limit' => 100,
            'is_popular' => 0,
            'is_active' => 1,
        ]);
        $this->assertNotNull($record);
        $originalStatus = (bool) $record->is_active;
        $this->cleanupIds[] = [SubscriptionPlan::class, $record->id];

        $this->browse(function (Browser $browser) use ($record) {
            $browser->loginAs($this->admin())
                ->visit('/admin/subscription-plan')
                ->visit("/admin/subscription-plan/{$record->id}/toggle")
                ->waitForText('Status updated successfully', 10);
        });

        $record->refresh();
        $this->assertNotEquals($originalStatus, (bool) $record->is_active);
    }

    public function test_cleanup_subscription_plans(): void
    {
        SubscriptionPlan::where('name', 'like', 'Plan % Dusk %')->forceDelete();
        $this->assertNull(SubscriptionPlan::where('name', 'like', 'Plan % Dusk %')->first());
    }
}
