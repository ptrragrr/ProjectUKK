<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function index(Request $request)
    {
        $query = Ticket::query();

        // PENTING: Handle parameter search
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            
            $query->where(function($q) use ($search) {
                $q->where('nama_event', 'like', "%{$search}%")
                  ->orWhere('jenis_tiket', 'like', "%{$search}%")
                //   ->orWhere('stok_tiket', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%");
            });
        }

        // Sorting (opsional)
        $query->orderBy('tanggal', 'desc');

        // Pagination
        $perPage = $request->per ?? 10;
        $tickets = $query->paginate($perPage);

        return response()->json($tickets);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_event' => 'required|string|max:255',
            'tanggal' => 'required|date',
            'harga_tiket' => 'required|numeric|min:0',
            'jenis_tiket' => 'required|string',
            'deskripsi' => 'nullable|string',
            'stok_tiket' => 'required|integer',
        ]);

        $ticket = Ticket::create($validated);

        return response()->json([
            'message' => 'Tiket berhasil ditambahkan',
            'data' => $ticket
        ], 201);
    }

    public function show($id)
    {
        $ticket = Ticket::findOrFail($id);
        return response()->json($ticket);
    }

    public function update(Request $request, $id)
    {
        $ticket = Ticket::findOrFail($id);

        $validated = $request->validate([
            'nama_event' => 'required|string|max:255',
            'tanggal' => 'required|date',
            'harga_tiket' => 'required|numeric|min:0',
            'jenis_tiket' => 'required|string',
            'stok_tiket' => 'required|integer',
            'deskripsi' => 'nullable|string',
        ]);

        $ticket->update($validated);

        return response()->json([
            'message' => 'Tiket berhasil diupdate',
            'data' => $ticket
        ]);
    }

    public function destroy($id)
    {
        $ticket = Ticket::findOrFail($id);
        $ticket->delete();

        return response()->json([
            'message' => 'Tiket berhasil dihapus'
        ]);
    }
}