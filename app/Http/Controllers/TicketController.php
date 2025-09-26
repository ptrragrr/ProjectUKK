<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TicketController extends Controller
{
    // 🔍 Get all tickets
    public function index(Request $request)
    {
        $tickets = Ticket::with('konser')
         ->orderBy('id', 'desc') ->paginate(10);

        return response()->json([
            'data' => $tickets->items(),
            'meta' => [
                'current_page' => $tickets->currentPage(),
                'last_page' => $tickets->lastPage(),
                'total' => $tickets->total(),
            ],
        ]);
    }

    public function store(Request $request)
    {
        // DEBUG: Log request data
        Log::info('Store Request Data:', $request->all());
        Log::info('Store Request Method', ['method' => $request->method()]);

        // PERBAIKAN: Handle both konser_id and konser field
        $validated = $request->validate([
            'konser_id' => 'nullable|exists:konser,id',
            'konser' => 'nullable|exists:konser,id', // Alternative field name
            'jenis_tiket' => 'required|string|max:255',
            'harga_tiket' => 'required|numeric|min:1',
            'stok_tiket' => 'required|integer|min:1',
        ]);

        // PERBAIKAN: Use konser_id or konser field
        $konser_id = $validated['konser_id'] ?? $validated['konser'] ?? null;
        
        if (!$konser_id) {
            return response()->json([
                'message' => 'Konser harus dipilih',
                'errors' => ['konser' => ['Field konser wajib diisi']]
            ], 422);
        }

        Log::info('Store Validated Data:', $validated);

        $ticket = new Ticket();
        $ticket->konser_id = $konser_id;
        $ticket->jenis_tiket = $validated['jenis_tiket'];
        $ticket->harga_tiket = $validated['harga_tiket'];
        $ticket->stok_tiket = $validated['stok_tiket'];
        $ticket->save();

        return response()->json([
            'message' => 'Tiket berhasil disimpan',
            'ticket' => $ticket
        ]);
    }

    // 👁 Show single ticket
    public function show($id)
    {
        $ticket = Ticket::with('konser')->find($id);

        if (!$ticket) {
            return response()->json(['message' => 'Tiket tidak ditemukan'], 404);
        }

        return response()->json([
            'tiket' => [
                'id' => $ticket->id,
                'konser_id' => $ticket->konser_id,
                'konser' => $ticket->konser->nama_konser ?? '', // Assuming konser has 'nama' field
                'jenis_tiket' => $ticket->jenis_tiket,
                'harga_tiket' => $ticket->harga_tiket,
                'stok_tiket' => $ticket->stok_tiket,
            ]
        ]);
    }

    // ✏️ Update ticket - DIPERBAIKI TOTAL
    public function update(Request $request, $id)
{
    // DEBUG: Log SEMUA data yang masuk
    Log::info('=== UPDATE DEBUG START ===');
    Log::info('Request All:', $request->all());
    Log::info('Request Input:', $request->input());
    Log::info('Request Method:', $request->method());
    Log::info('Ticket ID:', $id);
    
    // Validasi SUPER sederhana dulu
    $request->validate([
        'harga_tiket' => 'required',
        'stok_tiket' => 'required', 
        'jenis_tiket' => 'required'
    ]);
    
    $ticket = Ticket::findOrFail($id);
    $ticket->update($request->only(['harga_tiket', 'stok_tiket', 'jenis_tiket']));
    
    Log::info('=== UPDATE DEBUG END ===');
    
    return response()->json(['success' => true, 'message' => 'Berhasil update']);
}

public function updateMe(Request $request)
{
    Log::info('UpdateMe request data: ', $request->all()); // ✅ aman
    
    $request->validate([
        'name' => 'required|string|max:255',
        'phone' => 'nullable|string|max:20',
        'role_id' => 'nullable|exists:roles,id',
        'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
    ]);
}

    // 🗑 Delete ticket
    public function destroy($id)
    {
        $ticket = Ticket::findOrFail($id);
        $ticket->delete();

        return response()->json(['message' => 'Ticket deleted successfully.']);
    }

    // 📝 TAMBAHAN: Method untuk menampilkan form edit (jika menggunakan web routes)
    // public function edit($id)
    // {
    //     $ticket = Ticket::with('konser')->findOrFail($id);
        
    //     // Jika ini web route, return view
    //     // return view('tickets.edit', compact('ticket'));
        
    //     // Jika ini API route, return JSON
    //     return response()->json([
    //         'success' => true,
    //         'data' => [
    //             'id' => $ticket->id,
    //             'konser_id' => $ticket->konser_id,
    //             'konser' => $ticket->konser_id, // For form compatibility
    //             'jenis_tiket' => $ticket->jenis_tiket,
    //             'harga_tiket' => $ticket->harga_tiket,
    //             'stok_tiket' => $ticket->stok_tiket,
    //         ]
    //     ]);
    // }
    public function edit($id)
{
    $ticket = Ticket::with('konser')->findOrFail($id);

    return response()->json([
        'success' => true,
        'data' => [
            'id' => $ticket->id,
            'konser_id' => $ticket->konser_id,                // untuk value hidden / relasi ID
            'konser_nama' => $ticket->konser->nama_konser ?? '', // untuk ditampilkan di UI
            'jenis_tiket' => $ticket->jenis_tiket,
            'harga_tiket' => $ticket->harga_tiket,
            'stok_tiket' => $ticket->stok_tiket,
        ]
    ]);
}
}