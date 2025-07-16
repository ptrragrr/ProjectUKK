<?php
use App\Models\Transaksi;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class TicketController extends Controller
{
public function store(Request $request)
{
    $request->validate([
        'metode_pembayaran' => 'required|string',
        'total' => 'required|numeric',
        'bayar' => 'required|numeric',
        'tickets' => 'required|array',
        'tickets.*.id' => 'required|exists:tickets,id',
        'tickets.*.jumlah' => 'required|integer|min:1',
    ]);

    $transaksi = Transaksi::create([
        'user_id' => Auth::id(),
        'kode_transaksi' => 'TRX-' . strtoupper(Str::random(8)),
        'metode_pembayaran' => $request->metode_pembayaran,
        'total_harga' => $request->total,
        'bayar' => $request->bayar,
        'status' => 'paid',
    ]);

    // Simpan ke tabel pivot
    foreach ($request->tickets as $item) {
        $transaksi->tickets()->attach($item['id'], ['jumlah' => $item['jumlah']]);

        // Kurangi stok tiket
        $ticket = Ticket::find($item['id']);
        $ticket->stok_tiket -= $item['jumlah'];
        $ticket->save();
    }

    return response()->json([
        'success' => true,
        'message' => 'Transaksi berhasil disimpan',
        'data' => $transaksi->load('tickets')
    ]);
}
}
