@php
    $generaleSetting = function_exists('generaleSetting') ? generaleSetting('setting') : null;
    $siteName = ($generaleSetting && !empty($generaleSetting->name)) ? $generaleSetting->name : 'Janmitram';
    $terms = \App\Repositories\CardRepository::terms();
    $discountPct = $terms['percentage'] ?? 10;
    $minOrder = $terms['min_order_amount'] ?? 500;
    $holderName = $card->customer?->user?->name ?? 'Valued Janmitram Member';
    $holderPhone = $card->customer?->user?->phone ?? '—';
    $formattedCardNumber = chunk_split($card->card_number, 4, ' ');
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Janmitram Card - {{ $card->card_number }}</title>
    <style>
        @page {
            margin: 15mm;
            background-color: #F8FAFC;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            color: #1E293B;
            margin: 0;
            padding: 0;
            background-color: #F8FAFC;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        
        .page-header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 1px dashed #CBD5E1;
            padding-bottom: 15px;
        }
        .page-title {
            font-size: 18px;
            font-weight: bold;
            color: #0F172A;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-bottom: 4px;
        }
        .page-subtitle {
            font-size: 11px;
            color: #64748B;
        }

        .cards-wrapper {
            width: 100%;
            margin: 0 auto;
        }

        /* Physical Card Dimensions CR80 (approx 85.6mm x 54mm) */
        .card-container {
            width: 340px;
            height: 205px;
            border-radius: 12px;
            overflow: hidden;
            position: relative;
            box-sizing: border-box;
        }

        /* Front Card - Premium Emerald & Deep Navy Luxury Gradient */
        .card-front {
            background-color: #0F2D2A;
            color: #FFFFFF;
            padding: 16px 20px;
            border: 1.5px solid #10B981;
        }

        .front-header-table {
            width: 100%;
            border-collapse: collapse;
        }
        .card-brand {
            font-size: 15px;
            font-weight: bold;
            color: #FFFFFF;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        .card-type-tag {
            font-size: 8px;
            font-weight: bold;
            color: #34D399;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .vip-pill {
            background-color: #F59E0B;
            color: #78350F;
            font-size: 8px;
            font-weight: bold;
            padding: 3px 8px;
            border-radius: 20px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Chip & Contactless */
        .chip-table {
            width: 100%;
            margin-top: 10px;
            margin-bottom: 12px;
        }
        .chip-box {
            width: 38px;
            height: 28px;
            background-color: #D97706;
            border: 1px solid #FBBF24;
            border-radius: 5px;
        }
        .privilege-label {
            font-size: 9px;
            color: #A7F3D0;
            font-weight: bold;
            text-align: right;
            letter-spacing: 0.5px;
        }

        /* Card Number */
        .card-number-box {
            font-family: 'Courier', 'DejaVu Sans Mono', monospace;
            font-size: 20px;
            font-weight: bold;
            color: #F8FAFC;
            letter-spacing: 3px;
            margin-bottom: 12px;
            text-shadow: 1px 1px 2px #000000;
        }

        /* Cardholder Footer */
        .front-footer-table {
            width: 100%;
            border-collapse: collapse;
        }
        .label-tiny {
            font-size: 7px;
            color: #94A3B8;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
        }
        .val-name {
            font-size: 11px;
            font-weight: bold;
            color: #FFFFFF;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .val-date {
            font-size: 10px;
            font-weight: bold;
            color: #E2E8F0;
        }

        /* Back Card - Clean Operational / Verification */
        .card-back {
            background-color: #FFFFFF;
            color: #1E293B;
            border: 1.5px solid #CBD5E1;
            padding: 14px 16px;
        }
        .mag-stripe {
            width: 100%;
            height: 32px;
            background-color: #1E293B;
            margin-left: -16px;
            margin-right: -16px;
            margin-top: -14px;
            margin-bottom: 10px;
        }
        .back-content-table {
            width: 100%;
            border-collapse: collapse;
        }
        .qr-cell {
            width: 90px;
            text-align: center;
            vertical-align: top;
        }
        .qr-img {
            width: 78px;
            height: 78px;
            border: 1px solid #E2E8F0;
            border-radius: 6px;
            padding: 2px;
            background-color: #FFFFFF;
        }
        .terms-cell {
            vertical-align: top;
            padding-left: 10px;
        }
        .terms-heading {
            font-size: 9px;
            font-weight: bold;
            color: #0F172A;
            text-transform: uppercase;
            margin-bottom: 4px;
        }
        .terms-text {
            font-size: 7.5px;
            color: #475569;
            line-height: 10.5px;
            margin-bottom: 6px;
        }
        .support-info {
            font-size: 7.5px;
            color: #059669;
            font-weight: bold;
        }

        .cut-guide-note {
            font-size: 9px;
            color: #94A3B8;
            text-align: center;
            margin-top: 15px;
            font-style: italic;
        }

        .card-holder-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 20px 0;
        }
    </style>
