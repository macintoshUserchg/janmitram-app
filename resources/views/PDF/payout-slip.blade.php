@php
    $generaleSetting = function_exists('generaleSetting') ? generaleSetting('setting') : null;
    $siteName = ($generaleSetting && !empty($generaleSetting->name)) ? $generaleSetting->name : 'Janmitram';
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Payout Statement - {{ $payout->shop?->name }} ({{ DateTime::createFromFormat('!m', $payout->month)?->format('F') }} {{ $payout->year }})</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 13px;
            color: #2D3748;
            background-color: #FFFFFF;
            margin: 0;
            padding: 20px;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        
        .header-table {
            width: 100%;
            border-bottom: 2px solid #25314C;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #25314C;
            letter-spacing: 1px;
            margin-bottom: 2px;
            text-transform: uppercase;
        }
        .company-tagline {
            font-size: 11px;
            font-weight: bold;
            color: #4A5568;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }
        .company-info {
            font-size: 11px;
            color: #5E6470;
            line-height: 15px;
        }
        .statement-title {
            font-size: 18px;
            font-weight: bold;
            color: #1A202C;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .voucher-badge {
            background-color: #EBF8FF;
            color: #2B6CB0;
            padding: 6px 12px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 12px;
            display: inline-block;
            margin-top: 5px;
        }

        .info-table {
            width: 100%;
            margin-bottom: 20px;
            border-spacing: 0;
        }
        .info-box {
            background: #F7FAFC;
            border: 1px solid #E2E8F0;
            border-radius: 6px;
            padding: 12px;
            vertical-align: top;
        }
        .box-title {
            font-size: 12px;
            font-weight: bold;
            color: #4A5568;
            text-transform: uppercase;
            border-bottom: 1px solid #CBD5E0;
            padding-bottom: 4px;
            margin-bottom: 8px;
        }
        
        .perf-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .perf-table th {
            background-color: #EDF2F7;
            color: #2D3748;
            font-size: 11px;
            text-transform: uppercase;
            padding: 8px;
            border: 1px solid #CBD5E0;
        }
        .perf-table td {
            padding: 10px;
            border: 1px solid #E2E8F0;
            text-align: center;
            font-size: 12px;
        }
        
        .breakdown-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }
        .breakdown-table th {
            background-color: #25314C;
            color: #FFFFFF;
            font-size: 12px;
            text-transform: uppercase;
            padding: 10px;
            text-align: left;
        }
        .breakdown-table td {
            padding: 10px;
            border-bottom: 1px solid #E2E8F0;
            font-size: 13px;
        }
        .total-row td {
            font-weight: bold;
            font-size: 15px;
            background-color: #EBF8FF;
            color: #2B6CB0;
            border-top: 2px solid #2B6CB0;
            border-bottom: 2px solid #2B6CB0;
        }

        .badge-success {
            background-color: #C6F6D5;
            color: #22543D;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: bold;
        }

        .footer {
            margin-top: 40px;
            border-top: 1px solid #E2E8F0;
            padding-top: 15px;
            font-size: 11px;
            color: #718096;
            text-align: center;
        }
    </style>
