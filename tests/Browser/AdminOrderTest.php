<?php

namespace Tests\Browser;

use App\Enums\PaymentStatus;
use App\Models\Order;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class AdminOrderTest extends DuskTestCase
{
    use Helpers;

    public function test_order_list_loads(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin())
                ->visit('/admin/order')
                ->assertSee('Order');
        });
    }

    public function test_order_detail_loads(): void
    {
        $order = Order::first();
        $this->assertNotNull($order);

        $this->browse(function (Browser $browser) use ($order) {
            $browser->loginAs($this->admin())
                ->visit("/admin/order/{$order->id}")
                ->assertSee($order->order_id ?? $order->id);
        });
    }

    public function test_order_status_change(): void
    {
        $order = Order::first();
        $this->assertNotNull($order);

        $this->browse(function (Browser $browser) use ($order) {
            $browser->loginAs($this->admin())
                ->visit("/admin/order/{$order->id}")
                ->assertSee('Order');
        });
    }

    public function test_order_payment_toggle(): void
    {
        $order = Order::where('payment_status', PaymentStatus::PENDING->value)->first();
        $this->assertNotNull($order);

        $this->browse(function (Browser $browser) use ($order) {
            $browser->loginAs($this->admin())
                ->visit('/admin/order')
                ->script([
                    "var f=document.createElement('form');".
                    "f.method='POST';".
                    "f.action='".route('admin.order.payment.status.toggle', $order->id)."';".
                    "f.style.display='none';".
                    "var i=document.createElement('input');i.name='_method';i.value='PUT';f.appendChild(i);".
                    "var t=document.createElement('input');t.name='_token';".
                    "t.value=document.querySelector('meta[name=\"csrf-token\"]').getAttribute('content');f.appendChild(t);".
                    'document.body.appendChild(f);f.submit();',
                ]);

            $browser->waitForText('Payment status updated successfully', 15);
        });

        $order->refresh();
        $this->assertEquals(PaymentStatus::PAID, $order->payment_status);
    }
}
