<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use Illuminate\Http\Request;

class TransaksiController extends Controller
{
    // 🔍 Menampilkan semua transaksi dengan relasi tiket + user
    public function index()
    {
        $transaksis = Transaksi::with([
            'details.ticket', // cukup ambil tiket saja
            'user'
        ])->orderBy('created_at', 'desc')->get();

        return response()->json($transaksis);
    }

    // ➕ Menyimpan transaksi baru
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'kode_transaksi' => 'required|string|unique:transaksis,kode_transaksi',
            'metode_pembayaran' => 'required|string',
            'total_harga' => 'required|numeric|min:0',
            'bayar' => 'required|numeric|min:0',
            'status' => 'required|string',
        ]);

        $transaksi = Transaksi::create($validated);

        return response()->json(['success' => true, 'data' => $transaksi], 201);
    }

    // 👁 Tampilkan 1 transaksi lengkap dengan tiket
    public function show($id)
    {
        $transaksi = Transaksi::with([
            'details.ticket',
            'user'
        ])->findOrFail($id);

        return response()->json($transaksi);
    }

    // ✏️ Update transaksi
    public function update(Request $request, $id)
    {
        $transaksi = Transaksi::findOrFail($id);

        $validated = $request->validate([
            'metode_pembayaran' => 'sometimes|string',
            'total_harga' => 'sometimes|numeric|min:0',
            'bayar' => 'sometimes|numeric|min:0',
            'status' => 'sometimes|string',
        ]);

        $transaksi->update($validated);

        return response()->json(['success' => true, 'data' => $transaksi]);
    }

    // 🗑 Hapus transaksi
    public function destroy($id)
    {
        $transaksi = Transaksi::findOrFail($id);
        $transaksi->delete();

        return response()->json(['success' => true, 'message' => 'Transaksi berhasil dihapus.']);
    }

    // 💾 Simpan kode tiket
    public function simpanKodeTiket(Request $request, $id)
    {
        $request->validate([
            'kode_tiket' => 'required|string|max:50'
        ]);

        $transaksi = Transaksi::findOrFail($id);
        $transaksi->kode_tiket = $request->kode_tiket; // pastikan ada kolom kode_tiket di transaksis
        $transaksi->save();

        return response()->json(['message' => 'Kode tiket berhasil disimpan']);
    }

    // 🖨 Cetak tiket (PDF)
    public function cetakTiket($id)
    {
        $transaksi = Transaksi::with('details.ticket')->findOrFail($id);

        // Generate PDF tiket
        $pdf = \PDF::loadView('pdf.tiket', compact('transaksi'));
        return $pdf->download('tiket-'.$transaksi->kode_tiket.'.pdf');
    }
}