</head>
<body>

    <!-- Header Section with Official Janmitram Branding -->
    <table class="header-table">
        <tr>
            <td style="width: 60%;">
                <div class="company-name">JANMITRAM</div>
                <div class="company-tagline">Janmitram Partner Network Statement</div>
                <div class="company-info">
                    @if($generaleSetting?->email) <div>Email: {{ $generaleSetting->email }}</div> @else <div>Email: support@janmitram.com</div> @endif
                    @if($generaleSetting?->mobile) <div>Contact: {{ $generaleSetting->mobile }}</div> @endif
                    @if($generaleSetting?->address) <div>Address: {{ $generaleSetting->address }}</div> @endif
                </div>
            </td>
            <td style="width: 40%;" class="text-right">
                <div class="statement-title">Payout Statement</div>
                <div class="voucher-badge">Voucher # PAY-{{ $payout->year }}{{ str_pad($payout->month, 2, '0', STR_PAD_LEFT) }}-{{ str_pad($payout->id, 5, '0', STR_PAD_LEFT) }}</div>
                <div style="font-size: 11px; color: #718096; margin-top: 5px;">Period: {{ DateTime::createFromFormat('!m', $payout->month)?->format('F') }} {{ $payout->year }}</div>
            </td>
        </tr>
    </table>

    <!-- Info Section: Shop & Owner Details -->
    <table class="info-table">
        <tr>
            <td style="width: 49%;" class="info-box">
                <div class="box-title">Shop & Partner Details</div>
                <div><strong>Shop Name:</strong> {{ $payout->shop?->name }}</div>
                <div><strong>Owner Name:</strong> {{ $payout->shop?->user?->name ?? 'N/A' }}</div>
                <div><strong>Email:</strong> {{ $payout->shop?->user?->email ?? 'N/A' }}</div>
                <div><strong>Phone:</strong> {{ $payout->shop?->user?->phone ?? 'N/A' }}</div>
                <div><strong>Shop ID:</strong> #{{ $payout->shop_id }}</div>
            </td>
            <td style="width: 2%;"></td>
            <td style="width: 49%;" class="info-box">
                <div class="box-title">Statement & Status Info</div>
                <div><strong>Payout Month:</strong> {{ DateTime::createFromFormat('!m', $payout->month)?->format('F') }} {{ $payout->year }}</div>
                <div><strong>Credited On:</strong> {{ $payout->created_at ? $payout->created_at->format('d M Y') : 'N/A' }}</div>
                <div><strong>Status:</strong> <span class="badge-success">CREDITED TO WALLET</span></div>
                <div><strong>Sponsor Shop:</strong> {{ $payout->shop?->parent?->name ?? 'Root Network Node' }}</div>
                <div><strong>Qualification Tier:</strong> {{ $payout->level ?? 'Level 0 (Member)' }}</div>
            </td>
        </tr>
    </table>

    <!-- Qualification & Performance Matrix -->
    <div style="font-size: 12px; font-weight: bold; color: #2D3748; margin-bottom: 8px; text-transform: uppercase;">1. Performance Summary & Qualification Matrix</div>
    <table class="perf-table">
        <thead>
            <tr>
                <th>Tier Level</th>
                <th>Personal Sales (₹)</th>
                <th>Downline Group Sales (₹)</th>
                <th>Downline Group Size</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><span class="font-bold" style="color: #25314C;">{{ $payout->level ?? 'Level 0' }}</span></td>
                <td>₹{{ number_format((float) $payout->personal_sales, 2) }}</td>
                <td>₹{{ number_format((float) $payout->group_sales, 2) }}</td>
                <td>{{ number_format((int) $payout->group_size) }} Members</td>
            </tr>
        </tbody>
    </table>

    <!-- Itemized Commission & Payout Breakdown -->
    <div style="font-size: 12px; font-weight: bold; color: #2D3748; margin-bottom: 8px; text-transform: uppercase;">2. Itemized Earnings & Bonus Breakdown</div>
    <table class="breakdown-table">
        <thead>
            <tr>
                <th style="width: 10%;">#</th>
                <th style="width: 60%;">Earnings Component Description</th>
                <th style="width: 30%;" class="text-right">Amount (₹)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>1</td>
                <td>
                    <strong>Phase 1 Direct Personal Sales Commission (10%)</strong><br/>
                    <small style="color: #718096;">10% direct commission earned on personal delivered sales volume of ₹{{ number_format((float)$payout->personal_sales, 2) }}</small>
                </td>
                <td class="text-right font-bold">₹{{ number_format((float) $payout->phase1_amount, 2) }}</td>
            </tr>
            <tr>
                <td>2</td>
                <td>
                    <strong>Phase 2 Downline Group Volume Bonus (Slab %)</strong><br/>
                    <small style="color: #718096;">Group sales achievement slab bonus based on tier level {{ $payout->level ?? 'Level 0' }} across {{ number_format((int)$payout->group_size) }} downline shops</small>
                </td>
                <td class="text-right font-bold">₹{{ number_format((float) $payout->phase2_amount, 2) }}</td>
            </tr>
            <tr class="total-row">
                <td colspan="2" class="text-right">Total Net Payout Credited:</td>
                <td class="text-right">₹{{ number_format((float) $payout->total_payout, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <!-- Footer Section -->
    <div class="footer">
        <p>This statement is an official computer-generated monthly payout voucher issued by JANMITRAM.</p>
        <p style="margin-top: 4px;">No physical signature is required. For any inquiries regarding payout calculations, please contact partner support.</p>
    </div>

</body>
</html>
