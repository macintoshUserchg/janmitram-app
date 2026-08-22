<?php

namespace App\Http\Resources;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderDetailsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $paymentMethod = $this->payment_method->value;
        if ($this->payment_status->value == PaymentStatus::PENDING->value && $paymentMethod != PaymentMethod::CASH->value) {
            $paymentMethod = PaymentMethod::ONLINE->value;
        }

        $estimateDelivery = $this->shop?->estimated_delivery_time ?? '2-3 days';

        $is_returned = false;

        if ($this->created_at && $this->order_status->value === 'Delivered') {
            $hasDigital = $this->products->contains(fn ($product) => $product->is_digital);

            if ($hasDigital) {
                $is_returned = false;
            } else {
                $is_returned = $this->created_at->copy()
                    ->addDays($generaleSetting?->return_order_within_days ?? 3)
                    ->isFuture();
            }

        }

        $blockedStatuses = ['Delivered', 'Cancelled'];

        $canShowRiderLocation = $this->driverOrder?->driver_id && ! in_array($this->order_status->value, $blockedStatuses);
        $riderLocation = [];
        if ($canShowRiderLocation) {
            $riderLocation = $this->driverOrder ? OrderRiderResource::make($this->driverOrder) : [];
        }

        $taxAmount = (float) ($this->tax_amount ?? 0);
        $totalAmount = (float) ($this->total_amount ?? 0);
        $cardDiscount = (float) ($this->card_discount ?? 0);
        $couponDiscount = (float) ($this->coupon_discount ?? 0);
        $totalDiscount = $cardDiscount + $couponDiscount + (float) ($this->discount ?? 0);
        $discountedItems = max(0, $totalAmount - $totalDiscount);
        $discountFactor = $totalAmount > 0 ? ($discountedItems / $totalAmount) : 1.0;

        $grossTax = $discountFactor > 0 ? ($taxAmount / $discountFactor) : $taxAmount;
        $preTaxable = max(0, $totalAmount - $grossTax);
        $netTaxable = max(0, $discountedItems - $taxAmount);
        $baseDiscount = max(0, $preTaxable - $netTaxable);
        $taxSavings = max(0, $grossTax - $taxAmount);

        return [
            'id' => $this->id,
            'order_code' => (string) '#'.$this->prefix.''.$this->order_code,
            'order_status' => $this->order_status->value,
            'created_at' => $this->created_at,
            'placed_at' => $this->created_at->format('d M, Y h:i A'),
            'estimated_delivery_date' => (string) $estimateDelivery,
            'payment_method' => $paymentMethod,
            'payment_status' => $this->payment_status->value,
            'total_amount' => (float) number_format($this->total_amount, 2, '.', ''),
            'taxable_base' => (float) number_format($preTaxable, 2, '.', ''),
            'base_discount' => (float) number_format($baseDiscount, 2, '.', ''),
            'tax_savings' => (float) number_format($taxSavings, 2, '.', ''),
            'net_taxable_base' => (float) number_format($netTaxable, 2, '.', ''),
            'tax_amount' => (float) number_format($this->tax_amount, 2, '.', ''),
            'discount' => (float) number_format($this->discount, 2, '.', ''),
            'coupon_discount' => (float) number_format($this->coupon_discount, 2, '.', ''),
            'card_discount' => (float) number_format($cardDiscount, 2, '.', ''),
            'payable_amount' => (float) number_format($this->payable_amount, 2, '.', ''),
            'quantity' => (int) $this->products->sum('pivot.quantity'),
            'delivery_charge' => (float) number_format(($this->delivery_charge ?? 0), 2, '.', ''),
            'shop' => ShopResource::make($this->shop),
            'products' => OrderProductResource::collection($this->products),
            'invoice_url' => route('shop.download-invoice', $this->id),
            'payment_receipt_url' => route('shop.payment-slip', $this->id),
            'address' => AddressResource::make($this->address),
            'all_vat_taxes' => $this->vatTaxes,
            'return_order_within_days' => $generaleSetting?->return_order_within_days ?? 3,
            'last_return_date' => $this->created_at
                ? $this->created_at->copy()->addDays($generaleSetting?->return_order_within_days ?? 3)->format('d M, Y h:i A')
                : null,
            'is_returnable' => $is_returned,
            'rider' => $riderLocation,
        ];
    }
}
