@php
    $directory = app()->getLocale() == 'ar' ? 'rtl' : 'ltr';
    $generaleSetting = generaleSetting('setting');
    $address = $order->address;
    $user = $order->customer?->user;
    $otherDiscount = max(0, (float)($order->discount ?? 0) - (float)($order->coupon_discount ?? 0) - (float)($order->card_discount ?? 0));
    $payment = $order->payments()?->latest()->first();
    $paymentStatus = is_object($order->payment_status) ? $order->payment_status->value : (string)$order->payment_status;
    $paymentMethod = is_object($order->payment_method) ? $order->payment_method->value : (string)$order->payment_method;
@endphp
<!DOCTYPE html>
<html lang="en" dir="{{ $directory }}">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>{{ __('Tax Invoice') }} - #{{ $order->prefix . $order->order_code }}</title>
    <style>
        body {
            font-family: "DejaVu Sans", "Helvetica Neue", Helvetica, Arial, sans-serif;
            color: #1e293b;
            background-color: #ffffff;
            font-size: 12px;
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        p, h1, h2, h3, h4, h5, h6 {
            margin: 0;
            padding: 0;
        }

        .text-left { text-align: left; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }

        .text-muted { color: #64748b; }
        .text-dark { color: #0f172a; }
        .text-primary { color: #d97706; }
        .text-success { color: #059669; }
        .text-danger { color: #dc2626; }

        .fw-normal { font-weight: normal; }
        .fw-medium { font-weight: 500; }
        .fw-bold { font-weight: bold; }

        /* Header Bar */
        .header-table {
            margin-bottom: 20px;
            border-bottom: 2px solid #f1f5f9;
            padding-bottom: 16px;
        }

        .company-logo {
            max-height: 65px;
            max-width: 160px;
            object-fit: contain;
        }

        .invoice-badge {
            display: inline-block;
            background-color: #0f172a;
            color: #ffffff;
            font-size: 18px;
            font-weight: bold;
            letter-spacing: 1px;
            padding: 6px 16px;
            border-radius: 6px;
            margin-bottom: 8px;
        }

        .status-badge-paid {
            display: inline-block;
            background-color: #dcfce7;
            color: #15803d;
            font-size: 11px;
            font-weight: bold;
            padding: 3px 8px;
            border-radius: 4px;
            border: 1px solid #86efac;
        }

        .status-badge-pending {
            display: inline-block;
            background-color: #fef3c7;
            color: #b45309;
            font-size: 11px;
            font-weight: bold;
            padding: 3px 8px;
            border-radius: 4px;
            border: 1px solid #fcd34d;
        }

        /* Information Boxes */
        .info-card {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 12px 14px;
            vertical-align: top;
        }

        .info-card-title {
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #64748b;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 5px;
            margin-bottom: 6px;
        }

        /* Meta Table */
        .meta-table td {
            padding: 2px 0;
            font-size: 11px;
        }

        /* Items Table */
        .items-table {
            margin-top: 18px;
            margin-bottom: 16px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            overflow: hidden;
        }

        .items-table th {
            background-color: #0f172a;
            color: #ffffff;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 10px 10px;
        }

        .items-table td {
            padding: 9px 10px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 11px;
            color: #334155;
            vertical-align: middle;
        }

        .items-table tr:nth-child(even) td {
            background-color: #f8fafc;
        }

        .product-thumbnail {
            width: 32px;
            height: 32px;
            border-radius: 4px;
            border: 1px solid #e2e8f0;
            object-fit: cover;
            margin-right: 6px;
            vertical-align: middle;
        }

        .sku-tag {
            font-size: 9px;
            font-family: monospace;
            background-color: #e2e8f0;
            color: #475569;
            padding: 1px 4px;
            border-radius: 3px;
        }

        /* Summary Table */
        .summary-container {
            margin-top: 10px;
        }

        .summary-table {
            width: 100%;
            border-collapse: collapse;
        }

        .summary-table td {
            padding: 5px 8px;
            font-size: 11px;
        }

        .summary-total-row td {
            border-top: 2px solid #0f172a;
            border-bottom: 2px solid #0f172a;
            background-color: #f8fafc;
            padding: 8px;
            font-size: 14px;
            font-weight: bold;
            color: #0f172a;
        }

        /* QR & Verification Area */
        .qr-box {
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 10px;
            background-color: #f8fafc;
            text-align: center;
        }

        .qr-image {
            width: 75px;
            height: 75px;
        }

        /* Footer */
        .invoice-footer {
            margin-top: 30px;
            border-top: 1px solid #e2e8f0;
            padding-top: 14px;
            font-size: 10px;
            color: #64748b;
        }

        .signature-box {
            text-align: center;
            float: right;
            width: 180px;
            border-top: 1px solid #94a3b8;
            padding-top: 4px;
            font-size: 10px;
            font-weight: 500;
            color: #475569;
        }
    </style>
</head>
<body>

    <!-- Header Section -->
    <table class="header-table">
        <tr>
            <!-- Company Info Left -->
            <td style="width: 55%; vertical-align: top;">
                <table style="width: 100%;">
                    <tr>
                        <td style="vertical-align: middle;">
                            @if ($generaleSetting?->logo)
                                <img src="{{ $generaleSetting->logo }}" alt="Logo" class="company-logo" />
                            @else
                                <h1 style="font-size: 24px; color: #0f172a; font-weight: bold; margin-bottom: 2px;">
                                    {{ $generaleSetting?->name ?? 'Janmitram' }}
                                </h1>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td style="padding-top: 6px;">
                            <p class="fw-bold text-dark" style="font-size: 13px;">{{ $generaleSetting?->name ?? 'Janmitram Multipurpose Platform' }}</p>
                            <p class="text-muted" style="font-size: 10.5px; max-width: 320px;">
                                {{ $generaleSetting?->address ?? 'Corporate Headquarters, Rajasthan, India' }}
                            </p>
                            <p class="text-muted" style="font-size: 10.5px; margin-top: 2px;">
                                @if($generaleSetting?->email)<strong>Email:</strong> {{ $generaleSetting->email }} @endif
                                @if($generaleSetting?->mobile) &nbsp;|&nbsp; <strong>Phone:</strong> {{ $generaleSetting->mobile }} @endif
                            </p>
                        </td>
                    </tr>
                </table>
            </td>

            <!-- Invoice Title & Details Right -->
            <td style="width: 45%; vertical-align: top;" class="text-right">
                <div class="invoice-badge">{{ __('TAX INVOICE') }}</div>
                <table class="meta-table" style="width: 100%; margin-top: 4px;">
                    <tr>
                        <td class="text-right text-muted">{{ __('Invoice Number') }}:</td>
                        <td class="text-right fw-bold text-dark" style="padding-left: 8px;">#{{ $order->prefix . $order->order_code }}</td>
                    </tr>
                    <tr>
                        <td class="text-right text-muted">{{ __('Invoice Date') }}:</td>
                        <td class="text-right fw-medium text-dark" style="padding-left: 8px;">{{ now()->format('d M Y') }}</td>
                    </tr>
                    <tr>
                        <td class="text-right text-muted">{{ __('Order Date') }}:</td>
                        <td class="text-right fw-medium text-dark" style="padding-left: 8px;">{{ $order->created_at->format('d M Y, h:i A') }}</td>
                    </tr>
                    <tr>
                        <td class="text-right text-muted">{{ __('Payment Method') }}:</td>
                        <td class="text-right fw-medium text-dark" style="padding-left: 8px;">{{ $paymentMethod }}</td>
                    </tr>
                    <tr>
                        <td class="text-right text-muted">{{ __('Payment Status') }}:</td>
                        <td class="text-right" style="padding-left: 8px;">
                            @if(strtolower($paymentStatus) === 'paid')
                                <span class="status-badge-paid">{{ __('PAID') }}</span>
                            @else
                                <span class="status-badge-pending">{{ strtoupper($paymentStatus) }}</span>
                            @endif
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- Billing & Shipping Information Cards -->
    <table style="margin-bottom: 14px;">
        <tr>
            <!-- Bill To Customer -->
            <td style="width: 49%; vertical-align: top;">
                <div class="info-card">
                    <div class="info-card-title">{{ __('Billed / Delivered To') }}</div>
                    <p class="fw-bold text-dark" style="font-size: 12px; margin-bottom: 2px;">{{ $user?->name ?? 'Valued Customer' }}</p>
                    <p class="text-muted" style="font-size: 10.5px; line-height: 1.35; margin-bottom: 3px;">
                        @if ($address?->address_line) {{ $address->address_line }}, @endif
                        @if ($address?->address_line2) {{ $address->address_line2 }}, @endif
                        @if ($address?->area) {{ $address->area }}, @endif
                        @if ($address?->address_type) ({{ $address->address_type }}) @endif
                        @if ($address?->address) {{ $address->address }} @endif
                    </p>
                    <p style="font-size: 10.5px; margin-top: 3px;">
                        @if($user?->phone) <span class="text-muted">{{ __('Phone') }}:</span> <strong class="text-dark">{{ $user->phone }}</strong><br> @endif
                        @if($user?->email) <span class="text-muted">{{ __('Email') }}:</span> <span class="text-dark">{{ $user->email }}</span> @endif
                    </p>
                </div>
            </td>

            <td style="width: 2%;"></td>

            <!-- Merchant / Fulfilled By -->
            <td style="width: 49%; vertical-align: top;">
                <div class="info-card">
                    <div class="info-card-title">{{ __('Sold & Dispatched By') }}</div>
                    <p class="fw-bold text-dark" style="font-size: 12px; margin-bottom: 2px;">{{ $order->shop?->name ?? $generaleSetting?->name ?? 'Janmitram Verified Partner' }}</p>
                    <p class="text-muted" style="font-size: 10.5px; line-height: 1.35; margin-bottom: 3px;">
                        {{ $order->shop?->address ?? $generaleSetting?->address ?? 'Rajasthan Central Hub, India' }}
                    </p>
                    <p style="font-size: 10.5px; margin-top: 3px;">
                        @if($order->shop?->phone) <span class="text-muted">{{ __('Support') }}:</span> <strong class="text-dark">{{ $order->shop->phone }}</strong><br> @endif
                        <span class="text-muted">{{ __('Fulfillment Status') }}:</span> <strong class="text-dark">{{ $order->order_status->value ?? $order->order_status }}</strong>
                    </p>
                </div>
            </td>
        </tr>
    </table>

    <!-- Line Items Table -->
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 5%; text-align: center;">#</th>
                <th style="width: 45%; text-align: left;">{{ __('Product Description') }}</th>
                <th style="width: 14%; text-align: right;">{{ __('Unit Price') }}</th>
                <th style="width: 8%; text-align: center;">{{ __('Qty') }}</th>
                <th style="width: 10%; text-align: center;">{{ __('Unit / Size') }}</th>
                <th style="width: 18%; text-align: right;">{{ __('Net Amount') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($order->products ?? [] as $product)
                @php
                    $price = $product->discount_price > 0 ? $product->discount_price : $product->price;
                    $qty = $product->pivot->quantity ?? 1;
                    $rowTotal = $price * $qty;
                    $unitStr = $product->pivot->unit ?? $product->unit?->name ?? '';
                    $sizeStr = $product->pivot->size ?? '';
                    $spec = array_filter([$unitStr, $sizeStr ? "Size: $sizeStr" : null]);
                @endphp
                <tr>
                    <td class="text-center text-muted">{{ $loop->iteration }}</td>
                    <td>
                        <strong class="text-dark" style="font-size: 11.5px;">{{ $product->name }}</strong>
                        @if (!empty($product->pivot->sku))
                            <span class="sku-tag">#{{ $product->pivot->sku }}</span>
                        @endif
                        @if (!empty($product->short_description))
                            <div class="text-muted" style="font-size: 9.5px; margin-top: 2px;">{{ Str::limit(strip_tags($product->short_description), 80) }}</div>
                        @endif
                    </td>
                    <td class="text-right fw-medium">{{ showCurrency($price) }}</td>
                    <td class="text-center fw-bold">{{ $qty }}</td>
                    <td class="text-center text-muted">{{ !empty($spec) ? implode(' / ', $spec) : '1 Item' }}</td>
                    <td class="text-right fw-bold text-dark">{{ showCurrency($rowTotal) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center text-muted" style="padding: 20px;">{{ __('No products found in this order.') }}</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Financial Breakdown & QR Code Section -->
    <table class="summary-container">
        <tr>
            <!-- Left Side: QR Code & Payment Verification Note -->
            <td style="width: 45%; vertical-align: top;">
                <div class="qr-box">
                    <table style="width: 100%;">
                        <tr>
                            <td style="width: 85px; vertical-align: middle; text-align: center;">
                                @if(!empty($qrCodeImage))
                                    <img src="{{ $qrCodeImage }}" alt="Order QR" class="qr-image" />
                                @endif
                            </td>
                            <td style="vertical-align: middle; text-align: left; padding-left: 10px;">
                                <p class="fw-bold text-dark" style="font-size: 11.5px;">{{ __('Digitally Verified') }}</p>
                                <p class="text-muted" style="font-size: 9.5px; margin-top: 2px;">
                                    Scan QR code to verify invoice authenticity & order status.
                                </p>
                                @if($payment?->payment_token)
                                    <p class="text-muted" style="font-size: 9px; font-family: monospace; margin-top: 4px;">
                                        Ref: {{ Str::limit($payment->payment_token, 20) }}
                                    </p>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </td>

            <td style="width: 5%;"></td>

            <!-- Right Side: Financial Calculation Summary -->
            <td style="width: 50%; vertical-align: top;">
                <table class="summary-table">
                    <tr>
                        <td class="text-muted">{{ __('Items Subtotal') }}:</td>
                        <td class="text-right fw-bold text-dark">{{ showCurrency($order->total_amount) }}</td>
                    </tr>

                    @if ($order->coupon_discount > 0)
                        <tr>
                            <td class="text-danger">{{ __('Coupon Discount') }} {{ $order->coupon ? '(' . $order->coupon->code . ')' : '' }}:</td>
                            <td class="text-right fw-bold text-danger">-{{ showCurrency($order->coupon_discount) }}</td>
                        </tr>
                    @endif

                    @if ($order->card_discount > 0)
                        <tr>
                            <td class="text-danger">{{ __('Card Discount') }} {{ $order->card ? '(' . $order->card->card_number . ')' : '' }}:</td>
                            <td class="text-right fw-bold text-danger">-{{ showCurrency($order->card_discount) }}</td>
                        </tr>
                    @endif

                    @if ($otherDiscount > 0)
                        <tr>
                            <td class="text-danger">{{ __('Special Discount') }}:</td>
                            <td class="text-right fw-bold text-danger">-{{ showCurrency($otherDiscount) }}</td>
                        </tr>
                    @endif

                    <tr>
                        <td class="text-muted">{{ __('Delivery / Shipping Charge') }}:</td>
                        <td class="text-right fw-medium text-dark">{{ showCurrency($order->delivery_charge) }}</td>
                    </tr>

                    @foreach ($order->vatTaxes ?? [] as $vatTax)
                        <tr>
                            <td class="text-muted">{{ $vatTax->name }} ({{ $vatTax->percentage }}%):</td>
                            <td class="text-right fw-medium text-dark">{{ showCurrency($vatTax->amount) }}</td>
                        </tr>
                    @endforeach

                    @if ($order->tax_amount > 0 && count($order->vatTaxes ?? []) <= 0)
                        <tr>
                            <td class="text-muted">{{ __('GST / Taxes') }}:</td>
                            <td class="text-right fw-medium text-dark">{{ showCurrency($order->tax_amount) }}</td>
                        </tr>
                    @endif

                    <tr class="summary-total-row">
                        <td style="border-radius: 6px 0 0 6px;">{{ __('Grand Total Payable') }}:</td>
                        <td class="text-right text-primary" style="border-radius: 0 6px 6px 0; font-size: 15px;">
                            {{ showCurrency($order->payable_amount) }}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- Footer Terms & Authorized Signature -->
    <table class="invoice-footer">
        <tr>
            <td style="width: 65%; vertical-align: bottom;">
                <p class="fw-bold text-dark" style="font-size: 10.5px; margin-bottom: 2px;">{{ __('Terms & Conditions') }}:</p>
                <p class="text-muted" style="font-size: 9.5px; line-height: 1.4;">
                    1. Goods once sold can be returned according to Janmitram Return & Refund Policy.<br>
                    2. This is a computer-generated tax invoice and does not require a physical signature.<br>
                    3. For questions or support, contact <strong>{{ $generaleSetting?->email ?? 'support@janmitram.com' }}</strong>
                </p>
            </td>
            <td style="width: 35%; vertical-align: bottom;" class="text-right">
                <div class="signature-box">
                    <p class="fw-bold text-dark">{{ $generaleSetting?->name ?? 'Janmitram' }}</p>
                    <p class="text-muted" style="font-size: 9px;">{{ __('Authorized Signatory') }}</p>
                </div>
            </td>
        </tr>
    </table>

</body>
</html>
