@php
    $directory = app()->getLocale() == 'ar' ? 'rtl' : 'ltr';
    $generaleSetting = generaleSetting('setting');
    $address = $order->address;
    $user = $order->customer?->user;
    $payment = $order->payments()?->latest()->first();
    $transactionId = $payment?->payment_token ?? ('TXN-' . str_pad($order->id, 8, '0', STR_PAD_LEFT));
    $paymentStatus = is_object($order->payment_status) ? $order->payment_status->value : (string)$order->payment_status;
    $paymentMethod = is_object($order->payment_method) ? $order->payment_method->value : (string)$order->payment_method;
    $otherDiscount = max(0, (float)($order->discount ?? 0) - (float)($order->coupon_discount ?? 0) - (float)($order->card_discount ?? 0));
    $isPaid = strtolower($paymentStatus) === 'paid';
@endphp
<!DOCTYPE html>
<html lang="en" dir="{{ $directory }}">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>{{ __('Payment Receipt') }} - #{{ $order->prefix . $order->order_code }}</title>
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
        .text-emerald { color: #059669; }
        .text-danger { color: #dc2626; }

        .fw-normal { font-weight: normal; }
        .fw-medium { font-weight: 500; }
        .fw-bold { font-weight: bold; }

        /* Header */
        .header-table {
            margin-bottom: 18px;
            border-bottom: 2px solid #f1f5f9;
            padding-bottom: 14px;
        }

        .company-logo {
            max-height: 60px;
            max-width: 150px;
            object-fit: contain;
        }

        .receipt-badge {
            display: inline-block;
            background-color: #047857;
            color: #ffffff;
            font-size: 16px;
            font-weight: bold;
            letter-spacing: 0.8px;
            padding: 6px 16px;
            border-radius: 6px;
            margin-bottom: 6px;
        }

        /* Payment Hero Card */
        .hero-payment-card {
            background-color: #f0fdf4;
            border: 1.5px solid #86efac;
            border-radius: 10px;
            padding: 14px 18px;
            margin-bottom: 18px;
        }

        .hero-payment-card.pending {
            background-color: #fffbeb;
            border-color: #fcd34d;
        }

        .hero-amount {
            font-size: 24px;
            font-weight: bold;
            color: #047857;
            margin-top: 2px;
        }

        .hero-amount.pending {
            color: #b45309;
        }

        .status-pill {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: bold;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .status-pill-paid {
            background-color: #059669;
            color: #ffffff;
        }

        .status-pill-pending {
            background-color: #d97706;
            color: #ffffff;
        }

        /* Info Card */
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

        /* Items Table */
        .items-table {
            margin-top: 16px;
            margin-bottom: 14px;
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
            padding: 9px 10px;
        }

        .items-table td {
            padding: 8px 10px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 11px;
            color: #334155;
            vertical-align: middle;
        }

        .items-table tr:nth-child(even) td {
            background-color: #f8fafc;
        }

        /* Summary Table */
        .summary-table {
            width: 100%;
            border-collapse: collapse;
        }

        .summary-table td {
            padding: 5px 8px;
            font-size: 11px;
        }

        .summary-total-row td {
            border-top: 2px solid #047857;
            border-bottom: 2px solid #047857;
            background-color: #f0fdf4;
            padding: 8px;
            font-size: 13px;
            font-weight: bold;
            color: #047857;
        }

        /* Footer */
        .receipt-footer {
            margin-top: 26px;
            border-top: 1px solid #e2e8f0;
            padding-top: 14px;
            font-size: 10px;
            color: #64748b;
        }

        .seal-box {
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
            <!-- Company Details Left -->
            <td style="width: 55%; vertical-align: top;">
                <table style="width: 100%;">
                    <tr>
                        <td style="vertical-align: middle;">
                            @if ($generaleSetting?->logo)
                                <img src="{{ $generaleSetting->logo }}" alt="Logo" class="company-logo" />
                            @else
                                <h1 style="font-size: 22px; color: #0f172a; font-weight: bold; margin-bottom: 2px;">
                                    {{ $generaleSetting?->name ?? 'Janmitram' }}
                                </h1>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td style="padding-top: 6px;">
                            <p class="fw-bold text-dark" style="font-size: 12.5px;">{{ $generaleSetting?->name ?? 'Janmitram Multipurpose Platform' }}</p>
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

            <!-- Receipt Meta Right -->
            <td style="width: 45%; vertical-align: top;" class="text-right">
                <div class="receipt-badge">{{ __('PAYMENT RECEIPT') }}</div>
                <table style="width: 100%; margin-top: 4px;">
                    <tr>
                        <td class="text-right text-muted" style="font-size: 11px;">{{ __('Receipt Reference') }}:</td>
                        <td class="text-right fw-bold text-dark" style="font-size: 11px; padding-left: 8px;">#REC-{{ $order->prefix . $order->order_code }}</td>
                    </tr>
                    <tr>
                        <td class="text-right text-muted" style="font-size: 11px;">{{ __('Receipt Date') }}:</td>
                        <td class="text-right fw-medium text-dark" style="font-size: 11px; padding-left: 8px;">{{ now()->format('d M Y, h:i A') }}</td>
                    </tr>
                    <tr>
                        <td class="text-right text-muted" style="font-size: 11px;">{{ __('Order Code') }}:</td>
                        <td class="text-right fw-bold text-dark" style="font-size: 11px; padding-left: 8px;">#{{ $order->prefix . $order->order_code }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- Verified Payment Confirmation Card -->
    <div class="hero-payment-card {{ $isPaid ? '' : 'pending' }}">
        <table style="width: 100%;">
            <tr>
                <td style="width: 50%; vertical-align: middle;">
                    <span class="text-muted" style="font-size: 11px; text-transform: uppercase; font-weight: bold; letter-spacing: 0.5px;">
                        {{ $isPaid ? __('Total Amount Paid') : __('Total Payable Amount') }}
                    </span>
                    <div class="hero-amount {{ $isPaid ? '' : 'pending' }}">
                        {{ showCurrency($order->payable_amount) }}
                    </div>
                </td>
                <td style="width: 50%; vertical-align: middle; text-align: right;">
                    <span class="status-pill {{ $isPaid ? 'status-pill-paid' : 'status-pill-pending' }}">
                        {{ $isPaid ? '✓ PAYMENT COMPLETED' : '⏳ PAYMENT PENDING' }}
                    </span>
                    <div style="font-size: 11px; color: #475569; margin-top: 5px;">
                        <strong>Method:</strong> {{ $paymentMethod }}<br>
                        <strong>Txn Ref:</strong> <span style="font-family: monospace;">{{ $transactionId }}</span>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Payer and Order Context Cards -->
    <table style="margin-bottom: 14px;">
        <tr>
            <!-- Customer / Payer Info -->
            <td style="width: 49%; vertical-align: top;">
                <div class="info-card">
                    <div class="info-card-title">{{ __('Payer Details') }}</div>
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

            <!-- Order Fulfillment & Shop Details -->
            <td style="width: 49%; vertical-align: top;">
                <div class="info-card">
                    <div class="info-card-title">{{ __('Order Summary') }}</div>
                    <p class="fw-bold text-dark" style="font-size: 12px; margin-bottom: 2px;">{{ $order->shop?->name ?? $generaleSetting?->name ?? 'Janmitram Partner Shop' }}</p>
                    <p class="text-muted" style="font-size: 10.5px; line-height: 1.35; margin-bottom: 3px;">
                        {{ $order->shop?->address ?? $generaleSetting?->address ?? 'Central Hub, India' }}
                    </p>
                    <p style="font-size: 10.5px; margin-top: 3px;">
                        <span class="text-muted">{{ __('Order Placed') }}:</span> <strong class="text-dark">{{ $order->created_at->format('d M Y, h:i A') }}</strong><br>
                        <span class="text-muted">{{ __('Delivery Status') }}:</span> <strong class="text-dark">{{ $order->order_status->value ?? $order->order_status }}</strong>
                    </p>
                </div>
            </td>
        </tr>
    </table>

    <!-- Line Items Table -->
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 6%; text-align: center;">#</th>
                <th style="width: 50%; text-align: left;">{{ __('Item Purchased') }}</th>
                <th style="width: 14%; text-align: right;">{{ __('Rate') }}</th>
                <th style="width: 10%; text-align: center;">{{ __('Qty') }}</th>
                <th style="width: 20%; text-align: right;">{{ __('Amount') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($order->products ?? [] as $product)
                @php
                    $price = $product->discount_price > 0 ? $product->discount_price : $product->price;
                    $qty = $product->pivot->quantity ?? 1;
                    $rowTotal = $price * $qty;
                @endphp
                <tr>
                    <td class="text-center text-muted">{{ $loop->iteration }}</td>
                    <td>
                        <strong class="text-dark">{{ $product->name }}</strong>
                        @if (!empty($product->pivot->sku))
                            <span style="font-size: 9px; color: #64748b;">(SKU: {{ $product->pivot->sku }})</span>
                        @endif
                    </td>
                    <td class="text-right">{{ showCurrency($price) }}</td>
                    <td class="text-center fw-bold">{{ $qty }}</td>
                    <td class="text-right fw-bold text-dark">{{ showCurrency($rowTotal) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center text-muted" style="padding: 16px;">{{ __('No items found.') }}</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Financial Breakdown Table -->
    <table style="width: 100%; margin-top: 8px;">
        <tr>
            <td style="width: 50%; vertical-align: top;">
                <div style="background-color: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 8px; padding: 12px;">
                    <p class="fw-bold text-dark" style="font-size: 11px; margin-bottom: 2px;">{{ __('Payment Verification Note') }}</p>
                    <p class="text-muted" style="font-size: 10px; line-height: 1.4;">
                        This receipt confirms that the payment for order <strong>#{{ $order->prefix . $order->order_code }}</strong> has been recorded in Janmitram's financial ledger under reference <strong>{{ $transactionId }}</strong>.
                    </p>
                </div>
            </td>

            <td style="width: 5%;"></td>

            <td style="width: 45%; vertical-align: top;">
                <table class="summary-table">
                    <tr>
                        <td class="text-muted">{{ __('Items Subtotal') }}:</td>
                        <td class="text-right fw-bold text-dark">{{ showCurrency($order->total_amount) }}</td>
                    </tr>

                    @if ($order->coupon_discount > 0)
                        <tr>
                            <td class="text-danger">{{ __('Coupon Discount') }}:</td>
                            <td class="text-right fw-bold text-danger">-{{ showCurrency($order->coupon_discount) }}</td>
                        </tr>
                    @endif

                    @if ($order->card_discount > 0)
                        <tr>
                            <td class="text-danger">{{ __('Card Discount') }}:</td>
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
                        <td class="text-muted">{{ __('Delivery Fee') }}:</td>
                        <td class="text-right text-dark">{{ showCurrency($order->delivery_charge) }}</td>
                    </tr>

                    @if ($order->tax_amount > 0)
                        <tr>
                            <td class="text-muted">{{ __('Applicable Taxes') }}:</td>
                            <td class="text-right text-dark">{{ showCurrency($order->tax_amount) }}</td>
                        </tr>
                    @endif

                    <tr class="summary-total-row">
                        <td style="border-radius: 6px 0 0 6px;">{{ __('Total Settled Amount') }}:</td>
                        <td class="text-right" style="border-radius: 0 6px 6px 0;">
                            {{ showCurrency($order->payable_amount) }}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- Footer -->
    <table class="receipt-footer">
        <tr>
            <td style="width: 65%; vertical-align: bottom;">
                <p class="fw-bold text-dark" style="font-size: 10px; margin-bottom: 2px;">{{ __('Thank you for your business!') }}</p>
                <p class="text-muted" style="font-size: 9.5px; line-height: 1.35;">
                    This is an electronically generated receipt for transaction records.<br>
                    Need assistance? Contact support at <strong>{{ $generaleSetting?->email ?? 'support@janmitram.com' }}</strong> or call <strong>{{ $generaleSetting?->mobile ?? '+91 9999999999' }}</strong>.
                </p>
            </td>
            <td style="width: 35%; vertical-align: bottom;" class="text-right">
                <div class="seal-box">
                    <p class="fw-bold text-dark">{{ $generaleSetting?->name ?? 'Janmitram' }}</p>
                    <p class="text-muted" style="font-size: 9px;">{{ __('Accounts & Billing Department') }}</p>
                </div>
            </td>
        </tr>
    </table>

</body>
</html>
