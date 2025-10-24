<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tiket Konser - {{ $transaksi->kode_transaksi }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            background: linear-gradient(135deg, #FEFAE0 0%, #B3B49A 100%); 
            padding: 40px 20px; 
            line-height: 1.6;
        }
        .container { 
            max-width: 650px; 
            margin: auto; 
            background: #ffffff; 
            border-radius: 16px; 
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        .header {
            background: linear-gradient(135deg, #1C290D 0%, #676F53 50%, #A19379 100%);
            padding: 40px 30px;
            text-align: center;
            color: white;
        }
        .header h1 {
            font-size: 28px;
            margin-bottom: 10px;
            font-weight: 700;
        }
        .header p {
            font-size: 16px;
            opacity: 0.95;
        }
        .content {
            padding: 40px 30px;
        }
        .greeting {
            font-size: 18px;
            color: #2d3748;
            margin-bottom: 20px;
        }
        .greeting strong {
            color: #667eea;
            font-weight: 600;
        }
        .order-summary {
            background: linear-gradient(135deg, #f6f8fb 0%, #f1f4f9 100%);
            border-radius: 12px;
            padding: 25px;
            margin: 25px 0;
            border-left: 4px solid #667eea;
        }
        .order-row {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #e2e8f0;
            font-size: 15px;
        }
        .order-row:last-child {
            border-bottom: none;
            font-weight: 600;
            color: #667eea;
            font-size: 18px;
            margin-top: 10px;
            padding-top: 15px;
            border-top: 2px solid #667eea;
        }
        .order-label {
            color: #64748b;
            font-weight: 500;
        }
        .order-value {
            color: #1e293b;
            font-weight: 600;
        }
        .tickets-section {
            margin: 30px 0;
        }
        .section-title {
            font-size: 20px;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .ticket-card {
            background: #ffffff;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 15px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        .ticket-card:hover {
            border-color: #667eea;
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.15);
            transform: translateY(-2px);
        }
        .ticket-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 5px;
            height: 100%;
            background: linear-gradient(180deg, #667eea 0%, #764ba2 100%);
        }
        .ticket-event {
            font-size: 18px;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 10px;
            padding-left: 15px;
        }
        .ticket-code {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            font-family: 'Courier New', monospace;
            font-size: 16px;
            font-weight: 700;
            letter-spacing: 2px;
            margin-left: 15px;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        }
        .info-box {
            background: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 15px 20px;
            border-radius: 8px;
            margin: 25px 0;
            font-size: 14px;
            color: #92400e;
        }
        .info-box strong {
            display: block;
            margin-bottom: 5px;
            font-size: 15px;
        }
        .footer {
            background: #f8fafc;
            padding: 30px;
            text-align: center;
            border-top: 1px solid #e2e8f0;
        }
        .footer p {
            color: #64748b;
            font-size: 13px;
            margin: 8px 0;
        }
        .footer .copyright {
            margin-top: 15px;
            font-weight: 600;
            color: #94a3b8;
        }
        .icon {
            font-size: 24px;
        }
        @media (max-width: 600px) {
            body { padding: 20px 10px; }
            .header { padding: 30px 20px; }
            .content { padding: 30px 20px; }
            .header h1 { font-size: 24px; }
            .ticket-event { font-size: 16px; }
            .ticket-code { font-size: 14px; letter-spacing: 1px; }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>🎉 Pembelian Tiket Berhasil!</h1>
            <p>Tiket konsermu sudah siap digunakan</p>
        </div>

        <!-- Content -->
        <div class="content">
            <p class="greeting">
                Halo <strong>{{ $transaksi->nama_pembeli }}</strong>,
            </p>
            <p style="color: #64748b; margin-bottom: 25px;">
                Terima kasih telah mempercayai platform kami untuk pembelian tiket konser. Pesananmu telah berhasil diproses!
            </p>

            <!-- Order Summary -->
            <div class="order-summary">
                <div class="order-row">
                    <span class="order-label">Order ID</span>
                    <span class="order-value">{{ $transaksi->kode_transaksi }}</span>
                </div>
                <div class="order-row">
                    <span class="order-label">Nama Pembeli</span>
                    <span class="order-value">{{ $transaksi->nama_pembeli }}</span>
                </div>
                <div class="order-row">
                    <span class="order-label">Email</span>
                    <span class="order-value">{{ $transaksi->email }}</span>
                </div>
                <div class="order-row">
                    <span class="order-label">Total Pembayaran</span>
                    <span class="order-value">Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</span>
                </div>
            </div>

            <!-- Tickets Section -->
            <div class="tickets-section">
                <div class="section-title">
                    <span class="icon">🎫</span>
                    <span>Tiket Kamu</span>
                </div>

                @foreach ($transaksi->details as $index => $detail)
                <div class="ticket-card">
                    <div class="ticket-event">
                        {{ $detail->ticket->nama_event ?? 'Event Tidak Diketahui' }}
                    </div>
                    <div class="ticket-code">
                        {{ $codes[$index] ?? 'N/A' }}
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Info Box -->
            <div class="info-box">
                <strong>📱 Cara Menggunakan Tiket:</strong>
                Simpan email ini dan tunjukkan kode tiket saat check-in di lokasi konser. Pastikan kode tiket terlihat jelas untuk proses verifikasi yang lebih cepat.
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>Jika kamu merasa tidak melakukan pembelian ini atau memiliki pertanyaan, silakan hubungi customer service kami. (+62 812 3456 7890)</p>
            <p>Email ini dikirim secara otomatis, mohon untuk tidak membalas.</p>
            <p class="copyright">&copy; {{ date('Y') }} Tiket Konser. All rights reserved.</p>
        </div>
    </div>
</body>
</html>