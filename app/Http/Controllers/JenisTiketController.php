<?php

namespace App\Http\Controllers;

use App\Models\JenisTiket;
use Illuminate\Http\Request;

class JenisTiketController extends Controller
{
    // 🔹 List dengan pagination (opsional)
    public function index()
    {
        return response()->json(JenisTiket::paginate(10));
    }

    // 🔹 Ambil semua tanpa pagination
    public function all()
    {
        return response()->json(JenisTiket::all());
    }

    // 🔹 Simpan jenis tiket baru
    public function store(Request $request)
    {
        $validated = $request->validate([
            'jenis_tiket' => 'required|string|max:20',
            'harga' => 'required|numeric|min:0',
        ]);

        $jenis = JenisTiket::create($validated);

        return response()->json([
            'message' => 'Jenis tiket berhasil ditambahkan.',
            'data' => $jenis
        ], 201);
    }

    // 🔹 Detail satu jenis tiket
    public function show($id)
    {
        $jenis = JenisTiket::findOrFail($id);
        return response()->json($jenis);
    }

    // 🔹 Update data jenis tiket
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'jenis_tiket' => 'required|string|max:20',
            'harga' => 'required|numeric|min:0',
        ]);

        $jenis = JenisTiket::findOrFail($id);
        $jenis->update($validated);

        return response()->json([
            'message' => 'Jenis tiket berhasil diperbarui.',
            'data' => $jenis
        ]);
    }

    // 🔹 Hapus jenis tiket
    public function destroy($id)
    {
        $jenis = JenisTiket::findOrFail($id);
        $jenis->delete();

        return response()->json([
            'message' => 'Jenis tiket berhasil dihapus.'
        ]);
    }
}
