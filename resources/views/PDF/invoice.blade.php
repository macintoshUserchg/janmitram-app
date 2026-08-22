@php
    $directory = app()->getLocale() == 'ar' ? 'rtl' : 'ltr';
    $generaleSetting = generaleSetting('setting');
    $address = $order->address;
    $user = $order->customer?->user;
    $otherDiscount = max(0, (float)($order->discount ?? 0) - (float)($order->coupon_discount ?? 0) - (float)($order->card_discount ?? 0));
    $payment = $order->payments()?->latest()->first();
    $paymentStatus = is_object($order->payment_status) ? $order->payment_status->value : (string)$order->payment_status;
    $paymentMethod = is_object($order->payment_method) ? $order->payment_method->value : (string)$order->payment_method;

    // Company & Branch Information
    $companyName = 'JANMITRA UDYOG LLP S-15 Dwarika Tower Vidyadhar Nagar Jaipur Rajasthan (Unit Badharna Road Harmada Sabji Mandi Ke Pas Sikar Road)';
    $companyHindi = '"जनमित्रम " शिव मंदिर के सामने बढ़ारना रोड सब्जी मंडी के पास सीकर रोड जयपुर';
    $companyPhone = '9414057690';
    $companyEmail = 'janmitraudyog@gmail.com';
    $companyGstin = '08AAQFJ8465L1ZF';
    $companyState = '08-Rajasthan';

    // Bank Details
    $bankName = 'Indian Overseas Bank, Vidhya Dhar Nagar Jaipur';
    $bankAccountNo = '242702000000224';
    $bankIfsc = 'IOBA0002427';
    $bankHolder = 'JANMITRA UDYOG LLP';

    // Client / Customer Information
    $clientName = $user?->name ?? 'CASH CUSTOMER';
    $clientAddressParts = array_filter([
        $address?->address_line,
        $address?->address_line2,
        $address?->area,
        $address?->address,
        $address?->city ? $address->city : null,
        $address?->state ? $address->state : null,
        $address?->pincode ? $address->pincode : ($address?->zip_code ?? null)
    ]);
    $clientAddress = !empty($clientAddressParts) ? implode(', ', $clientAddressParts) : ($order->shop?->address ?? 'Jaipur, Rajasthan');
    $placeOfSupply = $address?->state ? $address->state : $companyState;

    // Line Items Computation
    $items = [];
    $totalQty = 0;
    $totalGst = 0;
    $totalTaxable = 0;
    $totalGross = 0;

    // Determine overall tax rate from vatTaxes or default 5%
    $defaultTaxRate = 5.0;
    if ($order->vatTaxes && $order->vatTaxes->count() > 0) {
        $defaultTaxRate = (float)$order->vatTaxes->first()->percentage;
    }

    foreach ($order->products ?? [] as $idx => $product) {
        $mrpUnit = (float)($product->pivot->price > 0
            ? $product->pivot->price
            : ($product->discount_price > 0 ? $product->discount_price : $product->price));
        $qty = (int)($product->pivot->quantity ?? 1);
        $unitStr = $product->pivot->unit ?? $product->unit?->name ?? '-';
        $sizeStr = $product->pivot->size ?? '';
        $hsn = $product->hsn_code ?? $product->sku ?? ($product->pivot->sku ?? '0405');

        $taxRate = $defaultTaxRate;
        if ($product->vatTaxes && $product->vatTaxes->count() > 0) {
            $taxRate = (float)$product->vatTaxes->first()->percentage;
        }

        $rowGross = $mrpUnit * $qty;
        $taxableRow = round($rowGross / (1 + ($taxRate / 100)), 2);
        $taxAmount = round($rowGross - $taxableRow, 2);
        $taxableUnit = round($taxableRow / $qty, 2);

        $totalQty += $qty;
        $totalGst += $taxAmount;
        $totalTaxable += $taxableRow;
        $totalGross += $rowGross;

        $items[] = [
            'index' => $idx + 1,
            'name' => $product->name . ($sizeStr ? " ($sizeStr)" : ''),
            'hsn' => $hsn,
            'qty' => $qty,
            'unit' => $unitStr,
            'price_unit' => $taxableUnit,
            'mrp_unit' => $mrpUnit,
            'tax_rate' => $taxRate,
            'tax_amount' => $taxAmount,
            'taxable_amount' => $taxableRow,
            'amount' => $rowGross,
        ];
    }

    $couponDiscount = (float)($order->coupon_discount ?? 0);
    $cardDiscount = (float)($order->card_discount ?? 0);
    $otherDiscount = max(0, (float)($order->discount ?? 0) - $couponDiscount - $cardDiscount);
    $orderLevelDiscount = $couponDiscount + $cardDiscount + $otherDiscount;

    $grossTotal = (float)($order->total_amount > 0 ? $order->total_amount : $totalGross);
    $discountedSubtotal = max(0, $grossTotal - $orderLevelDiscount);
    $discountFactor = $grossTotal > 0 ? ($discountedSubtotal / $grossTotal) : 1.0;

    $taxAmountTotal = (float)($order->tax_amount > 0 ? $order->tax_amount : round($totalGst * $discountFactor, 2));
    $grossTax = $discountFactor > 0 ? round($taxAmountTotal / $discountFactor, 2) : $taxAmountTotal;
    $preTaxable = max(0, $grossTotal - $grossTax);
    $netTaxable = max(0, $discountedSubtotal - $taxAmountTotal);
    $baseDiscount = max(0, $preTaxable - $netTaxable);
    $taxSavings = max(0, $grossTax - $taxAmountTotal);

    $deliveryCharge = (float)($order->delivery_charge ?? 0);
    $payableTotal = (float)($order->payable_amount > 0 ? $order->payable_amount : ($discountedSubtotal + $deliveryCharge));

    $amountInWords = numberToIndianWords($payableTotal);
