<?php

namespace App\Http\Controllers;

use App\Models\TransaksiDetail;
use Illuminate\Http\Request;

class InputKodeController extends Controller
{
    // ✅ Cek kode tiket
    public function cek($kode)
    {
        $detail = TransaksiDetail::with(['transaksi', 'ticket'])
            ->where('kode_tiket', $kode)
            ->first();

        if (!$detail) {
            return response()->json([
                'success' => false,
                'message' => 'Kode tiket tidak ditemukan',
            ], 404);
        }

        if ($detail->status === 'expired') {
            return response()->json([
                'success' => false,
                'message' => 'Kode tiket expired',
            ], 400);
        }

        $detail->transaksi->total_harga_rp = 'Rp ' . number_format($detail->transaksi->total_harga, 0, ',', '.');

        return response()->json([
            'success' => true,
            'message' => 'Tiket valid',
            'data' => $detail,
        ]);
    }

    // ✅ Cetak tiket (halaman blade)
    // public function cetak($kode)
    // {
    //     $detail = TransaksiDetail::with(['transaksi', 'ticket'])
    //         ->where('kode_tiket', $kode)
    //         ->firstOrFail();

    //     if( $detail->status == 'Expired') {
    //         return "Tiket ini sudah expired.";
    //     }
    //       if( $transaksi->status_payment == 'Pending') {
    //         return "Tiket ini belum melakukan pembayaran.";
    //     }

    //     return view('tiket.cetak', compact('detail'));
    // }

    public function cetak($kode)
{
    $detail = TransaksiDetail::with(['transaksi', 'ticket'])
        ->where('kode_tiket', $kode)
        ->firstOrFail();

    // ❌ Cek kalau tiket sudah expired
    if ($detail->status === 'Expired') {
        return "Tiket ini sudah expired.";
    }

    // ❌ Cek kalau transaksi belum dibayar
    if ($detail->transaksi->status_payment === 'Pending') {
        return "Tiket ini belum melakukan pembayaran.";
    }

    // ✅ Jika sudah dibayar
    return view('tiket.cetak', compact('detail'));
}

    // ✅ Selesai → Expired
    public function selesai($kode)
{
    // Pastikan field yang dipakai sesuai tabel
    $transaksi = \App\Models\TransaksiDetail::where('kode_tiket', $kode)->first();

    if (!$transaksi) {
        return response()->json([
            'success' => false,
            'message' => 'Tiket tidak ditemukan'
        ], 404);
    }

    // Update status tiket
    $transaksi->status = 'Expired';
    $transaksi->save();

    return response()->json([
        'success' => true,
        'message' => 'Tiket berhasil diselesaikan',
        'data' => $transaksi
    ]);
}
}
