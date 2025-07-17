<?php
namespace App\Http\Controllers;

use App\Models\Konser;
use Illuminate\Http\Request;

class KonserController extends Controller
{
    // 🔍 Get all konser
    public function index()
{
    $data = Konser::orderBy('tanggal', 'desc')->paginate(10);
    return response()->json($data); // ⬅️ Ini sudah sesuai struktur di atas
}


    // ➕ Tambah konser
    public function store(Request $request)
{
    $data = $request->validate([
        'nama_konser' => 'required|string|max:255',
        'lokasi' => 'required|string',
        'tanggal' => 'required|date',
        'deskripsi' => 'nullable|string',
        'banner' => 'required|file|image|mimes:jpg,jpeg,png|max:2048',
    ]);
//    Simpan banner ke folder storage
    if ($request->hasFile('banner')) {
        $file = $request->file('banner');
        $path = $file->store('banners', 'public'); // => storage/app/public/banners/xxx.jpg
        $data['banner'] = $path; // Simpan path ke kolom banner di DB
    }
    //   $data['banner'] = $request->file('banner')->store('banners', 'public');

    $konser = Konser::create($data);

    return response()->json(['success' => true, 'data' => $konser], 201);
}
    // public function store(Request $request)
    // {
    //     $data = $request->validate([
    //         'nama_konser' => 'required|string|max:255',
    //         'lokasi' => 'required|string',
    //         'tanggal' => 'required|date',
    //         'deskripsi' => 'nullable|string',
    //         'banner' => 'required|file|image|mimes:jpg,jpeg,png|max:2048',
    //     ]);

    //     $konser = Konser::create($data);

    //     return response()->json(['success' => true, 'data' => $konser], 201);
    // }

    // 👁 Tampilkan 1 konser
public function show($id)
{
    $konser = Konser::findOrFail($id); // Ambil berdasarkan id manual

    return response()->json([
        'konser' => [
            'nama_konser' => $konser->nama_konser,
            'lokasi' => $konser->lokasi,
            'tanggal' => $konser->tanggal,
            'deskripsi' => $konser->deskripsi,
            'banner' => $konser->banner,
        ]
    ]);
}


    // ✏️ Update konser
    public function update(Request $request, $id)
    {
        $konser = Konser::findOrFail($id);

        $data = $request->validate([
            'nama_konser' => 'sometimes|string|max:255',
            'lokasi' => 'sometimes|string',
            'tanggal' => 'sometimes|date',
            'deskripsi' => 'nullable|string',
            'banner' => 'nullable|file|image|mimes:jpg,jpeg,png|max:2048',
        ]);

    if ($request->hasFile('banner')) {
        $file = $request->file('banner');
        $path = $file->store('banners', 'public');
        $data['banner'] = $path;
    }

        $konser->update($data);

        return response()->json(['success' => true, 'data' => $konser]);
    }

    // 🗑 Hapus konser
    public function destroy($id)
    {
        $konser = Konser::findOrFail($id);
        $konser->delete();

        return response()->json(['message' => 'Konser berhasil dihapus.']);
    }
}
