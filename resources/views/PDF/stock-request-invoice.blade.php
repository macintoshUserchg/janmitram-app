@php
    $directory = app()->getLocale() == 'ar' ? 'rtl' : 'ltr';
    $setting = generaleSetting();
@endphp
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ $directory }}">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Janmitram Stock Dispatch Invoice #INV-SR-{{ str_pad((string)$stockRequest->id, 5, '0', STR_PAD_LEFT) }}</title>
    <style>
        body {
            font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
            color: #2D3748;
            font-size: 13px;
            line-height: 1.5;
            margin: 0;
            padding: 20px;
            background-color: #F8FAFC;
        }

        .invoice-box {
            max-width: 800px;
            margin: auto;
            padding: 25px;
            background-color: #ffffff;
            border: 2px solid #1E3A8A;
            border-radius: 10px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .table-full {
            width: 100%;
            border-collapse: collapse;
        }

        .header-title {
            font-size: 20px;
            font-weight: bold;
            margin: 0;
        }

        .badge-completed {
            background-color: #10B981;
            color: #ffffff;
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: bold;
            display: inline-block;
            border: 1px solid #059669;
        }

        .section-box {
            background-color: #F8FAFC;
            border: 1px solid #CBD5E0;
            border-radius: 8px;
            padding: 15px;
            margin-top: 15px;
        }

        .table-items {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            border: 1px solid #CBD5E0;
        }

        .table-items th {
            background-color: #EDF2F7;
            color: #2D3748;
            font-size: 11px;
            text-transform: uppercase;
            padding: 10px;
            border: 1px solid #CBD5E0;
            text-align: left;
        }

        .table-items td {
            padding: 10px;
            border: 1px solid #E2E8F0;
        }

        .text-right {
            text-align: right !important;
        }

        .text-center {
            text-align: center !important;
        }

        .fw-bold {
            font-weight: bold;
        }

        .text-muted {
            color: #718096;
        }

        .footer-signatures {
            margin-top: 35px;
            width: 100%;
            border-collapse: collapse;
        }

        .signature-box {
            border: 1px solid #CBD5E0;
            border-radius: 8px;
            padding: 15px;
            background-color: #F8FAFC;
        }

        .signature-line {
            border-top: 1px dashed #A0AEC0;
            width: 80%;
            margin: 30px auto 5px auto;
            font-weight: bold;
            color: #4A5568;
        }

        @media print {
            .no-print {
                display: none !important;
            }
            body {
                padding: 0;
                background-color: #ffffff;
            }
            .invoice-box {
                border: 2px solid #000000;
                box-shadow: none;
            }
        }
    </style>
</head>

<body>
    <div class="no-print" style="margin-bottom: 20px; text-align: right; max-width: 800px; margin-left: auto; margin-right: auto;">
        <button onclick="window.print()" style="background-color: #2563EB; color: white; border: none; padding: 8px 16px; border-radius: 4px; font-weight: bold; cursor: pointer;">
            🖨️ Print Dispatch Note
        </button>
    </div>

    <div class="invoice-box">
        <!-- Janmitram Brand Header Banner -->
        <table class="table-full" style="border-bottom: 2px solid #1E3A8A; padding-bottom: 12px; margin-bottom: 15px;">
            <tr>
                <td style="width: 50%; vertical-align: middle;">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        @if($setting?->logo)
                            <img src="{{ $setting->logo }}" alt="Janmitram Logo" style="max-height: 50px; width: auto; object-fit: contain;">
                        @endif
                        <div>
                            <h2 style="margin: 0; font-size: 20px; font-weight: 800; color: #1E3A8A; letter-spacing: 0.5px;">{{ strtoupper($setting?->name ?? 'JANMITRAM') }}</h2>
                            <div style="font-size: 11px; color: #4B5563; font-weight: 600;">Central Logistics & Retail Fulfillment Network</div>
                        </div>
                    </div>
                </td>
                <td class="text-right" style="width: 50%; vertical-align: middle;">
                    <h1 class="header-title" style="color: #1E3A8A; font-size: 18px; margin-bottom: 4px;">INVOICE / GOODS DISPATCH NOTE</h1>
                    <div style="font-size: 13px; font-weight: bold; color: #4B5563;">
                        Invoice #: <span style="color: #2563EB;">INV-SR-{{ str_pad((string)$stockRequest->id, 5, '0', STR_PAD_LEFT) }}</span>
                    </div>
                    <div style="margin-top: 4px;">
                        <span class="badge-completed">APPROVED & DISPATCHED</span>
                        <span style="font-size: 11px; margin-left: 8px;" class="text-muted">
                            {{ $stockRequest->updated_at ? $stockRequest->updated_at->format('d M, Y h:i A') : now()->format('d M, Y') }}
                        </span>
                    </div>
                </td>
            </tr>
        </table>

        <!-- Sender & Recipient Information Cards -->
        <table class="table-full">
            <tr>
                <td style="width: 48%; vertical-align: top;">
                    <div class="section-box">
                        <div class="fw-bold" style="color: #1E3A8A; font-size: 13px; margin-bottom: 6px; border-bottom: 1px solid #CBD5E0; padding-bottom: 4px;">
                            🏢 DISPATCHING WAREHOUSE (FROM)
                        </div>
                        <div><strong>{{ $stockRequest->warehouse?->name ?? 'Central Warehouse' }}</strong></div>
                        @if($stockRequest->warehouse?->address)
                            <div class="text-muted">{{ $stockRequest->warehouse->address }}</div>
                        @endif
                        <div class="text-muted" style="font-size: 11px; margin-top: 4px;">Type: Central Fulfillment Logistics Hub</div>
                    </div>
                </td>
                <td style="width: 4%;"></td>
                <td style="width: 48%; vertical-align: top;">
                    <div class="section-box">
                        <div class="fw-bold" style="color: #1E3A8A; font-size: 13px; margin-bottom: 6px; border-bottom: 1px solid #CBD5E0; padding-bottom: 4px;">
                            🏪 RECEIVING SHOP (TO)
                        </div>
                        <div><strong>{{ $stockRequest->shop?->name ?? 'Recipient Shop' }}</strong></div>
                        @if($stockRequest->shop?->address)
                            <div class="text-muted">{{ $stockRequest->shop->address }}</div>
                        @endif
                        <div class="text-muted" style="font-size: 11px; margin-top: 4px;">Shop ID: #{{ $stockRequest->shop_id }}</div>
                    </div>
                </td>
            </tr>
        </table>

        <!-- Itemized Products Table with Full Cell Borders -->
        <table class="table-items">
            <thead>
                <tr>
                    <th class="text-center" style="width: 40px;">#</th>
                    <th>Product & Master SKU</th>
                    <th>Category / Brand</th>
                    <th class="text-center" style="width: 100px;">Dispatched Qty</th>
                    <th class="text-right" style="width: 110px;">Unit Price</th>
                    <th class="text-right" style="width: 120px;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $totalQty = 0;
                    $totalValuation = 0;
                @endphp
                @foreach($stockRequest->items as $index => $item)
                    @php
                        $qty = $item->quantity;
                        $price = $item->product?->price ?? 0;
                        $subtotal = $qty * $price;
                        $totalQty += $qty;
                        $totalValuation += $subtotal;
                    @endphp
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>
                            <div class="fw-bold">{{ $item->product?->name ?? 'Product #'.$item->product_id }}</div>
                            <div class="text-muted" style="font-size: 11px;">
                                SKU: {{ $item->product?->code ?? 'N/A' }} | Master ID: #{{ $item->product_id }}
                                @if($item->color) | Color: {{ $item->color->name }} @endif
                                @if($item->size) | Size: {{ $item->size->name }} @endif
                            </div>
                        </td>
                        <td>
                            {{ $item->product?->brand?->name ?? '—' }}
                        </td>
                        <td class="text-center fw-bold">{{ $qty }} units</td>
                        <td class="text-right">{{ showCurrency($price) }}</td>
                        <td class="text-right fw-bold">{{ showCurrency($subtotal) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Financial & Quantity Summary Box -->
        <table class="table-full" style="margin-top: 15px;">
            <tr>
                <td style="width: 55%; vertical-align: top;">
                    @if($stockRequest->notes)
                        <div class="section-box">
                            <div class="fw-bold" style="font-size: 11px; text-transform: uppercase; color: #718096; border-bottom: 1px solid #CBD5E0; padding-bottom: 4px;">Dispatch Notes:</div>
                            <div style="font-style: italic; margin-top: 6px;">{{ $stockRequest->notes }}</div>
                        </div>
                    @endif
                </td>
                <td style="width: 45%; vertical-align: top;">
                    <div class="section-box" style="background-color: #EFF6FF; border: 1.5px solid #93C5FD;">
                        <table class="table-full" style="font-size: 13px;">
                            <tr>
                                <td>Total SKU Lines:</td>
                                <td class="text-right fw-bold">{{ $stockRequest->items->count() }} SKUs</td>
                            </tr>
                            <tr>
                                <td>Total Dispatched Units:</td>
                                <td class="text-right fw-bold">{{ $totalQty }} Physical Units</td>
                            </tr>
                            <tr style="border-top: 1.5px solid #3B82F6; font-size: 15px; font-weight: bold; color: #1E3A8A;">
                                <td style="padding-top: 8px;">Total Dispatch Value:</td>
                                <td class="text-right" style="padding-top: 8px;">{{ showCurrency($totalValuation) }}</td>
                            </tr>
                        </table>
                    </div>
                </td>
            </tr>
        </table>

        <!-- Signatures Footer -->
        <table class="footer-signatures">
            <tr>
                <td style="width: 48%; vertical-align: top;">
                    <div class="signature-box text-center">
                        <div class="signature-line">Authorized Warehouse Issuer</div>
                        <div class="text-muted" style="font-size: 10px;">{{ $stockRequest->warehouse?->name ?? 'Central Logistics Hub' }}</div>
                    </div>
                </td>
                <td style="width: 4%;"></td>
                <td style="width: 48%; vertical-align: top;">
                    <div class="signature-box text-center">
                        <div class="signature-line">Receiving Shop Representative</div>
                        <div class="text-muted" style="font-size: 10px;">{{ $stockRequest->shop?->name ?? 'Shop Manager' }}</div>
                    </div>
                </td>
            </tr>
        </table>

        <!-- Janmitram Platform Footer Branding -->
        <div style="margin-top: 25px; padding-top: 10px; border-top: 1px solid #E2E8F0; text-align: center; font-size: 10px; color: #718096;">
            <strong>JANMITRAM</strong> • Official Stock Transfer & Logistics Fulfillment Document • Generated on {{ now()->format('d M, Y h:i A') }}
        </div>
    </div>
</body>

</html>