</head>
<body>

    <div class="page-header">
        <div class="page-title">{{ $siteName }} Privilege Card</div>
        <div class="page-subtitle">Official Membership & Counter Verification Document • ID #{{ $card->id }}</div>
    </div>

    <table class="card-holder-table">
        <tr>
            <!-- FRONT SIDE -->
            <td style="width: 50%; vertical-align: top;">
                <div style="font-size: 10px; font-weight: bold; color: #475569; margin-bottom: 6px; text-transform: uppercase;">
                    [ Front Side ]
                </div>
                <div class="card-container card-front">
                    <table class="front-header-table">
                        <tr>
                            <td>
                                <div class="card-brand">{{ $siteName }}</div>
                                <div class="card-type-tag">Health & Privilege Card</div>
                            </td>
                            <td class="text-right">
                                <span class="vip-pill">{{ $discountPct }}% DISCOUNT</span>
                            </td>
                        </tr>
                    </table>

                    <table class="chip-table">
                        <tr>
                            <td style="width: 45px;">
                                <div class="chip-box"></div>
                            </td>
                            <td class="privilege-label">
                                INSTANT POS & ONLINE SAVINGS
                            </td>
                        </tr>
                    </table>

                    <div class="card-number-box">
                        {{ $formattedCardNumber }}
                    </div>

                    <table class="front-footer-table">
                        <tr>
                            <td>
                                <div class="label-tiny">Card Holder</div>
                                <div class="val-name">{{ Str::limit($holderName, 22) }}</div>
                            </td>
                            <td class="text-right" style="width: 90px;">
                                <div class="label-tiny">Issued Date</div>
                                <div class="val-date">{{ $card->created_at?->format('M Y') ?? '—' }}</div>
                            </td>
                        </tr>
                    </table>
                </div>
            </td>

            <!-- BACK SIDE -->
            <td style="width: 50%; vertical-align: top;">
                <div style="font-size: 10px; font-weight: bold; color: #475569; margin-bottom: 6px; text-transform: uppercase;">
                    [ Back Side ]
                </div>
                <div class="card-container card-back">
                    <table class="back-content-table" style="margin-top: 4px;">
                        <tr>
                            <td class="qr-cell">
                                @if(!empty($qrCodeImage))
                                    <img src="{{ $qrCodeImage }}" class="qr-img" alt="QR Code" />
                                    <div style="font-size: 7px; color: #64748B; font-weight: bold; margin-top: 3px;">
                                        SCAN AT POS
                                    </div>
                                @endif
                            </td>
                            <td class="terms-cell">
                                <div class="terms-heading">Terms & Conditions</div>
                                <div class="terms-text">
                                    • Present this card at any Janmitram Kendra or shop for instant {{ $discountPct }}% savings on qualifying orders of {{ showCurrency($minOrder) }}+.<br>
                                    • Card is non-transferable and digitally verified.<br>
                                    • In case of loss or inquiry, please contact Janmitram customer support.
                                </div>
                                <div class="support-info">
                                    Helpline: support@janmitram.com • janmitram.com
                                </div>
                            </td>
                        </tr>
                    </table>

                    <div style="border-top: 1px solid #E2E8F0; margin-top: 14px; padding-top: 6px;">
                        <table style="width: 100%;">
                            <tr>
                                <td style="font-size: 7.5px; color: #64748B;">
                                    <strong>Holder Contact:</strong> {{ $holderPhone }}
                                </td>
                                <td class="text-right" style="font-size: 7.5px; color: #64748B;">
                                    Status: <strong style="color: {{ $card->is_active ? '#059669' : '#DC2626' }};">{{ $card->is_active ? 'Active' : 'Inactive' }}</strong>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </td>
        </tr>
    </table>

    <div class="cut-guide-note">
        ✂ Cut along the outer borders of each card. Standard CR80 PVC / Cardstock printing recommended (85.6mm × 54.0mm).
    </div>

</body>
</html>
