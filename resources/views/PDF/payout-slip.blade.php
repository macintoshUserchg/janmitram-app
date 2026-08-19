@php
    $generaleSetting = function_exists('generaleSetting') ? generaleSetting('setting') : null;
    $siteName = ($generaleSetting && !empty($generaleSetting->name)) ? $generaleSetting->name : 'Janmitram';
    $monthName = DateTime::createFromFormat('!m', $payout->month)?->format('F') ?? $payout->month;
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ __('Payout Statement') }} - {{ $payout->shop?->name }} ({{ $monthName }} {{ $payout->year }})</title>
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
        .text-emerald { color: #059669; }

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

        .payout-badge {
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

        .status-badge-settled {
            display: inline-block;
            background-color: #dcfce7;
            color: #15803d;
            font-size: 11px;
            font-weight: bold;
            padding: 3px 8px;
            border-radius: 4px;
            border: 1px solid #86efac;
        }

        /* Hero Highlight Card */
        .hero-payout-card {
            background-color: #f0fdf4;
            border: 1.5px solid #86efac;
            border-radius: 10px;
            padding: 14px 18px;
            margin-bottom: 18px;
        }

        .hero-amount {
            font-size: 24px;
            font-weight: bold;
            color: #047857;
            margin-top: 2px;
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
            border-top: 2px solid #047857;
            border-bottom: 2px solid #047857;
            background-color: #f0fdf4;
            padding: 8px;
            font-size: 13px;
            font-weight: bold;
            color: #047857;
        }

        /* Footer */
        .payout-footer {
            margin-top: 28px;
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
                                    {{ $siteName }}
                                </h1>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td style="padding-top: 6px;">
                            <p class="fw-bold text-dark" style="font-size: 12.5px;">{{ $siteName }} Partner Network</p>
                            <p class="text-muted" style="font-size: 10.5px; max-width: 320px;">
                                {{ $generaleSetting?->address ?? 'Corporate Finance Hub, Rajasthan, India' }}
                            </p>
                            <p class="text-muted" style="font-size: 10.5px; margin-top: 2px;">
                                @if($generaleSetting?->email)<strong>Email:</strong> {{ $generaleSetting->email }} @endif
                                @if($generaleSetting?->mobile) &nbsp;|&nbsp; <strong>Phone:</strong> {{ $generaleSetting->mobile }} @endif
                            </p>
                        </td>
                    </tr>
                </table>
            </td>

            <!-- Payout Meta Right -->
            <td style="width: 45%; vertical-align: top;" class="text-right">
                <div class="payout-badge">{{ __('PAYOUT STATEMENT') }}</div>
                <table style="width: 100%; margin-top: 4px;">
                    <tr>
                        <td class="text-right text-muted" style="font-size: 11px;">{{ __('Voucher Reference') }}:</td>
                        <td class="text-right fw-bold text-dark" style="font-size: 11px; padding-left: 8px;">#PAY-{{ $payout->year }}{{ str_pad($payout->month, 2, '0', STR_PAD_LEFT) }}-{{ str_pad($payout->id, 5, '0', STR_PAD_LEFT) }}</td>
                    </tr>
                    <tr>
                        <td class="text-right text-muted" style="font-size: 11px;">{{ __('Statement Period') }}:</td>
                        <td class="text-right fw-medium text-dark" style="font-size: 11px; padding-left: 8px;">{{ $monthName }} {{ $payout->year }}</td>
                    </tr>
                    <tr>
                        <td class="text-right text-muted" style="font-size: 11px;">{{ __('Settlement Status') }}:</td>
                        <td class="text-right" style="padding-left: 8px;">
                            <span class="status-badge-settled">{{ __('CREDITED TO WALLET') }}</span>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- Hero Settlement Summary Card -->
    <div class="hero-payout-card">
        <table style="width: 100%;">
            <tr>
                <td style="width: 50%; vertical-align: middle;">
                    <span class="text-muted" style="font-size: 11px; text-transform: uppercase; font-weight: bold; letter-spacing: 0.5px;">
                        {{ __('Net Earnings Settled') }}
                    </span>
                    <div class="hero-amount">
                        {{ showCurrency($payout->final_amount ?? $payout->total_amount ?? 0) }}
                    </div>
                </td>
                <td style="width: 50%; vertical-align: middle; text-align: right;">
                    <div style="font-size: 11px; color: #475569;">
                        <strong>Partner Shop:</strong> {{ $payout->shop?->name }}<br>
                        <strong>Settlement Date:</strong> {{ $payout->created_at ? $payout->created_at->format('d M Y') : now()->format('d M Y') }}
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Partner and Network Tier Context Cards -->
    <table style="margin-bottom: 14px;">
        <tr>
            <!-- Partner Details -->
            <td style="width: 49%; vertical-align: top;">
                <div class="info-card">
                    <div class="info-card-title">{{ __('Partner Shop Details') }}</div>
                    <p class="fw-bold text-dark" style="font-size: 12px; margin-bottom: 2px;">{{ $payout->shop?->name }}</p>
                    <p class="text-muted" style="font-size: 10.5px; line-height: 1.35; margin-bottom: 3px;">
                        <strong>Owner:</strong> {{ $payout->shop?->user?->name ?? 'Partner' }}<br>
                        <strong>Phone:</strong> {{ $payout->shop?->user?->phone ?? $payout->shop?->phone ?? 'N/A' }}<br>
                        <strong>Email:</strong> {{ $payout->shop?->user?->email ?? 'N/A' }}
                    </p>
                </div>
            </td>

            <td style="width: 2%;"></td>

            <!-- Tier & Parent Node -->
            <td style="width: 49%; vertical-align: top;">
                <div class="info-card">
                    <div class="info-card-title">{{ __('Network & Tier Profile') }}</div>
                    <p class="fw-bold text-dark" style="font-size: 12px; margin-bottom: 2px;">
                        {{ $payout->level ?? 'Level Member' }}
                    </p>
                    <p class="text-muted" style="font-size: 10.5px; line-height: 1.35;">
                        <strong>Sponsor Shop:</strong> {{ $payout->shop?->parent?->name ?? 'Root Node' }}<br>
                        <strong>Shop ID:</strong> #{{ $payout->shop_id }}
                    </p>
                </div>
            </td>
        </tr>
    </table>

    <!-- Commission & Earnings Breakdown Table -->
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 8%; text-align: center;">#</th>
                <th style="width: 52%; text-align: left;">{{ __('Commission Component / Revenue Stream') }}</th>
                <th style="width: 20%; text-align: center;">{{ __('Basis / Metrics') }}</th>
                <th style="width: 20%; text-align: right;">{{ __('Settled Amount') }}</th>
            </tr>
        </thead>
        <tbody>
            @if(isset($payout->direct_commission) && $payout->direct_commission > 0)
                <tr>
                    <td class="text-center text-muted">1</td>
                    <td><strong class="text-dark">{{ __('Direct Sales Commission') }}</strong></td>
                    <td class="text-center text-muted">Direct Orders</td>
                    <td class="text-right fw-bold text-dark">{{ showCurrency($payout->direct_commission) }}</td>
                </tr>
            @endif

            @if(isset($payout->network_commission) && $payout->network_commission > 0)
                <tr>
                    <td class="text-center text-muted">2</td>
                    <td><strong class="text-dark">{{ __('Sub-Shop / Network Commission') }}</strong></td>
                    <td class="text-center text-muted">Downline Volume</td>
                    <td class="text-right fw-bold text-dark">{{ showCurrency($payout->network_commission) }}</td>
                </tr>
            @endif

            @if(isset($payout->bonus_amount) && $payout->bonus_amount > 0)
                <tr>
                    <td class="text-center text-muted">3</td>
                    <td><strong class="text-dark">{{ __('Performance Bonus / Incentive') }}</strong></td>
                    <td class="text-center text-muted">Tier Target Achieved</td>
                    <td class="text-right fw-bold text-dark">{{ showCurrency($payout->bonus_amount) }}</td>
                </tr>
            @endif

            @if(!isset($payout->direct_commission) && !isset($payout->network_commission))
                <tr>
                    <td class="text-center text-muted">1</td>
                    <td><strong class="text-dark">{{ __('Monthly Partner Remittance') }}</strong></td>
                    <td class="text-center text-muted">{{ $monthName }} {{ $payout->year }}</td>
                    <td class="text-right fw-bold text-dark">{{ showCurrency($payout->final_amount ?? $payout->total_amount ?? 0) }}</td>
                </tr>
            @endif

            <tr class="summary-total-row">
                <td colspan="3" class="text-right fw-bold" style="border-radius: 6px 0 0 6px;">{{ __('Total Credited to Partner Wallet') }}:</td>
                <td class="text-right fw-bold" style="border-radius: 0 6px 6px 0; font-size: 14px;">{{ showCurrency($payout->final_amount ?? $payout->total_amount ?? 0) }}</td>
            </tr>
        </tbody>
    </table>

    <!-- Footer -->
    <table class="payout-footer">
        <tr>
            <td style="width: 65%; vertical-align: bottom;">
                <p class="fw-bold text-dark" style="font-size: 10px; margin-bottom: 2px;">{{ __('Financial Note') }}</p>
                <p class="text-muted" style="font-size: 9.5px; line-height: 1.35;">
                    This statement is electronically generated by the {{ $siteName }} Finance & Settlements engine.<br>
                    Settlement credits are directly deposited to the shop wallet balance.
                </p>
            </td>
            <td style="width: 35%; vertical-align: bottom;" class="text-right">
                <div class="seal-box">
                    <p class="fw-bold text-dark">{{ $siteName }}</p>
                    <p class="text-muted" style="font-size: 9px;">{{ __('Accounts & Settlement Department') }}</p>
                </div>
            </td>
        </tr>
    </table>

</body>
</html>
