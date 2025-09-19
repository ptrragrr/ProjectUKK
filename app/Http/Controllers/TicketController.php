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
         ->orderBy('id', 'desc') 
         ->paginate(10);

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

        $validated = $request->validate([
            'konser_id' => 'required|exists:konser,id',
            'jenis_tiket' => 'required|string|max:255',
            'harga_tiket' => 'required|numeric|min:1',  // PERBAIKAN: Tambah min:1
            'stok_tiket' => 'required|integer|min:1',   // PERBAIKAN: Tambah min:1
        ]);

        Log::info('Store Validated Data:', $validated);

        $ticket = new Ticket();
        $ticket->konser_id = $validated['konser_id'];
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
        $ticket = Ticket::find($id);

        if (!$ticket) {
            return response()->json(['message' => 'Tiket tidak ditemukan'], 404);
        }

        return response()->json([
            'tiket' => [
                'id' => $ticket->id,
                'jenis_tiket' => $ticket->jenis_tiket,
                'harga_tiket' => $ticket->harga_tiket,
                'stok_tiket' => $ticket->stok_tiket,
                'konser_id' => $ticket->konser_id,
            ]
        ]);
    }

    // ✏️ Update ticket - DIPERBAIKI
    public function update(Request $request, $id)
    {
        // DEBUG: Log request data
        Log::info('Update Request Data:', $request->all());
        Log::info('Update Request Method', ['method' => $request->method()]);
        Log::info('Update Ticket ID', ['id' => $id]);
        $ticket = Ticket::findOrFail($id);

        // PERBAIKAN: Gunakan 'required' bukan 'sometimes' karena frontend selalu kirim semua field
        $validated = $request->validate([
            'konser_id' => 'required|exists:konser,id',
            'jenis_tiket' => 'required|string|max:255',
            'harga_tiket' => 'required|numeric|min:1',  // PERBAIKAN: required + min:1
            'stok_tiket' => 'required|integer|min:1',   // PERBAIKAN: required + min:1
        ]);

        Log::info('Update Validated Data:', $validated);

        $ticket->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Tiket berhasil diupdate',
            'data' => $ticket
        ]);
    }

    // 🗑 Delete ticket
    public function destroy($id)
    {
        $ticket = Ticket::findOrFail($id);
        $ticket->delete();

        return response()->json(['message' => 'Ticket deleted successfully.']);
    }
}