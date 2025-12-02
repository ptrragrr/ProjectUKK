<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Events\TicketAdded;
use Illuminate\Http\Request;

class TicketController extends Controller
{

    public function get(){
        $tickets = Ticket::orderBy('id', 'desc')->get();

        return response()->json($tickets);
    }

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
        $query->orderBy('created_at', 'asc');

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
            // 'stok_reserved' => 0,
            'stok_reserved' => 'nullable|integer',
            'stok_tiket' => 'required|integer',
        ]);

        $ticket = Ticket::create($request->all());

    // Kirim event ke semua client
    broadcast(new TicketAdded($ticket))->toOthers();

    return response()->json([
        'success' => true,
        'data' => $ticket,
    ]);

        // $ticket = Ticket::create($validated);

        // return response()->json([
        //     'message' => 'Tiket berhasil ditambahkan',
        //     'data' => $ticket
        // ], 201);
    }

//     public function store(Request $request)
// {
//     $validated = $request->validate([
//         'nama_event' => 'required|string|max:255',
//         'tanggal' => 'required|date',
//         'harga_tiket' => 'required|numeric|min:0',
//         'jenis_tiket' => 'required|string',
//         'deskripsi' => 'nullable|string',
//         'stok_tiket' => 'required|integer',
//     ]);

//     // Default value stok_reserved
//     $validated['stok_reserved'] = 0;

//     $ticket = Ticket::create($validated);

//     broadcast(new TicketAdded($ticket))->toOthers();

//     return response()->json([
//         'success' => true,
//         'data' => $ticket,
//     ]);
// }

    public function show($id)
    {
        $ticket = Ticket::findOrFail($id);
        return response()->json($ticket);
    }

    public function update(Request $request, $id)
    {
        $ticket = Ticket::findOrFail($id);

        // $validated = $request->validate([
        //     'nama_event' => 'required|string|max:255',
        //     'tanggal' => 'required|date',
        //     'harga_tiket' => 'required|numeric|min:0',
        //     'jenis_tiket' => 'required|string',
        //     'stok_tiket' => 'required|integer',
        //     'stok_reserved' => 0,
        //     'deskripsi' => 'nullable|string',
        // ]);

        // $ticket->update($validated);

        $validated = $request->validate([
    'nama_event' => 'required|string|max:255',
    'tanggal' => 'required|date',
    'harga_tiket' => 'required|numeric|min:0',
    'jenis_tiket' => 'required|string',
    'stok_reserved' => 'nullable|integer',
    'stok_tiket' => 'required|integer',
    'deskripsi' => 'nullable|string',
]);

$validated['stok_reserved'] = $ticket->stok_reserved ?? 0;

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