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
        $tickets = Ticket::orderBy('id', 'desc')->paginate(10);

        return response()->json([
            'data' => $tickets->items(),
            'meta' => [
                'current_page' => $tickets->currentPage(),
                'last_page' => $tickets->lastPage(),
                'total' => $tickets->total(),
            ],
        ]);
    }

    // ➕ Store ticket
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_event'   => 'required|string|max:255',
            'tanggal'      => 'required|date',
            'jenis_tiket'  => 'required|string|max:255',
            'harga_tiket'  => 'required|numeric',
            'stok_tiket'   => 'required|integer|min:1',
            'deskripsi'    => 'nullable|string',
        ]);

        Ticket::create($validated);

        return response()->json(['message' => 'Tiket berhasil ditambahkan']);
    }

    // 👁 Show single ticket
    public function show($id)
    {
        $ticket = Ticket::find($id);

        if (!$ticket) {
            return response()->json(['message' => 'Tiket tidak ditemukan'], 404);
        }

        return response()->json([
            'tiket' => $ticket
        ]);
    }

    // ✏️ Update ticket
    public function update(Request $request, $id)
    {
        Log::info('=== UPDATE DEBUG START ===');
        Log::info('Request Data', $request->all());

        $validated = $request->validate([
            'nama_event'   => 'required|string|max:255',
            'tanggal'      => 'required|date',
            'jenis_tiket'  => 'required|string|max:255',
            'harga_tiket'  => 'required|numeric|min:1',
            'stok_tiket'   => 'required|integer|min:1',
            'deskripsi'    => 'nullable|string',
        ]);

        $ticket = Ticket::findOrFail($id);
        $ticket->update($validated);

        Log::info('Updated Ticket Data', $ticket->toArray());
        Log::info('=== UPDATE DEBUG END ===');

        return response()->json([
            'success' => true,
            'message' => 'Tiket berhasil diupdate',
            'ticket'  => $ticket
        ]);
    }

    // 🗑 Delete ticket
    public function destroy($id)
    {
        $ticket = Ticket::findOrFail($id);
        $ticket->delete();

        return response()->json([
            'success' => true,
            'message' => 'Ticket deleted successfully'
        ]);
    }

    // 📝 Edit ticket
    public function edit($id)
    {
        $ticket = Ticket::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $ticket
        ]);
    }
}
