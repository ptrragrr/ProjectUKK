<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran Pending</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #fefae0 0%, #b3b49a 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .pending-container {
            background: white;
            border-radius: 24px;
            padding: 60px 40px;
            text-align: center;
            max-width: 500px;
            width: 100%;
            box-shadow: 0 20px 40px rgba(28, 41, 13, 0.15);
        }

        .pending-icon {
            font-size: 80px;
            margin-bottom: 24px;
            animation: pulse 2s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }

        h1 {
            font-size: 32px;
            color: #381d03;
            margin-bottom: 12px;
            font-weight: 700;
        }

        .description {
            color: #736046;
            font-size: 16px;
            margin-bottom: 32px;
            line-height: 1.6;
        }

        .order-info {
            background: #fefae0;
            padding: 24px;
            border-radius: 12px;
            margin-bottom: 32px;
            border: 2px solid #b3b49a;
        }

        .order-info p {
            color: #381d03;
            font-size: 15px;
            margin-bottom: 8px;
        }

        .order-info strong {
            color: #676f53;
            font-weight: 600;
        }

        .order-id {
            font-family: 'Courier New', monospace;
            font-size: 18px;
            color: #1c290d;
            font-weight: 700;
            margin-top: 12px;
            padding: 12px;
            background: white;
            border-radius: 8px;
            word-break: break-all;
        }

        .info-box {
            background: #fff9e6;
            border-left: 4px solid #f59e0b;
            padding: 16px;
            margin-bottom: 24px;
            border-radius: 8px;
            text-align: left;
        }

        .info-box p {
            color: #92400e;
            font-size: 14px;
            line-height: 1.6;
        }

        .action-buttons {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .btn {
            padding: 16px 32px;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary {
            background: linear-gradient(135deg, #676f53, #1c290d);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(103, 111, 83, 0.5);
        }

        .btn-secondary {
            background: white;
            color: #381d03;
            border: 2px solid #b3b49a;
        }

        .btn-secondary:hover {
            background: #fefae0;
            border-color: #676f53;
            transform: translateY(-2px);
        }

        @media (max-width: 640px) {
            .pending-container {
                padding: 40px 24px;
            }

            h1 {
                font-size: 26px;
            }

            .pending-icon {
                font-size: 64px;
            }
        }
    </style>
</head>
<body>
    <div class="pending-container">
        <div class="pending-icon">⏳</div>
        <h1>Pembayaran Pending</h1>
        <p class="description">
            Pesanan Anda sedang menunggu pembayaran. Silakan selesaikan pembayaran untuk melanjutkan.
        </p>
        
        <div class="order-info">
            <p><strong>Order ID:</strong></p>
            <div class="order-id">{{ $orderId ?? 'Tidak tersedia' }}</div>
        </div>

        <div class="info-box">
            <p>
                💡 <strong>Catatan:</strong> Silakan cek email Anda untuk instruksi pembayaran lebih lanjut. 
                Atau Anda dapat melanjutkan pembayaran melalui akun Anda.
            </p>
        </div>

        <div class="action-buttons">
            <a href="/" class="btn btn-primary">
                🏠 Kembali ke Beranda
            </a>
            <a href="/dashboard_pengguna/checkout" class="btn btn-secondary">
                📋 Lihat Pesanan Saya
            </a>
        </div>
    </div>

    <script>
        // Log untuk debugging
        console.log('Halaman Pending dimuat');
        console.log('Order ID:', '{{ $orderId ?? "null" }}');
    </script>
</body>
</html>