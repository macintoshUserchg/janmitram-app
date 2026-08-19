<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful - Janmitram</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background: linear-gradient(135deg, #ecfdf5, #f0fdf4);
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .card {
            max-width: 460px;
            width: 90%;
            background: #ffffff;
            border: 1px solid #d1fae5;
            border-radius: 20px;
            padding: 32px 24px;
            text-align: center;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05), 0 10px 10px -5px rgba(0, 0, 0, 0.02);
            animation: fadeIn 0.4s ease-out;
        }
        .icon-circle {
            width: 72px;
            height: 72px;
            background: #dcfce7;
            color: #16a34a;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px;
            font-size: 36px;
            font-weight: bold;
        }
        .btn-home {
            background: #059669;
            color: #ffffff;
            border: none;
            padding: 10px 24px;
            border-radius: 10px;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            margin-top: 15px;
            transition: all 0.2s;
        }
        .btn-home:hover {
            background: #047857;
            color: #ffffff;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(12px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon-circle">✓</div>
        <h3 class="text-success fw-bold mb-1">Payment Successful!</h3>
        <p class="text-muted mt-2 mb-2">Your payment of <strong>₹{{ number_format((float)($payment->amount ?? 0), 2) }}</strong> has been verified.</p>
        
        @if(!empty($payment->payment_token))
            <p class="text-secondary small mb-3">Ref ID: <span class="badge bg-light text-dark border">{{ $payment->payment_token }}</span></p>
        @endif

        <p class="text-muted small mt-2 mb-0" id="countdown-text">
            Returning to order summary in <strong id="countdown">1</strong>s...
        </p>

        <a href="/order-history" class="btn-home mt-3">View Orders</a>
    </div>

    <script>
        // 1. Post message to opener window
        try {
            if (window.opener && !window.opener.closed) {
                window.opener.postMessage({
                    type: 'PAYMENT_SUCCESS',
                    paymentId: {{ (int)($payment->id ?? 0) }},
                    token: "{{ $payment->payment_token ?? '' }}"
                }, '*');
            }
        } catch (e) {
            console.warn('Opener postMessage notice:', e);
        }

        // 2. Broadcast via localStorage for cross-tab sync
        try {
            localStorage.setItem('janmitram_payment_event', JSON.stringify({
                status: 'success',
                paymentId: {{ (int)($payment->id ?? 0) }},
                timestamp: Date.now()
            }));
        } catch (e) {}

        // 3. Auto-close popup or fallback redirect
        let seconds = 1;
        const countdownEl = document.getElementById('countdown');
        const timer = setInterval(() => {
            seconds--;
            if (countdownEl) countdownEl.textContent = seconds;
            if (seconds <= 0) {
                clearInterval(timer);
                if (window.opener && !window.opener.closed) {
                    window.close();
                } else {
                    window.location.href = '/order-history';
                }
            }
        }, 800);
    </script>
</body>
</html>