@endphp
<!DOCTYPE html>
<html lang="en" dir="{{ $directory }}">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>{{ __('Tax Invoice') }} - #{{ $order->prefix . $order->order_code }}</title>
    <style>
        @page {
            margin: 8mm;
        }

        body {
            font-family: "DejaVu Sans", "Helvetica Neue", Helvetica, Arial, sans-serif;
            color: #0f172a;
            background-color: #ffffff;
            font-size: 11px;
            line-height: 1.35;
            margin: 0;
            padding: 0;
        }

        .invoice-box {
            width: 100%;
            border: 1.5px solid #334155;
            background-color: #ffffff;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 5px 6px;
            vertical-align: middle;
        }

        .border-bottom { border-bottom: 1px solid #334155; }
        .border-top { border-top: 1px solid #334155; }
        .border-left { border-left: 1px solid #334155; }
        .border-right { border-right: 1px solid #334155; }

        .bg-light-header {
            background-color: #f8fafc;
            font-weight: bold;
            color: #1e293b;
        }

        .text-left { text-align: left; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }

        .fw-bold { font-weight: bold; }
        .fw-normal { font-weight: normal; }

        .company-logo {
            width: 110px;
            height: 110px;
            object-fit: contain;
            display: block;
            margin: 0 auto;
        }

        /* Items Table */
        .items-table {
            width: 100%;
            table-layout: fixed;
            border-top: 1px solid #334155;
            border-bottom: 1px solid #334155;
        }

        .items-table thead {
            display: table-header-group;
        }

        .items-table tr {
            page-break-inside: avoid;
        }

        .items-table th {
            background-color: #ffffff;
            border: 1px solid #334155;
            font-size: 10.5px;
            font-weight: bold;
            padding: 5px 3px;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        .items-table td {
            border: 1px solid #334155;
            font-size: 10px;
            padding: 4px 4px;
            word-wrap: break-word;
            overflow-wrap: break-word;
            line-height: 1.25;
        }

        .avoid-break {
            page-break-inside: avoid;
        }

        .signature-img {
            max-height: 42px;
            margin-top: 4px;
            margin-bottom: 2px;
        }
    </style>
</head>
<body>

<div class="invoice-box">

    <!-- 1. Top Header (Company Details & Logo) -->
    <table class="avoid-break">
        <tr>
            <!-- Left Logo Box -->
            <td style="width: 130px; text-align: center; vertical-align: middle; border-right: 1px solid #334155; padding: 8px;">
                @if ($generaleSetting?->logo)
                    <img src="{{ $generaleSetting->logo }}" alt="Logo" class="company-logo" />
                @else
                    <img src="{{ public_path('assets/logo.png') }}" alt="Logo" class="company-logo" />
                @endif
            </td>

            <!-- Right Company Text Info -->
            <td style="vertical-align: top; padding: 8px 12px;">
                <h1 style="font-size: 15px; font-weight: bold; margin: 0 0 3px 0; color: #0f172a; line-height: 1.25;">
                    {{ $companyName }}
                </h1>
                <div style="font-size: 10.5px; color: #334155; margin-bottom: 6px; font-weight: 500;">
                    {{ $companyHindi }}
                </div>
                <table style="width: 100%; font-size: 10.5px;">
                    <tr>
                        <td style="padding: 1px 0; width: 48%;"><strong>Phone:</strong> {{ $companyPhone }}</td>
                        <td style="padding: 1px 0; width: 52%;"><strong>Email:</strong> {{ $companyEmail }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 1px 0;"><strong>GSTIN:</strong> {{ $companyGstin }}</td>
                        <td style="padding: 1px 0;"><strong>State:</strong> {{ $companyState }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- 2. Invoice For & Details (2 Columns) -->
    <table class="border-top avoid-break">
        <tr class="bg-light-header border-bottom">
            <td style="width: 50%; border-right: 1px solid #334155; font-size: 11px; padding: 4px 8px;">
                <strong>Invoice For:</strong>
            </td>
            <td style="width: 50%; font-size: 11px; padding: 4px 8px;">
                <strong>Invoice Details:</strong>
            </td>
        </tr>
        <tr>
            <!-- Customer Bill To Info -->
            <td style="width: 50%; border-right: 1px solid #334155; vertical-align: top; padding: 8px;">
                <div style="font-size: 12px; font-weight: bold; text-transform: uppercase; margin-bottom: 3px; color: #0f172a;">
                    {{ $clientName }}
                </div>
                <div style="font-size: 10.5px; color: #334155; line-height: 1.35;">
                    {{ $clientAddress }}
                </div>
                @if($user?->phone)
                    <div style="font-size: 10px; color: #475569; margin-top: 3px;">
                        <strong>Phone:</strong> {{ $user->phone }}
                    </div>
                @endif
            </td>

            <!-- Invoice Reference Info -->
            <td style="width: 50%; vertical-align: top; padding: 8px;">
                <table style="width: 100%; font-size: 11px;">
                    <tr>
                        <td style="width: 32%; padding: 2px 0; color: #475569;">No:</td>
                        <td style="width: 68%; padding: 2px 0; font-weight: bold; color: #0f172a;">
                            {{ $order->prefix ? $order->prefix . $order->order_code : $order->id }}
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 2px 0; color: #475569;">Date:</td>
                        <td style="padding: 2px 0; font-weight: bold; color: #0f172a;">
                            {{ $order->created_at ? $order->created_at->format('d-m-Y') : now()->format('d-m-Y') }}
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 2px 0; color: #475569;">Place of Supply:</td>
                        <td style="padding: 2px 0; font-weight: bold; color: #0f172a;">
                            {{ $placeOfSupply }}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- 3. Line Items Table -->
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 5%;" class="text-center">#</th>
                <th style="width: 35%;" class="text-left">Item Name</th>
                <th style="width: 11%;" class="text-center">HSN/ SAC</th>
                <th style="width: 9%;" class="text-center">Quantity</th>
                <th style="width: 8%;" class="text-center">Unit</th>
                <th style="width: 14%;" class="text-right">Price/ Unit (₹)</th>
                <th style="width: 18%;" class="text-right">GST(₹)</th>
                <th style="width: 15%;" class="text-right">Amount(₹)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($items as $row)
                <tr>
                    <td class="text-center fw-bold">{{ $row['index'] }}</td>
                    <td class="text-left fw-bold" style="color: #0f172a;">{{ $row['name'] }}</td>
                    <td class="text-center text-muted">{{ $row['hsn'] }}</td>
                    <td class="text-center fw-bold">{{ $row['qty'] }}</td>
                    <td class="text-center">{{ $row['unit'] }}</td>
                    <td class="text-right">{{ formatIndianCurrency($row['price_unit']) }}</td>
                    <td class="text-right">
                        {{ formatIndianCurrency($row['tax_amount']) }}
                        <span style="font-size: 9px; color: #475569;">({{ number_format($row['tax_rate'], 1) }}%)</span>
                    </td>
                    <td class="text-right fw-bold">{{ formatIndianCurrency($row['amount']) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center" style="padding: 15px;">{{ __('No products found') }}</td>
                </tr>
            @endforelse

            <!-- Total Bar Row -->
            <tr style="background-color: #ffffff; font-weight: bold;">
                <td class="border-top border-bottom"></td>
                <td class="text-left border-top border-bottom fw-bold" style="font-size: 11.5px;">Total</td>
                <td class="border-top border-bottom"></td>
                <td class="text-center border-top border-bottom fw-bold" style="font-size: 11.5px;">{{ $totalQty }}</td>
                <td class="border-top border-bottom"></td>
                <td class="border-top border-bottom"></td>
                <td class="text-right border-top border-bottom fw-bold" style="font-size: 11px;">
                    {{ formatIndianCurrency($totalGst) }}
                </td>
                <td class="text-right border-top border-bottom fw-bold" style="font-size: 11.5px;">
                    {{ formatIndianCurrency($totalGross) }}
                </td>
            </tr>
        </tbody>
    </table>

    <!-- 4. Tax Summary & Totals / In Words (Split 2 Columns) -->
    <table class="avoid-break">
        <tr>
            <!-- Left Sub-Table: Tax Summary -->
            <td style="width: 58%; vertical-align: top; padding: 0; border-right: 1px solid #334155;">
                <table style="width: 100%; border-collapse: collapse; table-layout: fixed;">
                    <thead>
                        <tr class="bg-light-header">
                            <td colspan="5" style="padding: 5px 8px; border-bottom: 1px solid #334155; font-size: 11px; font-weight: bold; color: #0f172a;">
                                Tax Summary:
                            </td>
                        </tr>
                        <tr style="font-size: 9.5px; font-weight: bold; background-color: #ffffff; border-bottom: 1px solid #334155;">
                            <th rowspan="2" style="width: 20%; text-align: center; border-right: 1px solid #334155; border-bottom: 1px solid #334155; padding: 4px 2px; vertical-align: middle;">HSN/ SAC</th>
                            <th rowspan="2" style="width: 28%; text-align: right; border-right: 1px solid #334155; border-bottom: 1px solid #334155; padding: 4px 6px; vertical-align: middle;">Taxable Amount (₹)</th>
                            <th colspan="2" style="width: 32%; text-align: center; border-right: 1px solid #334155; border-bottom: 1px solid #334155; padding: 3px 2px;">IGST</th>
                            <th rowspan="2" style="width: 20%; text-align: right; border-bottom: 1px solid #334155; padding: 4px 6px; vertical-align: middle;">Total Tax (₹)</th>
                        </tr>
                        <tr style="font-size: 9px; font-weight: bold; background-color: #ffffff; border-bottom: 1px solid #334155;">
                            <th style="width: 12%; text-align: center; border-right: 1px solid #334155; border-bottom: 1px solid #334155; padding: 3px 2px;">Rate (%)</th>
                            <th style="width: 20%; text-align: right; border-right: 1px solid #334155; border-bottom: 1px solid #334155; padding: 3px 4px;">Amt (₹)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $hsnGroups = [];
                            $discountRatio = $grossTotal > 0 ? ($discountedSubtotal / $grossTotal) : 1.0;
                            foreach ($items as $item) {
                                $h = !empty($item['hsn']) ? $item['hsn'] : '0405';
                                if (!isset($hsnGroups[$h])) {
                                    $hsnGroups[$h] = [
                                        'hsn' => $h,
                                        'taxable_amount' => 0,
                                        'tax_rate' => $item['tax_rate'],
                                        'tax_amount' => 0,
                                    ];
                                }
                                $hsnGroups[$h]['taxable_amount'] += ($item['taxable_amount'] * $discountRatio);
                                $hsnGroups[$h]['tax_amount'] += ($item['tax_amount'] * $discountRatio);
                            }
                            $taxSummaryTaxableTotal = array_sum(array_column($hsnGroups, 'taxable_amount'));
                            $taxSummaryTaxTotal = array_sum(array_column($hsnGroups, 'tax_amount'));
                        @endphp
                        @foreach($hsnGroups as $group)
                            <tr>
                                <td class="text-center" style="border-right: 1px solid #334155; border-bottom: 1px solid #334155; font-size: 9.5px; padding: 4px 2px;">{{ $group['hsn'] }}</td>
                                <td class="text-right" style="border-right: 1px solid #334155; border-bottom: 1px solid #334155; font-size: 9.5px; padding: 4px 6px;">{{ formatIndianCurrency($group['taxable_amount'], false) }}</td>
                                <td class="text-center" style="border-right: 1px solid #334155; border-bottom: 1px solid #334155; font-size: 9.5px; padding: 4px 2px;">{{ number_format($group['tax_rate'], 1) }}</td>
                                <td class="text-right" style="border-right: 1px solid #334155; border-bottom: 1px solid #334155; font-size: 9.5px; padding: 4px 6px;">{{ formatIndianCurrency($group['tax_amount'], false) }}</td>
                                <td class="text-right" style="border-bottom: 1px solid #334155; font-size: 9.5px; padding: 4px 6px;">{{ formatIndianCurrency($group['tax_amount'], false) }}</td>
                            </tr>
                        @endforeach
                        <tr style="font-weight: bold; background-color: #ffffff;">
                            <td class="text-center fw-bold" style="border-right: 1px solid #334155; font-size: 10px; padding: 5px 2px;">TOTAL</td>
                            <td class="text-right fw-bold" style="border-right: 1px solid #334155; font-size: 10px; padding: 5px 6px;">{{ formatIndianCurrency($taxSummaryTaxableTotal, false) }}</td>
                            <td style="border-right: 1px solid #334155; padding: 5px 2px;"></td>
                            <td class="text-right fw-bold" style="border-right: 1px solid #334155; font-size: 10px; padding: 5px 6px;">{{ formatIndianCurrency($taxSummaryTaxTotal, false) }}</td>
                            <td class="text-right fw-bold" style="font-size: 10px; padding: 5px 6px;">{{ formatIndianCurrency($taxSummaryTaxTotal, false) }}</td>
                        </tr>
                    </tbody>
                </table>
            </td>

            <!-- Right Sub-Table: Totals & Amount in Words -->
            <td style="width: 42%; vertical-align: top; padding: 0;">
                <table style="width: 100%; border-collapse: collapse; font-size: 11px;">
                    <tr class="border-bottom">
                        <td style="width: 50%; padding: 4px 8px; color: #1e293b;">Gross Total (MRP)</td>
                        <td style="width: 4%; text-align: center;">:</td>
                        <td style="width: 46%; text-align: right; padding: 4px 8px; font-weight: bold;">
                            {{ formatIndianCurrency($grossTotal) }}
                        </td>
                    </tr>
                    @if($preTaxable > 0)
                        <tr class="border-bottom">
                            <td style="padding: 3px 8px; color: #64748b; font-size: 10px;">Price Without GST (Taxable Base)</td>
                            <td style="text-align: center; color: #64748b;">:</td>
                            <td style="text-align: right; padding: 3px 8px; color: #475569; font-size: 10px;">
                                {{ formatIndianCurrency($preTaxable) }}
                            </td>
                        </tr>
                    @endif
                    @if($cardDiscount > 0)
                        <tr class="border-bottom">
                            <td style="padding: 4px 8px; color: #dc2626;">Card Discount</td>
                            <td style="text-align: center; color: #dc2626;">:</td>
                            <td style="text-align: right; padding: 4px 8px; font-weight: bold; color: #dc2626;">
                                -{{ formatIndianCurrency($cardDiscount) }}
                            </td>
                        </tr>
                    @endif
                    @if($couponDiscount > 0)
                        <tr class="border-bottom">
                            <td style="padding: 4px 8px; color: #dc2626;">Coupon Discount</td>
                            <td style="text-align: center; color: #dc2626;">:</td>
                            <td style="text-align: right; padding: 4px 8px; font-weight: bold; color: #dc2626;">
                                -{{ formatIndianCurrency($couponDiscount) }}
                            </td>
                        </tr>
                    @endif
                    @if($otherDiscount > 0)
                        <tr class="border-bottom">
                            <td style="padding: 4px 8px; color: #dc2626;">Special Discount</td>
                            <td style="text-align: center; color: #dc2626;">:</td>
                            <td style="text-align: right; padding: 4px 8px; font-weight: bold; color: #dc2626;">
                                -{{ formatIndianCurrency($otherDiscount) }}
                            </td>
                        </tr>
                    @endif
                    @if($orderLevelDiscount > 0 && $netTaxable > 0)
                        <tr class="border-bottom">
                            <td style="padding: 3px 8px; color: #047857; font-size: 10px; font-weight: 500;">Net Taxable Value (After Disc.)</td>
                            <td style="text-align: center; color: #047857;">:</td>
                            <td style="text-align: right; padding: 3px 8px; font-weight: bold; color: #047857; font-size: 10px;">
                                {{ formatIndianCurrency($netTaxable) }}
                            </td>
                        </tr>
                    @endif
                    <tr class="border-bottom">
                        <td style="width: 50%; padding: 5px 8px; color: #1e293b; font-weight: bold;">Net Subtotal</td>
                        <td style="width: 4%; text-align: center;">:</td>
                        <td style="width: 46%; text-align: right; padding: 5px 8px; font-weight: bold;">
                            {{ formatIndianCurrency($discountedSubtotal) }}
                        </td>
                    </tr>
                    @if($taxAmountTotal > 0)
                        <tr class="border-bottom">
                            <td style="padding: 4px 8px; color: #1e293b; font-size: 10px;">Incl. GST ({{ number_format($defaultTaxRate, 1) }}%)</td>
                            <td style="text-align: center;">:</td>
                            <td style="text-align: right; padding: 4px 8px; font-weight: normal; font-size: 10px; color: #475569;">
                                [{{ formatIndianCurrency($taxAmountTotal) }}]
                            </td>
                        </tr>
                    @endif
                    @if($deliveryCharge > 0)
                        <tr class="border-bottom">
                            <td style="padding: 4px 8px; color: #1e293b;">Delivery Charges</td>
                            <td style="text-align: center;">:</td>
                            <td style="text-align: right; padding: 4px 8px; font-weight: bold;">
                                +{{ formatIndianCurrency($deliveryCharge) }}
                            </td>
                        </tr>
                    @endif
                    <tr class="border-bottom" style="background-color: #f8fafc;">
                        <td style="padding: 6px 8px; font-weight: bold; font-size: 11.5px;">Total</td>
                        <td style="text-align: center; font-weight: bold;">:</td>
                        <td style="text-align: right; padding: 6px 8px; font-weight: bold; font-size: 12px; color: #0f172a;">
                            {{ formatIndianCurrency($payableTotal) }}
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3" style="padding: 6px 8px;">
                            <div style="font-weight: bold; font-size: 10.5px; color: #334155; margin-bottom: 2px;">
                                Invoice Amount In Words :
                            </div>
                            <div style="font-size: 10px; color: #0f172a; font-weight: 600; line-height: 1.3;">
                                {{ $amountInWords }}
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- 5. Description & Terms and Conditions (Split 2 Columns) -->
    <table class="border-top avoid-break">
        <tr class="bg-light-header border-bottom">
            <td style="width: 50%; border-right: 1px solid #334155; font-size: 11px; padding: 4px 8px;">
                <strong>Description:</strong>
            </td>
            <td style="width: 50%; font-size: 11px; padding: 4px 8px;">
                <strong>Terms And Conditions:</strong>
            </td>
        </tr>
        <tr>
            <!-- Left Notes -->
            <td style="width: 50%; border-right: 1px solid #334155; vertical-align: top; padding: 8px; font-size: 10.5px; line-height: 1.4; color: #1e293b;">
                Whether the tax is payable on reverse charge - No
                @if(!empty($order->notes))
                    <div style="font-size: 9.5px; color: #64748b; margin-top: 4px;">{{ $order->notes }}</div>
                @endif
            </td>

            <!-- Right Terms -->
            <td style="width: 50%; vertical-align: top; padding: 8px; font-size: 10px; line-height: 1.35; color: #334155;">
                Thank you for doing business with us.
            </td>
        </tr>
    </table>

    <!-- 6. Bank Details & Authorized Signatory (Split 2 Columns) -->
    <table class="border-top avoid-break">
        <tr class="bg-light-header border-bottom">
            <td style="width: 50%; border-right: 1px solid #334155; font-size: 11px; padding: 4px 8px;">
                <strong>Bank Details:</strong>
            </td>
            <td style="width: 50%; font-size: 10px; padding: 4px 8px; line-height: 1.25;">
                <strong>For {{ $companyName }}:</strong>
            </td>
        </tr>
        <tr>
            <!-- Bank Details Box -->
            <td style="width: 50%; border-right: 1px solid #334155; vertical-align: top; padding: 8px; font-size: 10.5px;">
                <table style="width: 100%;">
                    <tr>
                        <td style="padding: 1px 0; width: 35%; color: #475569;">Name:</td>
                        <td style="padding: 1px 0; width: 65%; font-weight: bold; color: #0f172a;">{{ $bankName }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 1px 0; color: #475569;">Account No.:</td>
                        <td style="padding: 1px 0; font-weight: bold; color: #0f172a;">{{ $bankAccountNo }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 1px 0; color: #475569;">IFSC code:</td>
                        <td style="padding: 1px 0; font-weight: bold; color: #0f172a;">{{ $bankIfsc }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 1px 0; color: #475569;">Account Holder's Name:</td>
                        <td style="padding: 1px 0; font-weight: bold; color: #0f172a;">{{ $bankHolder }}</td>
                    </tr>
                </table>
            </td>

            <!-- Signature Box -->
            <td style="width: 50%; vertical-align: bottom; text-align: center; padding: 12px 8px 8px 8px;">
                <!-- Decorative Signatory Signature -->
                <div style="display: inline-block; margin-bottom: 2px;">
                    <svg width="130" height="40" viewBox="0 0 130 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M10 25C20 10 35 30 45 15C55 5 60 35 75 20C85 10 90 28 105 18C115 12 120 22 125 15" stroke="#0f172a" stroke-width="1.8" stroke-linecap="round"/>
                        <path d="M25 28C40 28 80 26 115 24" stroke="#0f172a" stroke-width="1.2" stroke-linecap="round"/>
                        <path d="M40 32C60 30 90 29 110 28" stroke="#0f172a" stroke-width="1" stroke-linecap="round"/>
                    </svg>
                </div>
                <div style="font-size: 10.5px; font-weight: 500; color: #334155;">
                    Authorized Signatory
                </div>
            </td>
        </tr>
    </table>

</div>

</body>
</html>

