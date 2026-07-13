<?php

namespace Tests\Browser;

use App\Models\TicketIssueType;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class AdminSupportTicketTest extends DuskTestCase
{
    use Helpers;

    public function test_support_ticket_list_loads(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin())
                ->visit('/admin/support-ticket')
                ->assertSee('All Help Requests');
        });
    }

    public function test_ticket_issue_type_list_loads(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin())
                ->visit('/admin/ticket-issue-type')
                ->assertSee('Ticket Issue Types');
        });
    }

    public function test_create_ticket_issue_type(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin())
                ->visit('/admin/ticket-issue-type')
                ->assertSee('Ticket Issue Types')
                ->press('Add Ticket Issue Type')
                ->waitFor('#createBrand')
                ->type('input[name="name"]', 'Test Ticket Issue Type Dusk')
                ->press('Save Issue Type')
                ->waitForText('Ticket issue type created successfully', 10);

            $record = TicketIssueType::where('name', 'Test Ticket Issue Type Dusk')->first();
            $this->assertNotNull($record);
            $this->cleanupIds[] = [TicketIssueType::class, $record->id];
        });
    }

    public function test_cleanup_ticket_issue_types(): void
    {
        TicketIssueType::where('name', 'Test Ticket Issue Type Dusk')->forceDelete();
        $this->assertNull(TicketIssueType::where('name', 'Test Ticket Issue Type Dusk')->first());
    }
}
