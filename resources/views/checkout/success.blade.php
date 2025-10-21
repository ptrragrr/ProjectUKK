<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Payment Success - TiketKonser</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #FEFAE0 0%, #B3B49A 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container {
            background: white;
            border-radius: 20px;
            padding: 50px 40px;
            max-width: 500px;
            width: 100%;
            box-shadow: 0 20px 60px rgba(28, 41, 13, 0.2);
            text-align: center;
            animation: slideUp 0.6s ease-out;
            border: 1px solid rgba(179, 180, 154, 0.3);
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .success-icon {
            width: 100px;
            height: 100px;
            margin: 0 auto 30px;
            position: relative;
            animation: scaleIn 0.5s ease-out 0.2s both;
        }

        @keyframes scaleIn {
            from {
                transform: scale(0);
            }
            to {
                transform: scale(1);
            }
        }

        .success-icon svg {
            width: 100%;
            height: 100%;
        }

        .checkmark {
            stroke-dasharray: 166;
            stroke-dashoffset: 166;
            animation: dash 0.6s ease-out 0.4s forwards;
        }

        @keyframes dash {
            to {
                stroke-dashoffset: 0;
            }
        }

        h1 {
            color: #381D03;
            font-size: 32px;
            margin-bottom: 15px;
            font-weight: 700;
        }

        .subtitle {
            color: #736046;
            font-size: 16px;
            margin-bottom: 30px;
            line-height: 1.6;
        }

        .order-info {
            background: linear-gradient(135deg, #FEFAE0 0%, #F5F1E3 100%);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 30px;
            border-left: 4px solid #676F53;
        }

        .order-info p {
            color: #736046;
            font-size: 14px;
            margin-bottom: 8px;
        }

        .order-info p:last-child {
            margin-bottom: 0;
        }

        .order-info strong {
            color: #381D03;
            font-weight: 600;
        }

        .order-id {
            color: #676F53;
            font-family: 'Courier New', monospace;
            font-weight: 600;
        }

        .button-group {
            display: flex;
            gap: 12px;
            flex-direction: column;
        }

        .btn {
            padding: 14px 28px;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
            cursor: pointer;
            border: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, #676F53 0%, #1C290D 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(103, 111, 83, 0.4);
        }

        .btn-secondary {
            background: white;
            color: #676F53;
            border: 2px solid #676F53;
        }

        .btn-secondary:hover {
            background: #FEFAE0;
            transform: translateY(-2px);
        }

        .info-text {
            margin-top: 25px;
            color: #A19379;
            font-size: 13px;
            line-height: 1.6;
        }

        @media (max-width: 480px) {
            .container {
                padding: 40px 25px;
            }

            h1 {
                font-size: 26px;
            }

            .success-icon {
                width: 80px;
                height: 80px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="success-icon">
            <svg viewBox="0 0 100 100">
                <circle cx="50" cy="50" r="45" fill="#676F53" opacity="0.2"/>
                <circle cx="50" cy="50" r="40" fill="none" stroke="#676F53" stroke-width="3"/>
                <path class="checkmark" d="M30,50 L43,63 L70,35" fill="none" stroke="#676F53" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </div>

        <h1>Pembayaran Berhasil!</h1>
        <p class="subtitle">Terima kasih! Pembayaran Anda telah berhasil diproses. Tiket konser Anda akan segera dikirim ke email.</p>

        <div class="order-info">
            <p><strong>Order ID:</strong> <span class="order-id" id="orderId">Loading...</span></p>
            <p><strong>Status:</strong> <span style="color: #676F53;">Paid</span></p>
            <p><strong>Tanggal:</strong> <span id="paymentDate">-</span></p>
        </div>

        <div class="button-group">
            <a href="/dashboard_pengguna/home" class="btn btn-primary">Kembali ke Beranda</a>
            <!-- <a href="/my-tickets" class="btn btn-secondary">Lihat Tiket Saya</a> -->
        </div>

        <p class="info-text">
            📧 E-ticket telah dikirim ke email Anda<br>
            Silakan cek inbox atau folder spam
        </p>
    </div>

    <script>
        // Get order ID from URL parameter
        const urlParams = new URLSearchParams(window.location.search);
        const orderId = urlParams.get('order_id') || 'ORDER-SJPD9Y0LRM';
        
        document.getElementById('orderId').textContent = orderId;
        
        // Set current date
        const now = new Date();
        const dateOptions = { 
            year: 'numeric', 
            month: 'long', 
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        };
        document.getElementById('paymentDate').textContent = now.toLocaleDateString('id-ID', dateOptions);
    </script>
</body>
</html>