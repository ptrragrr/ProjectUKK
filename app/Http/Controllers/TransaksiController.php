<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaksi;
use App\Models\Ticket;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TransaksiController extends Controller
{
    public function index()
{
    $tickets = Ticket::all();
    return response()->json($tickets);
}

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_kasir' => 'required|string',
            'metode_pembayaran' => 'required|string',
            'keranjang' => 'required',
            'total' => 'required|numeric',
            'bayar' => 'required|numeric',
        ]);

        DB::beginTransaction();

        try {
            $keranjang = json_decode($data['keranjang'], true);

            foreach ($keranjang as $item) {
                $ticket = Ticket::findOrFail($item['id_ticket']);

                if ($ticket->stok_ticket < $item['jumlah']) {
                    throw new \Exception("Stok tidak cukup untuk {$ticket->nama_ticket}");
                }

                $ticket->stok_ticket -= $item['jumlah'];
                $ticket->save();

                Transaksi::create([
                    'id_ticket' => $ticket->id,
                    'nama_kasir' => $data['nama_kasir'],
                    'metode_pembayaran' => $data['metode_pembayaran'],
                    'jumlah' => $item['jumlah'],
                    'total_harga' => $item['total_harga'],
                ]);
            }

            DB::commit();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
