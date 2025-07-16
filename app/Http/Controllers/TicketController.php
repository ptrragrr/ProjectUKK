<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    // 🔍 Get all tickets

public function index(Request $request)
{
    $tickets = Ticket::with('konser')->paginate(10); // atau get() jika tanpa pagination

    return response()->json([
        'data' => $tickets->items(), // jika paginate
        'meta' => [
            'current_page' => $tickets->currentPage(),
            'last_page' => $tickets->lastPage(),
            'total' => $tickets->total(),
        ],
    ]);
}



    public function store(Request $request)
{
    $validated = $request->validate([
        'konser_id' => 'required|exists:konser,id',
        'jenis_tiket' => 'required|string',
        'harga_tiket' => 'required|numeric',
        'stok_tiket' => 'required|integer',
    ]);

    $ticket = new Ticket();
    $ticket->konser_id = $validated['konser_id'];
    $ticket->jenis_tiket = $validated['jenis_tiket'];
    $ticket->harga_tiket = $validated['harga_tiket'];
    $ticket->stok_tiket = $validated['stok_tiket'];
    $ticket->save();

    return response()->json(['message' => 'Tiket berhasil disimpan', 'ticket' => $ticket]);
}

    // ➕ Store new ticket
    // public function store(Request $request)
    // {
    //     $data = $request->validate([
    //         'konser_id' => 'required|exists:konser,id',
    //         'jenis_tiket' => 'required|string|max:255',
    //         'harga_tiket' => 'required|numeric|min:0',
    //         'stok_tiket' => 'required|integer|min:0',
    //     ]);

    //     $ticket = Ticket::create($data);

    //     return response()->json(['success' => true, 'data' => $ticket], 201);
    // }

    // 👁 Show single ticket
    public function show($id)
    {
        $ticket = Ticket::findOrFail($id);
        return response()->json($ticket);
    }

    // ✏️ Update ticket
    public function update(Request $request, $id)
    {
        $ticket = Ticket::findOrFail($id);

        $data = $request->validate([
            'konser_id' => 'sometimes|exists:konser,id',
            'jenis_tiket' => 'sometimes|string|max:255',
            'harga_tiket' => 'sometimes|numeric|min:0',
            'stok_tiket' => 'sometimes|integer|min:0',
        ]);

        $ticket->update($data);

        return response()->json(['success' => true, 'data' => $ticket]);
    }

    // 🗑 Delete ticket
    public function destroy($id)
    {
        $ticket = Ticket::findOrFail($id);
        $ticket->delete();

        return response()->json(['message' => 'Ticket deleted successfully.']);
    }
}

