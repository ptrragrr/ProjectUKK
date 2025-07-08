<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ManagementController extends Controller
{
    // Menampilkan semua tiket
    public function index(Request $request)
    {
        $per = $request->per ?? 10;
        $page = $request->page ? $request->page - 1 : 0;

        DB::statement('set @no=0+' . $page * $per);
        $data = Ticket::when($request->search, function (Builder $query, string $search) {
            $query->where('nama_destinasi', 'like', "%$search%")
                ->orWhere('harga_tiket', 'like', "%$search%")
                ->orWhere('foto_destinasi', 'like', "%$search%")
                ->orWhere('stok_tiket', 'like', "%$search%");
        })->latest()->paginate($per, ['*', DB::raw('@no := @no + 1 AS no')]);

        return response()->json($data);
    }
    
    // Menampilkan detail tiket
    public function show($id)
    {
        $tiket = Tiket::findOrFail($id);
        return response()->json(['tiket' => $tiket]);
    }

    // Menyimpan tiket baru
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_destinasi' => 'required|string',
            'harga_tiket' => 'required|numeric',
            'stok_tiket' => 'required|numeric',
            'foto_destinasi' => 'nullable|image',
        ]);

        if ($request->hasFile('foto_destinasi')) {
            $validated['foto_destinasi'] = $request->file('foto_destinasi')->store('foto_destinasi', 'public');
        }

        $tiket = Tiket::create($validated);

        return response()->json(['message' => 'Tiket berhasil ditambahkan', 'tiket' => $tiket], 201);
    }

    // Update tiket
    public function update(Request $request, $id)
    {
        $tiket = Tiket::findOrFail($id);

        $validated = $request->validate([
            'nama_destinasi' => 'required|string',
            'harga_tiket' => 'required|numeric',
            'stok_tiket' => 'required|numeric',
            'foto_destinasi' => 'nullable|image',
        ]);

        if ($request->hasFile('foto_destinasi')) {
            $validated['foto_destinasi'] = $request->file('foto_destinasi')->store('foto_destinasi', 'public');
        }

        $tiket->update($validated);

        return response()->json(['message' => 'Tiket berhasil diperbarui', 'tiket' => $tiket]);
    }

    // Menghapus tiket
    public function destroy($id)
    {
        $tiket = Tiket::findOrFail($id);
        $tiket->delete();

        return response()->json(['message' => 'Tiket berhasil dihapus']);
    }
}
