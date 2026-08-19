<?php

namespace App\Http\Controllers\Gateway\Razorpay;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Support\Str;
use Razorpay\Api\Api;

class ProcessController extends Controller
{
    /**
     * Process to Razorpay
     *
     * @return string
     */
    public static function process($paymentGateway, Payment $payment, ?array $info = null)
    {
        $config = json_decode($paymentGateway->config);

        $razorpay = new Api($config->key, $config->secret);

        $amount = (float) $payment->amount;
        $currency = 'INR';
        $receipt = 'payment_receipt_'.$payment->id;

        $paymentToken = Str::uuid()->toString();

        $payment->update([
            'payment_token' => $paymentToken,
        ]);

        $successUrl = $cancelUrl = null;

        if ($info) {
            $email = $info['email'] ?? 'customer@janmitram.com';
            $phone = $info['phone'] ?? '';
            $name = $info['name'] ?? 'Janmitram Customer';
            $description = $info['description'] ?? 'Janmitram Order Payment';

            $successUrl = route('payment.success', $payment->id);
            $cancelUrl = route('payment.cancel', $payment->id);
        } else {
            $user = $payment->orders[0]->customer?->user;
            $name = $user?->name ?? 'Janmitram Customer';
            $email = $user?->email ?? 'customer@janmitram.com';
            $phone = $user?->phone ?? '';
            $description = 'Order payment of '.$payment->amount.' INR (Total Orders: '.$payment->orders->count().')';
            $successUrl = route('payment.success', $payment->id);
            $cancelUrl = route('payment.cancel', $payment->id);
        }

        // Sanitize phone number (digits only, minimum 10 digits for Indian standard)
        $cleanPhone = preg_replace('/[^0-9]/', '', (string) $phone);
        $customer = [
            'name' => $name,
            'email' => $email,
        ];
        if (strlen($cleanPhone) >= 10 && ! preg_match('/^(\d)\1+$/', $cleanPhone)) {
            $customer['contact'] = $cleanPhone;
        }

        try {
            $paymentLink = $razorpay->invoice->create([
                'type' => 'link',
                'amount' => (int) round($amount * 100), // amount in paisa
                'currency' => $currency,
                'description' => $description,
                'customer' => $customer,
                'callback_url' => $successUrl,
                'redirect' => true,
                'callback_method' => 'get',
                'cancel_url' => $cancelUrl,
            ]);

            return $paymentLink['short_url'];

        } catch (\Throwable $th) {
            return json_encode(['error' => $th->getMessage()]);
        }
    }
}
