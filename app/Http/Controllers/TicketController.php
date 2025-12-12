<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;

// Event realtime
use App\Events\TicketAdded;
use App\Events\TicketUpdated;
use App\Events\TicketDeleted;

class TicketController extends Controller
{
    public function get()
    {
        return response()->json(
            Ticket::orderBy('id', 'desc')->get()
        );
    }

    public function index(Request $request)
    {
        $query = Ticket::query();

        // Search
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('nama_event', 'like', "%{$request->search}%")
                  ->orWhere('jenis_tiket', 'like', "%{$request->search}%")
                  ->orWhere('deskripsi', 'like', "%{$request->search}%");
            });
        }

        // Sorting
        $query->orderBy('created_at', 'asc');

        // Pagination
        $perPage = $request->per ?? 10;
        return response()->json($query->paginate($perPage));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_event'    => 'required|string|max:255',
            'tanggal'       => 'required|date',
            'harga_tiket'   => 'required|numeric|min:0',
            'jenis_tiket'   => 'required|string',
            'deskripsi'     => 'nullable|string',
            'stok_tiket'    => 'required|integer',
            'stok_reserved' => 'nullable|integer',
        ]);

        // fallback default
        $validated['stok_reserved'] = $validated['stok_reserved'] ?? 0;

        $ticket = Ticket::create($validated);

        // 🔥 Broadcast ke semua client yang lain
        broadcast(new TicketAdded($ticket))->toOthers();

        return response()->json([
            'success' => true,
            'data' => $ticket
        ]);
    }

    public function show($id)
    {
        return response()->json(
            Ticket::findOrFail($id)
        );
    }

    public function update(Request $request, $id)
    {
        $ticket = Ticket::findOrFail($id);

        $validated = $request->validate([
            'nama_event'    => 'required|string|max:255',
            'tanggal'       => 'required|date',
            'harga_tiket'   => 'required|numeric|min:0',
            'jenis_tiket'   => 'required|string',
            'stok_tiket'    => 'required|integer',
            'stok_reserved' => 'nullable|integer',
            'deskripsi'     => 'nullable|string',
        ]);

        $validated['stok_reserved'] = $validated['stok_reserved'] ?? $ticket->stok_reserved;

        $ticket->update($validated);

        // 🔥 Broadcast ticket diupdate
        // broadcast(new TicketUpdated($ticket))->toOthers();
         broadcast(new TicketUpdated($ticket));

        return response()->json([
            'message' => 'Tiket berhasil diupdate',
            'data' => $ticket
        ]);
    }

    public function destroy($id)
    {
        $ticket = Ticket::findOrFail($id);
        $ticketId = $ticket->id;

        $ticket->delete();

        // 🔥 Broadcast ticket dihapus
        broadcast(new TicketDeleted($ticketId))->toOthers();

        return response()->json(['message' => 'Tiket berhasil dihapus']);
    }
}
