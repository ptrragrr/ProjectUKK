<?php

use App\Http\Controllers\Controller;


use App\Models\Transaksi;
use Illuminate\Http\Request;

class TransaksiController extends Controller
{
    // 🔍 Menampilkan semua transaksi dengan relasi
    public function index()
    {
        $transaksis = Transaksi::with(['user', 'details'])->get();
        return response()->json(['success' => true, 'data' => $transaksis]);
    }

    // ➕ Menyimpan transaksi baru
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'kode_transaksi' => 'required|string|unique:transaksi,kode_transaksi',
            'metode_pembayaran' => 'required|string',
            'total_harga' => 'required|numeric|min:0',
            'bayar' => 'required|numeric|min:0',
            'status' => 'required|string',
        ]);

        $transaksi = Transaksi::create($validated);

        return response()->json(['success' => true, 'data' => $transaksi], 201);
    }

    // 👁 Tampilkan 1 transaksi
    public function show($id)
    {
        $transaksi = Transaksi::with(['user', 'details'])->findOrFail($id);
        return response()->json(['success' => true, 'data' => $transaksi]);
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
}
