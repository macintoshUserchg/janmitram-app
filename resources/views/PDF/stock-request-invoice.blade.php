@php
    $directory = app()->getLocale() == 'ar' ? 'rtl' : 'ltr';
    $setting = generaleSetting('setting');
@endphp
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ $directory }}">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>{{ __('Stock Dispatch Invoice') }} - #INV-SR-{{ str_pad((string)$stockRequest->id, 5, '0', STR_PAD_LEFT) }}</title>
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

        .invoice-badge {
            display: inline-block;
            background-color: #0f172a;
            color: #ffffff;
            font-size: 15px;
            font-weight: bold;
            letter-spacing: 0.8px;
            padding: 6px 14px;
            border-radius: 6px;
            margin-bottom: 6px;
        }

        .status-badge-dispatched {
            display: inline-block;
            background-color: #dcfce7;
            color: #15803d;
            font-size: 11px;
            font-weight: bold;
            padding: 3px 8px;
            border-radius: 4px;
            border: 1px solid #86efac;
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

        /* Line Items Table */
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

        .summary-total-row td {
            border-top: 2px solid #0f172a;
            border-bottom: 2px solid #0f172a;
            background-color: #f8fafc;
            padding: 8px;
            font-size: 13px;
            font-weight: bold;
            color: #0f172a;
        }

        /* Footer */
        .invoice-footer {
            margin-top: 28px;
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
                            @if ($setting?->logo)
                                <img src="{{ $setting->logo }}" alt="Logo" class="company-logo" />
                            @else
                                <h1 style="font-size: 22px; color: #0f172a; font-weight: bold; margin-bottom: 2px;">
                                    {{ $setting?->name ?? 'Janmitram' }}
                                </h1>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td style="padding-top: 6px;">
                            <p class="fw-bold text-dark" style="font-size: 12.5px;">{{ $setting?->name ?? 'Janmitram Central Logistics' }}</p>
                            <p class="text-muted" style="font-size: 10.5px; max-width: 320px;">
                                {{ $setting?->address ?? 'Central Hub, Rajasthan, India' }}
                            </p>
                            <p class="text-muted" style="font-size: 10.5px; margin-top: 2px;">
                                @if($setting?->email)<strong>Email:</strong> {{ $setting->email }} @endif
                                @if($setting?->mobile) &nbsp;|&nbsp; <strong>Phone:</strong> {{ $setting->mobile }} @endif
                            </p>
                        </td>
                    </tr>
                </table>
            </td>

            <!-- Invoice Details Right -->
            <td style="width: 45%; vertical-align: top;" class="text-right">
                <div class="invoice-badge">{{ __('GOODS DISPATCH NOTE') }}</div>
                <table style="width: 100%; margin-top: 4px;">
                    <tr>
                        <td class="text-right text-muted" style="font-size: 11px;">{{ __('Transfer Invoice #') }}:</td>
                        <td class="text-right fw-bold text-dark" style="font-size: 11px; padding-left: 8px;">#INV-SR-{{ str_pad((string)$stockRequest->id, 5, '0', STR_PAD_LEFT) }}</td>
                    </tr>
                    <tr>
                        <td class="text-right text-muted" style="font-size: 11px;">{{ __('Dispatch Date') }}:</td>
                        <td class="text-right fw-medium text-dark" style="font-size: 11px; padding-left: 8px;">{{ $stockRequest->updated_at ? $stockRequest->updated_at->format('d M Y, h:i A') : now()->format('d M Y') }}</td>
                    </tr>
                    <tr>
                        <td class="text-right text-muted" style="font-size: 11px;">{{ __('Status') }}:</td>
                        <td class="text-right" style="padding-left: 8px;">
                            <span class="status-badge-dispatched">{{ strtoupper($stockRequest->status) }}</span>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- Sender & Recipient Cards -->
    <table style="margin-bottom: 14px;">
        <tr>
            <!-- Dispatching Warehouse -->
            <td style="width: 49%; vertical-align: top;">
                <div class="info-card">
                    <div class="info-card-title">{{ __('Dispatching Hub (Source)') }}</div>
                    <p class="fw-bold text-dark" style="font-size: 12px; margin-bottom: 2px;">{{ $stockRequest->warehouse?->name ?? 'Central Warehouse' }}</p>
                    <p class="text-muted" style="font-size: 10.5px; line-height: 1.35;">
                        {{ $stockRequest->warehouse?->address ?? 'Central Hub Warehouse, India' }}
                    </p>
                    <p class="text-muted" style="font-size: 10px; margin-top: 3px;">
                        <strong>Type:</strong> Regional Logistics Hub
                    </p>
                </div>
            </td>

            <td style="width: 2%;"></td>

            <!-- Receiving Shop -->
            <td style="width: 49%; vertical-align: top;">
                <div class="info-card">
                    <div class="info-card-title">{{ __('Receiving Shop (Destination)') }}</div>
                    <p class="fw-bold text-dark" style="font-size: 12px; margin-bottom: 2px;">{{ $stockRequest->shop?->name ?? 'Recipient Shop' }}</p>
                    <p class="text-muted" style="font-size: 10.5px; line-height: 1.35;">
                        {{ $stockRequest->shop?->address ?? 'Partner Retail Store, India' }}
                    </p>
                    <p class="text-muted" style="font-size: 10px; margin-top: 3px;">
                        @if($stockRequest->shop?->phone)<strong>Phone:</strong> {{ $stockRequest->shop->phone }} @endif
                    </p>
                </div>
            </td>
        </tr>
    </table>

    <!-- Line Items Table -->
    @php
        $totalItems = 0;
        $totalQuantity = 0;
        $totalValuation = 0;
    @endphp
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 5%; text-align: center;">#</th>
                <th style="width: 45%; text-align: left;">{{ __('Product Description') }}</th>
                <th style="width: 15%; text-align: center;">{{ __('Variant / SKU') }}</th>
                <th style="width: 10%; text-align: center;">{{ __('Req Qty') }}</th>
                <th style="width: 10%; text-align: center;">{{ __('Sent Qty') }}</th>
                <th style="width: 15%; text-align: right;">{{ __('Est. Rate') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($stockRequest->items as $item)
                @php
                    $qty = $item->quantity;
                    $sentQty = $item->approved_quantity ?? $qty;
                    $rate = $item->unit_cost ?? $item->product?->price ?? 0;
                    $totalItems++;
                    $totalQuantity += $sentQty;
                    $totalValuation += ($rate * $sentQty);
                @endphp
                <tr>
                    <td class="text-center text-muted">{{ $loop->iteration }}</td>
                    <td>
                        <strong class="text-dark">{{ $item->product?->name ?? 'Product' }}</strong>
                        @if($item->product?->brand)
                            <div class="text-muted" style="font-size: 9.5px;">Brand: {{ $item->product->brand->name }}</div>
                        @endif
                    </td>
                    <td class="text-center text-muted">
                        @if($item->color || $item->size)
                            {{ $item->color?->name ?? '' }} {{ $item->size?->name ? '('.$item->size->name.')' : '' }}
                        @else
                            Default
                        @endif
                    </td>
                    <td class="text-center text-muted">{{ $qty }}</td>
                    <td class="text-center fw-bold text-dark">{{ $sentQty }}</td>
                    <td class="text-right fw-medium">{{ showCurrency($rate) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center text-muted" style="padding: 16px;">{{ __('No items found in this dispatch note.') }}</td>
                </tr>
            @endforelse
            <tr class="summary-total-row">
                <td colspan="4" class="text-right fw-bold" style="border-radius: 6px 0 0 6px;">{{ __('Total Dispatched Inventory Units') }}:</td>
                <td class="text-center fw-bold">{{ $totalQuantity }}</td>
                <td class="text-right text-primary fw-bold" style="border-radius: 0 6px 6px 0;">{{ showCurrency($totalValuation) }}</td>
            </tr>
        </tbody>
    </table>

    <!-- Footer & Signatures -->
    <table class="invoice-footer">
        <tr>
            <td style="width: 50%; vertical-align: top;">
                <div style="border-top: 1px dashed #94a3b8; width: 80%; padding-top: 4px;">
                    <p class="fw-bold text-dark">{{ __('Dispatched By (Warehouse Logistics)') }}</p>
                    <p class="text-muted" style="font-size: 9px;">Signature & Verification Stamp</p>
                </div>
            </td>
            <td style="width: 50%; vertical-align: top;" class="text-right">
                <div class="signature-box" style="float: right;">
                    <p class="fw-bold text-dark">{{ __('Received By (Shop Manager)') }}</p>
                    <p class="text-muted" style="font-size: 9px;">Signature & Store Seal</p>
                </div>
            </td>
        </tr>
    </table>

</body>
</html>
