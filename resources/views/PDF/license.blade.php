<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ __('Digital Product License Certificate') }}</title>
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

        .fw-normal { font-weight: normal; }
        .fw-medium { font-weight: 500; }
        .fw-bold { font-weight: bold; }

        /* Header */
        .header-table {
            margin-bottom: 20px;
            border-bottom: 2px solid #f1f5f9;
            padding-bottom: 16px;
        }

        .company-logo {
            max-height: 60px;
            max-width: 150px;
            object-fit: contain;
        }

        .cert-badge {
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

        /* License Box */
        .license-box {
            background-color: #f8fafc;
            border: 2px dashed #0f172a;
            border-radius: 10px;
            padding: 16px;
            text-align: center;
            margin: 18px 0;
        }

        .license-key {
            font-family: monospace;
            font-size: 18px;
            font-weight: bold;
            color: #d97706;
            letter-spacing: 1.5px;
            margin-top: 4px;
        }

        /* Info Card */
        .info-card {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 14px;
            margin-bottom: 16px;
        }

        .info-card-title {
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #64748b;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 5px;
            margin-bottom: 8px;
        }

        .footer {
            margin-top: 30px;
            border-top: 1px solid #e2e8f0;
            padding-top: 14px;
            font-size: 10px;
            color: #64748b;
            text-align: center;
        }
    </style>
</head>
<body>

    <!-- Header Section -->
    <table class="header-table">
        <tr>
            <td style="width: 55%; vertical-align: top;">
                @if(!empty($logo))
                    <img src="{{ $logo }}" alt="Logo" class="company-logo" />
                @else
                    <h1 style="font-size: 22px; color: #0f172a; font-weight: bold;">{{ config('app.name', 'Janmitram') }}</h1>
                @endif
                <p class="text-muted" style="font-size: 10.5px; margin-top: 4px;">
                    Official Software & Digital Asset License Certificate
                </p>
            </td>
            <td style="width: 45%; vertical-align: top;" class="text-right">
                <div class="cert-badge">{{ __('LICENSE CERTIFICATE') }}</div>
                <p class="text-muted" style="font-size: 11px; margin-top: 4px;">
                    Issued On: <strong class="text-dark">{{ now()->format('d M Y') }}</strong>
                </p>
            </td>
        </tr>
    </table>

    <!-- Licensee & Product Details -->
    <div class="info-card">
        <div class="info-card-title">{{ __('Licensed Asset & Grantee Information') }}</div>
        <table style="width: 100%;">
            <tr>
                <td style="width: 25%; padding: 4px 0;" class="text-muted">{{ __('Licensee Name') }}:</td>
                <td style="width: 75%; padding: 4px 0;" class="fw-bold text-dark">{{ $licenseData->user?->name ?? 'Valued Customer' }}</td>
            </tr>
            <tr>
                <td style="padding: 4px 0;" class="text-muted">{{ __('Licensed Product') }}:</td>
                <td style="padding: 4px 0;" class="fw-bold text-dark">{{ $licenseData->product?->name ?? 'Digital Product' }}</td>
            </tr>
            <tr>
                <td style="padding: 4px 0;" class="text-muted">{{ __('License Type') }}:</td>
                <td style="padding: 4px 0;" class="fw-medium text-dark">Standard Commercial License (Single Domain / Device)</td>
            </tr>
            <tr>
                <td style="padding: 4px 0;" class="text-muted">{{ __('Issuing Platform') }}:</td>
                <td style="padding: 4px 0;" class="text-dark">{{ config('app.url') }}</td>
            </tr>
        </table>
    </div>

    <!-- License Key Showcase -->
    <div class="license-box">
        <span class="text-muted" style="font-size: 11px; text-transform: uppercase; font-weight: bold; letter-spacing: 0.5px;">
            {{ __('Unique Purchase License Key') }}
        </span>
        <div class="license-key">
            {{ $licenseData->product_license ?? 'N/A' }}
        </div>
    </div>

    <!-- Terms -->
    <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px; font-size: 10px; color: #64748b; line-height: 1.45;">
        <strong class="text-dark">Terms of Use:</strong>
        This digital certificate confirms valid authorization to deploy and utilize the licensed digital product in accordance with the terms agreed upon during purchase. Unauthorized distribution, copying, or reverse-engineering is strictly prohibited.
    </div>

    <!-- Footer -->
    <div class="footer">
        {{ __('Thank you for choosing') }} {{ config('app.name', 'Janmitram') }}. &copy; {{ date('Y') }}. {{ __('All rights reserved.') }}
    </div>

</body>
</html>
