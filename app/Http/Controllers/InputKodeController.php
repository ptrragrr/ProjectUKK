<?php

namespace App\Http\Controllers;

use App\Models\TransaksiDetail;
use Illuminate\Http\Request;

class InputKodeController extends Controller
{
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

    // Jika transaksi batal → tiket juga batal
    if ($detail->transaksi->status_payment === 'cancelled') {
        $detail->status = 'Cancelled';
    }

    // Jika tiket expired
    if ($detail->status === 'Expired') {
        return response()->json([
            'success' => false,
            'message' => 'Kode tiket expired',
        ], 400);
    }

    // Format rupiah
    $detail->transaksi->total_harga_rp = 'Rp ' . number_format(
        $detail->transaksi->total_harga,
        0,
        ',',
        '.'
    );

    return response()->json([
        'success' => true,
        'message' => 'Tiket valid',
        'data' => $detail,
    ]);
}


public function cetak($kode)
{
    $detail = TransaksiDetail::with(['transaksi', 'ticket'])
        ->where('kode_tiket', $kode)
        ->firstOrFail();

    if (in_array($detail->transaksi->status_payment, ['pending', 'cancelled'])) {
    return response('Tiket belum dibayar, tidak bisa dicetak.', 403);
}

    // if ($detail->transaksi->status !== 'pending') {
    //     return response('Tiket belum dibayar, tidak bisa dicetak.', 403);
    // }

    if (strtolower($detail->status) === 'expired') {
        return response('Tiket ini sudah expired.', 403);
    }

    return view('tiket.cetak', ['detail' => $detail]);
}

public function selesai($kode)
{
    $detail = TransaksiDetail::where('kode_tiket', $kode)->first();

    if (!$detail) {
        return response()->json([
            'success' => false,
            'message' => 'Tiket tidak ditemukan'
        ], 404);
    }

    $detail->status = 'expired';
    $detail->save();

    return response()->json([
        'success' => true,
        'message' => 'Tiket berhasil diselesaikan',
        'data' => $detail
    ]);
}

}
