<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Tiket Konser</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .ticket-container { perspective: 1000px; margin: 20px; }
        .wristband {
            width: 600px; height: 180px; border-radius: 90px;
            position: relative;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3),
                        inset 0 2px 10px rgba(255, 255, 255, 0.2),
                        inset 0 -2px 10px rgba(0, 0, 0, 0.2);
            animation: gradientShift 4s ease-in-out infinite;
            transform: rotateX(5deg) rotateY(-5deg);
            transition: transform 0.3s ease;
            background-size: 400% 400%;
        }
        .wristband.vip { background: linear-gradient(45deg, #e74c3c, #c0392b, #ff4757, #ff3742); }
        .wristband.vvip { background: linear-gradient(45deg, #f39c12, #d35400, #ffb142, #ff9500); }
        .wristband.reguler { background: linear-gradient(45deg, #27ae60, #16a085, #00d2d3, #01a3a4); }
        .wristband:hover { transform: rotateX(0deg) rotateY(0deg) scale(1.02); }
        @keyframes gradientShift { 0%,100%{background-position:0% 50%} 50%{background-position:100% 50%} }
        .wristband-inner {
            position: absolute; top: 15px; left: 15px; right: 15px; bottom: 15px;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 75px;
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 40px; backdrop-filter: blur(10px);
            border: 2px solid rgba(255, 255, 255, 0.3);
        }
        .ticket-info { flex: 1; text-align: center; }
        .concert-name { font-size: 24px; font-weight: 900; color: #2c3e50; text-transform: uppercase; margin-bottom: 8px; }
        .ticket-type { font-size: 14px; font-weight: 600; text-transform: uppercase; margin-bottom: 12px; }
        .ticket-type.vip { background: linear-gradient(45deg, #e74c3c, #c0392b); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .ticket-type.vvip { background: linear-gradient(45deg, #f39c12, #d35400); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .ticket-type.reguler { background: linear-gradient(45deg, #27ae60, #16a085); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .ticket-code {
            font-size: 18px; font-weight: 800; color: #34495e; font-family: 'Courier New', monospace;
            background: rgba(52, 73, 94, 0.1); padding: 8px 16px; border-radius: 20px;
            border: 2px dashed #34495e; display: inline-block;
        }
        .wristband-buckle {
            width: 40px; height: 80px; background: linear-gradient(145deg, #ecf0f1, #bdc3c7);
            border-radius: 20px; position: relative;
            box-shadow: inset 2px 2px 5px rgba(0, 0, 0, 0.2), inset -2px -2px 5px rgba(255, 255, 255, 0.7);
        }
        .wristband-buckle::before {
            content: ''; position: absolute; top: 15px; left: 8px; right: 8px; bottom: 15px;
            background: #34495e; border-radius: 12px;
        }
        .wristband.vip .wristband-buckle::after { background: linear-gradient(45deg, #e74c3c, #c0392b); }
        .wristband.vvip .wristband-buckle::after { background: linear-gradient(45deg, #f39c12, #d35400); }
        .wristband.reguler .wristband-buckle::after { background: linear-gradient(45deg, #27ae60, #16a085); }
        .decorative-holes { position: absolute; right: 100px; top: 50%; transform: translateY(-50%); display: flex; gap: 8px; }
        .hole { width: 8px; height: 8px; background: rgba(52, 73, 94, 0.3); border-radius: 50%; }
        .print-section { text-align: center; margin-top: 40px; }
        .btn-print {
            background: linear-gradient(45deg, #3498db, #2980b9);
            color: white; border: none; padding: 15px 30px; font-size: 16px; font-weight: 600;
            border-radius: 50px; cursor: pointer; transition: all 0.3s ease;
            text-transform: uppercase; letter-spacing: 1px; margin: 5px;
        }
        .btn-print:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(52, 152, 219, 0.6); }
        .btn-danger { background: linear-gradient(45deg, #e74c3c, #c0392b); }
        .expired-banner {
            background: #e74c3c; color: white; padding: 10px; border-radius: 8px;
            text-align: center; margin-bottom: 15px; font-weight: bold;
        }
        @media print { .print-section { display: none; } .wristband { transform: none; margin: 50px auto; } }
    </style>
</head>
<body>
    <div class="ticket-container">
        @php
            $jenisTicket = strtolower($detail->ticket->jenis_tiket ?? 'reguler');
            $ticketClass = $jenisTicket;
            if (str_contains($jenisTicket, 'vip') && !str_contains($jenisTicket, 'vvip')) {
                $ticketClass = 'vip';
            } elseif (str_contains($jenisTicket, 'vvip')) {
                $ticketClass = 'vvip';
            } else {
                $ticketClass = 'reguler';
            }
        @endphp

        {{-- Banner kalau expired --}}
        @if($detail->status === 'expired')
            <div class="expired-banner">⚠️ KODE TIKET EXPIRED</div>
        @endif

        <div class="wristband {{ $ticketClass }}">
            <div class="wristband-inner">
                <div class="ticket-info">
                    <div class="concert-name">OURSKY.FEST 2025</div>
                    <div class="ticket-type {{ $ticketClass }}">
                        {{ strtoupper($detail->ticket->jenis_tiket ?? 'REGULER') }}
                    </div>
                    <div class="ticket-code">{{ $detail->kode_tiket }}</div>
                </div>
                <div class="decorative-holes">
                    <div class="hole"></div><div class="hole"></div><div class="hole"></div>
                </div>
                <div class="wristband-buckle"></div>
                <div class="watermark">{{ $detail->status === 'expired' ? 'EXPIRED' : 'VALID' }}</div>
            </div>
        </div>

        <div class="print-section">
            <p><b>Nama:</b> {{ $detail->transaksi->nama_pembeli }}</p>
            <p><b>Email:</b> {{ $detail->transaksi->email }}</p>
            <p><b>Telp:</b> {{ $detail->transaksi->nomer_telpon }}</p>
            <p><b>Total:</b> Rp{{ number_format($detail->transaksi->total_harga, 0, ',', '.') }}</p>
            <br>

            @if($detail->status !== 'expired')
                <button class="btn-print" onclick="window.print()">🖨️ Cetak Tiket</button>
                <button class="btn-print btn-danger" onclick="selesaiTiket('{{ $detail->kode_tiket }}')">✅ Selesai</button>
            @endif
        </div>
    </div>

    <script>
    function selesaiTiket(kode) {
        if(confirm("Yakin tiket ini selesai dan di-expired?")) {
            fetch(`/selesai/${kode}`, {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    "Content-Type": "application/json"
                }
            })
            .then(res => res.json())
            .then(data => {
                alert(data.message);
                location.reload(); // reload supaya langsung muncul expired
            });
        }
    }
    </script>
</body>
</html>
