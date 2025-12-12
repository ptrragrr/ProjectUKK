<?php

namespace App\Http\Controllers\pengguna;

use App\Events\TransaksiCreated;
use App\Events\TransaksiUpdated;
use App\Http\Controllers\Controller;
use App\Mail\TicketPaidMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Models\Ticket;
use App\Models\Transaksi;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Transaction;
use App\Models\pengguna\TiketKode;
use App\Models\TransaksiDetail;
use Illuminate\Support\Facades\Broadcast;

class CheckoutController extends Controller
{
    public function __construct()
    {
        Config::$serverKey    = config('midtrans.server_key');
        Config::$isProduction = (bool) config('midtrans.is_production', false);
        Config::$isSanitized  = (bool) config('midtrans.is_sanitized', true);
        Config::$is3ds        = (bool) config('midtrans.is_3ds', true);
    }

    public function store(Request $request)
    {
        return response()->json([
            'success' => true,
            'message' => 'Checkout berhasil!',
        ]);
    }

    // public function store(Request $request)
    // {
    //     $tiket = Ticket::findOrFail($request->tikets[0]['id']);

    //     // Jika tiket sudah dikunci user lain
    //     if ($tiket->is_locked && $tiket->locked_at->diffInSeconds(now()) < 300) { // 5 menit
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Tiket ini sedang di-checkout oleh pengguna lain. Coba lagi nanti.'
    //         ], 409);
    //     }

    //     // Kunci tiket sementara
    //     $tiket->update([
    //         'is_locked' => true,
    //         'locked_at' => now(),
    //     ]);

    //     // Lanjutkan bikin Snap Token Midtrans
    //     $snapToken = $this->createSnapToken($tiket, $request->user());

    //     return response()->json([
    //         'success' => true,
    //         'snap_token' => $snapToken,
    //     ]);
    // }

    /**
     * Halaman checkout
     */
    public function index(Request $request)
    {
        Log::info('All Request Data: ', $request->all());
        $ticketsData = $request->input('tickets', session('cart', []));
        if (is_string($ticketsData)) {
            $ticketsData = json_decode($ticketsData, true);
        }

        if (empty($ticketsData) || !is_array($ticketsData)) {
            return redirect()->route('home')->with('error', 'Silakan pilih tiket terlebih dahulu.');
        }

        $normalized = [];
        Log::info('Tickets Data: ', (array)$ticketsData);
        foreach ($ticketsData as $detail) {
            Log::info('Ticket Detail: ', (array)$detail);
            Log::info('qty: ' . ($detail['qty'] ?? 'not set'));

            // ✅ Tidak membuat TiketKode di sini
            $normalized[] = [
                'id' => $detail['id'],
                'qty' => $detail['qty'],
            ];
        }

        if (empty($normalized)) {
            return redirect()->route('home')->with('error', 'Tidak ada tiket valid yang dipilih.');
        }

        $ids = array_unique(array_column($normalized, 'id'));
        $tickets = Ticket::whereIn('id', $ids)->get()->keyBy('id');

        $keranjang = [];
        $subtotal = 0;

        foreach ($normalized as $row) {
            if (!isset($tickets[$row['id']])) continue;
            $ticket = $tickets[$row['id']];
            $qty = $row['qty'];

            if ($ticket->stok_tiket < $qty) {
                return redirect()->back()->with('error', "Stok tiket untuk {$ticket->jenis_tiket} tidak mencukupi.");
            }

            $price = (int) $ticket->harga_tiket;
            $lineTotal = $price * $qty;

            $keranjang[] = [
                'ticket' => $ticket,
                'qty' => $qty,
                'line_total' => $lineTotal,
            ];
            $subtotal += $lineTotal;
        }

        $tax = (int) round($subtotal * 0.10);
        $platformFee = (int) round($subtotal * 0.05);
        $total = $subtotal + $tax + $platformFee;

        return view('checkout', compact('keranjang', 'subtotal', 'tax', 'platformFee', 'total'));
    }

    /**
     * Proses pembayaran ke Midtrans
     */
    // public function pay(Request $request)
    // {
    //     $payload = $request->validate([
    //         'nama_pembeli' => 'required|string|max:150',
    //         'email' => 'required|email',
    //         'telepon' => 'required|string',
    //         'tickets' => 'required|array|min:1',
    //         'tickets.*.id' => 'required|integer|exists:tickets,id',
    //         'tickets.*.qty' => 'required|integer|min:1',
    //     ]);

    //     $ticketIds = array_unique(array_map(fn($t) => (int)$t['id'], $payload['tickets']));
    //     $tickets = Ticket::whereIn('id', $ticketIds)->get()->keyBy('id');

    //     $itemDetails = [];
    //     $subtotal = 0;

    //     foreach ($payload['tickets'] as $t) {
    //         $id = (int) $t['id'];
    //         $qty = (int) $t['qty'];
    //         if (!$tickets->has($id)) continue;

    //         $ticket = $tickets[$id];
    //         if ($ticket->stok_tiket < $qty) {
    //             return response()->json(['error' => true, 'message' => "Stok untuk {$ticket->jenis_tiket} tidak mencukupi."], 422);
    //         }
    //         $ticket->decrement('stok_tiket', $qty);
    //         $price = (int) $ticket->harga_tiket;
    //         $lineTotal = $price * $qty;
    //         $subtotal += $lineTotal;

    //         $itemDetails[] = [
    //             'id' => (string)$ticket->id,
    //             'price' => $price,
    //             'quantity' => $qty,
    //             'name' => "{$ticket->nama_event} - {$ticket->jenis_tiket}"
    //         ];
    //     }

    //     if (empty($itemDetails)) {
    //         return response()->json(['error' => true, 'message' => 'Tidak ada tiket valid.'], 422);
    //     }

    //     $tax = (int) round($subtotal * 0.10);
    //     $platformFee = (int) round($subtotal * 0.05);
    //     $grandTotal = $subtotal + $tax + $platformFee;

    //     if ($tax > 0) {
    //         $itemDetails[] = [
    //             'id' => 'tax',
    //             'price' => $tax,
    //             'quantity' => 1,
    //             'name' => 'Tax 10%'
    //         ];
    //     }
    //     if ($platformFee > 0) {
    //         $itemDetails[] = [
    //             'id' => 'platform_fee',
    //             'price' => $platformFee,
    //             'quantity' => 1,
    //             'name' => 'Platform Fee'
    //         ];
    //     }

    //     DB::beginTransaction();
    //     try {
    //         $orderId = 'ORDER-' . strtoupper(Str::random(10));

    //         $transaksi = Transaksi::create([
    //             'nama_pembeli' => $request->nama_pembeli,
    //             'email' => $request->email,
    //             'nomer_telpon' => $request->telepon,
    //             'kode_transaksi' => $orderId,
    //             'total_harga' => $grandTotal,
    //             'status_payment' => 'pending',
    //         ]);

    //         foreach ($payload['tickets'] as $t) {
    //             $ticket = $tickets[$t['id']];
    //             $price = (int) $ticket->harga_tiket;
    //             $totalLine = $price * $t['qty'];

    //             // TransaksiDetail::create([
    //             //     'transaksi_id' => $transaksi->id,
    //             //     'ticket_id' => $ticket->id,
    //             //     'jumlah' => $t['qty'],
    //             //     'harga_satuan' => $price,
    //             //     'total_harga' => $totalLine,
    //             //     'kode_tiket' => 'TKT-' . $transaksi->id . '-' . strtoupper(Str::random(6)),
    //             //     'status' => 'Available',
    //             // ]);
    //             for ($u = 0; $u < $t['qty']; $u++) {
    //                 $kode = 'PTR-' . $transaksi->id . '-' . strtoupper(Str::random(6));

    //                 TransaksiDetail::create([
    //                     'transaksi_id'   => $transaksi->id,
    //                     'ticket_id'      => $ticket->id,
    //                     'jumlah'         => 1,
    //                     'harga_satuan'   => $price,
    //                     'total_harga'    => $price,
    //                     'kode_tiket'     => $kode,
    //                     'status'         => 'Available',
    //                 ]);
    //             }
    //         }

    //         $params = [
    //             'transaction_details' => [
    //                 'order_id' => $orderId,
    //                 'gross_amount' => $grandTotal,
    //             ],
    //             'item_details' => $itemDetails,
    //             'customer_details' => [
    //                 'first_name' => $payload['nama_pembeli'],
    //                 'email' => $payload['email'],
    //                 'phone' => $payload['telepon'],
    //             ],
    //             'callbacks' => [
    //                 'finish' => route('checkout.callback')
    //             ]
    //         ];

    //         $snapToken = Snap::getSnapToken($params);

    //         DB::commit();

    //         return response()->json([
    //             'token' => $snapToken,
    //             'order_id' => $orderId,
    //             'amount' => $grandTotal
    //         ]);
    //     } catch (\Throwable $e) {
    //         DB::rollBack();
    //         Log::error('pay error: ' . $e->getMessage());
    //         return response()->json(['error' => true, 'message' => 'Gagal membuat transaksi.'], 500);
    //     }
    // }

    public function pay(Request $request)
    {
        $payload = $request->validate([
            'nama_pembeli' => 'required|string|max:150',
            'email' => 'required|email',
            'telepon' => 'required|string',
            'tickets' => 'required|array|min:1',
            'tickets.*.id' => 'required|integer|exists:tickets,id',
            'tickets.*.qty' => 'required|integer|min:1',
        ]);

        $ticketIds = array_unique(array_map(fn($t) => (int)$t['id'], $payload['tickets']));
        $tickets = Ticket::whereIn('id', $ticketIds)->get()->keyBy('id');

        $itemDetails = [];
        $subtotal = 0;

        DB::beginTransaction();
        try {
            // ✅ Cek dan reservasi stok
            foreach ($payload['tickets'] as $t) {
                $id = (int) $t['id'];
                $qty = (int) $t['qty'];

                if (!$tickets->has($id)) continue;

                $ticket = $tickets[$id];

                // Cek stok yang tersedia (stok asli - yang sedang direservasi)
                $availableStock = $ticket->stok_tiket - $ticket->stok_reserved;

                if ($availableStock < $qty) {
                    DB::rollBack();
                    return response()->json([
                        'error' => true,
                        'message' => "Stok untuk {$ticket->jenis_tiket} tidak mencukupi. Tersedia: {$availableStock}"
                    ], 422);
                }

                // ✅ RESERVASI stok (jangan kurangi stok asli dulu)
                $ticket->increment('stok_reserved', $qty);

                $price = (int) $ticket->harga_tiket;
                $lineTotal = $price * $qty;
                $subtotal += $lineTotal;

                $itemDetails[] = [
                    'id' => (string)$ticket->id,
                    'price' => $price,
                    'quantity' => $qty,
                    'name' => "{$ticket->nama_event} - {$ticket->jenis_tiket}"
                ];
            }

            if (empty($itemDetails)) {
                DB::rollBack();
                return response()->json(['error' => true, 'message' => 'Tidak ada tiket valid.'], 422);
            }

            $tax = (int) round($subtotal * 0.10);
            $platformFee = (int) round($subtotal * 0.05);
            $grandTotal = $subtotal + $tax + $platformFee;

            if ($tax > 0) {
                $itemDetails[] = [
                    'id' => 'tax',
                    'price' => $tax,
                    'quantity' => 1,
                    'name' => 'Tax 10%'
                ];
            }
            if ($platformFee > 0) {
                $itemDetails[] = [
                    'id' => 'platform_fee',
                    'price' => $platformFee,
                    'quantity' => 1,
                    'name' => 'Platform Fee'
                ];
            }

            $orderId = 'ORDER-' . strtoupper(Str::random(10));

            $transaksi = Transaksi::create([
                'nama_pembeli' => $request->nama_pembeli,
                'email' => $request->email,
                'nomer_telpon' => $request->telepon,
                'kode_transaksi' => $orderId,
                'total_harga' => $grandTotal,
                'status_payment' => 'pending',
                'expired_at' => now()->addMinutes(5), // ✅ Set expiry 5 menit
            ]);


            foreach ($payload['tickets'] as $t) {
                $ticket = $tickets[$t['id']];
                $price = (int) $ticket->harga_tiket;

                for ($u = 0; $u < $t['qty']; $u++) {
                    $kode = 'PTR-' . $transaksi->id . '-' . strtoupper(Str::random(6));

                    TransaksiDetail::create([
                        'transaksi_id'   => $transaksi->id,
                        'ticket_id'      => $ticket->id,
                        'jumlah'         => 1,
                        'harga_satuan'   => $price,
                        'total_harga'    => $price,
                        'kode_tiket'     => $kode,
                        'status'         => 'Reserved', // ✅ Status awal Reserved
                    ]);
                }
            }

            $params = [
                'transaction_details' => [
                    'order_id' => $orderId,
                    'gross_amount' => $grandTotal,
                ],
                'item_details' => $itemDetails,
                'customer_details' => [
                    'first_name' => $payload['nama_pembeli'],
                    'email' => $payload['email'],
                    'phone' => $payload['telepon'],
                ],
                'callbacks' => [
                    'finish' => route('checkout.callback')
                ],
                'expiry' => [
                    'start_time' => now()->format('Y-m-d H:i:s O'),
                    'unit' => 'minutes',
                    'duration' => 5
                ]
            ];

            $snapToken = Snap::getSnapToken($params);
            broadcast(new TransaksiCreated($transaksi))->toOthers();

            DB::commit();

            return response()->json([
                'token' => $snapToken,
                'order_id' => $orderId,
                'amount' => $grandTotal
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('pay error: ' . $e->getMessage());
            return response()->json(['error' => true, 'message' => 'Gagal membuat transaksi.'], 500);
        }
    }
    /**
     * Update status transaksi
     */
    // private function updateTransactionStatus($orderId, $status, $amount = null)
    // {
    //     $transaksi = Transaksi::where('kode_transaksi', $orderId)->first();

    //     if (!$transaksi) {
    //         Log::warning("Transaksi tidak ditemukan: {$orderId}");
    //         return;
    //     }

    //     $statusMap = [
    //         'pending' => 'pending',
    //         'settlement' => 'paid',
    //         'capture' => 'paid',
    //         'cancel' => 'cancelled',
    //         'deny' => 'cancelled',
    //         'failure' => 'cancelled',
    //         'expire' => 'cancelled',
    //     ];

    //     $newStatus = $statusMap[strtolower($status)] ?? $status;

    //     $previousStatus = $transaksi->status_payment;
    //     $transaksi->update(['status_payment' => $newStatus]);

    //     $transaksi->load('details');

    //     // === Kalau status paid, kurangi stok ===
    //     if ($newStatus === 'paid' && $previousStatus !== 'paid') {
    //         foreach ($transaksi->details as $detail) {
    //             $ticket = Ticket::find($detail->ticket_id);
    //             if ($ticket) {
    //                 $ticket->decrement('stok_tiket', $detail->jumlah);
    //             }

    //             TiketKode::create([
    //                 'transaksi_detail_id' => $detail->id,
    //                 'kode_tiket' => $detail->kode_tiket,
    //             ]);
    //         }

    //         try {
    //             $codes = $transaksi->details->pluck('kode_tiket');
    //             Mail::to($transaksi->email)->send(new TicketPaidMail($transaksi, $codes));
    //         } catch (\Exception $e) {
    //             Log::error("Gagal kirim email: " . $e->getMessage());
    //         }
    //     }

    //     // === Kalau status cancelled, restore stok ===
    //     if (in_array($newStatus, ['cancelled']) && $previousStatus !== 'cancelled') {
    //         foreach ($transaksi->details as $detail) {
    //             $ticket = Ticket::find($detail->ticket_id);
    //             if ($ticket) {
    //                 $ticket->increment('stok_tiket', $detail->jumlah);
    //             }
    //         }
    //         Log::info("Stok tiket dikembalikan karena transaksi {$orderId} gagal atau dibatalkan.");
    //     }

    //     Log::info("Status transaksi {$orderId} diperbarui ke {$newStatus}");
    // }

    // ==========================================
    // GANTI METHOD updateTransactionStatus()
    // ==========================================

    private function updateTransactionStatus($orderId, $status, $amount = null)
    {
        $transaksi = Transaksi::where('kode_transaksi', $orderId)->first();

        if (!$transaksi) {
            Log::warning("Transaksi tidak ditemukan: {$orderId}");
            return;
        }

        $statusMap = [
            'pending' => 'pending',
            'settlement' => 'paid',
            'capture' => 'paid',
            'cancel' => 'cancelled',
            'deny' => 'cancelled',
            'failure' => 'cancelled',
            'expire' => 'cancelled',
        ];

        $newStatus = $statusMap[strtolower($status)] ?? $status;
        $previousStatus = $transaksi->status_payment;

        $transaksi->update(['status_payment' => $newStatus]);
        $transaksi->load('details');

        // ✅ PAID: Konfirmasi reservasi → kurangi stok asli, lepas reserved
        if ($newStatus === 'paid' && $previousStatus !== 'paid') {
            $qtyPerTicket = $transaksi->details->groupBy('ticket_id')->map(function ($group) {
                return $group->count();
            });

            foreach ($qtyPerTicket as $ticketId => $qty) {
                $ticket = Ticket::find($ticketId);
                if ($ticket) {
                    // Kurangi stok asli
                    $ticket->decrement('stok_tiket', $qty);
                    // Lepas reservasi
                    $ticket->decrement('stok_reserved', $qty);
                }
            }

            // Update status detail & buat tiket kode
            foreach ($transaksi->details as $detail) {
                $detail->update(['status' => 'Available']);

                TiketKode::create([
                    'transaksi_detail_id' => $detail->id,
                    'kode_tiket' => $detail->kode_tiket,
                ]);
            }


            try {
                $codes = $transaksi->details->pluck('kode_tiket');
                Mail::to($transaksi->email)->send(new TicketPaidMail($transaksi, $codes));
            } catch (\Exception $e) {
                Log::error("Gagal kirim email: " . $e->getMessage());
            }

            Log::info("Transaksi {$orderId} PAID - Stok dikurangi & reservasi dilepas");
        }

        // ✅ CANCELLED/EXPIRED: Kembalikan stok reserved ke available
        if (in_array($newStatus, ['cancelled']) && $previousStatus !== 'cancelled') {
            $qtyPerTicket = $transaksi->details->groupBy('ticket_id')->map(function ($group) {
                return $group->count();
            });

            foreach ($qtyPerTicket as $ticketId => $qty) {
                $ticket = Ticket::find($ticketId);
                if ($ticket && $ticket->stok_reserved >= $qty) {
                    // Lepas reservasi (stok asli tidak berubah karena belum pernah dikurangi)
                    $ticket->decrement('stok_reserved', $qty);
                }
            }

            // Update status detail
            foreach ($transaksi->details as $detail) {
                $detail->update(['status' => 'Cancelled']);
            }

            Log::info("Transaksi {$orderId} CANCELLED - Reservasi dilepas, stok kembali tersedia");
        }
        broadcast(new TransaksiUpdated($transaksi))->toOthers();
        Log::info("Status transaksi {$orderId} diperbarui ke {$newStatus}");
    }

    // ==========================================
    // TAMBAHKAN METHOD BARU untuk Release Manual
    // ==========================================

    // public function releaseReservation(Request $request)
    // {
    //     $orderId = $request->input('order_id');

    //     if (!$orderId) {
    //         return response()->json(['error' => 'Order ID required'], 400);
    //     }

    //     $transaksi = Transaksi::where('kode_transaksi', $orderId)->first();

    //     if (!$transaksi) {
    //         return response()->json(['error' => 'Transaction not found'], 404);
    //     }

    //     // Hanya release jika masih pending
    //     if ($transaksi->status_payment === 'pending') {
    //         $transaksi->load('details');

    //         $qtyPerTicket = $transaksi->details->groupBy('ticket_id')->map(function($group) {
    //             return $group->count();
    //         });

    //         foreach ($qtyPerTicket as $ticketId => $qty) {
    //             $ticket = Ticket::find($ticketId);
    //             if ($ticket && $ticket->stok_reserved >= $qty) {
    //                 $ticket->decrement('stok_reserved', $qty);
    //             }
    //         }

    //         $transaksi->update(['status_payment' => 'cancelled']);

    //         foreach ($transaksi->details as $detail) {
    //             $detail->update(['status' => 'Cancelled']);
    //         }

    //         Log::info("Reservasi dilepas manual untuk transaksi {$orderId}");

    //         return response()->json(['success' => true, 'message' => 'Reservation released']);
    //     }

    //     return response()->json(['success' => false, 'message' => 'Transaction already processed']);
    // }

    public function releaseReservation(Request $request)
    {
        $orderId = $request->input('order_id');

        if (!$orderId) {
            return response()->json(['error' => 'Order ID required'], 400);
        }

        $transaksi = Transaksi::where('kode_transaksi', $orderId)->first();

        if (!$transaksi) {
            return response()->json(['error' => 'Transaction not found'], 404);
        }

        // Hanya release jika masih pending
        if ($transaksi->status_payment === 'pending') {
            $transaksi->load('details');

            $qtyPerTicket = $transaksi->details->groupBy('ticket_id')->map(fn($g) => $g->count());

            foreach ($qtyPerTicket as $ticketId => $qty) {
                $ticket = Ticket::find($ticketId);

                if ($ticket && $ticket->stok_reserved >= $qty) {
                    $ticket->decrement('stok_reserved', $qty);
                }
            }

            // Tandai detail cancelled
            $transaksi->details()->update(['status' => 'Cancelled']);

            $transaksi->update([
                'status_payment' => 'cancelled'
            ]);

            return response()->json([
                'success' => true,
                'message' => "Reservasi untuk {$orderId} berhasil dilepas"
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => "Tidak bisa release, status saat ini: {$transaksi->status_payment}"
        ]);
    }

    public function callback(Request $request)
    {
        $orderId = $request->input('order_id');
        if (!$orderId) {
            return redirect()->route('home')->with('error', 'Order ID tidak ditemukan.');
        }

        try {
            Log::info("ini Callback");
            $status = Transaction::status($orderId);
            $this->updateTransactionStatus($orderId, $status->transaction_status, $status->gross_amount);

            if (in_array($status->transaction_status, ['capture', 'settlement'])) {
                return redirect()->route('checkout.success', ['order_id' => $orderId]);
            } else {
                return redirect()->route('checkout.failed', ['order_id' => $orderId]);
            }
        } catch (\Exception $e) {
            Log::error('Callback error: ' . $e->getMessage());
            return redirect()->route('home')->with('error', 'Terjadi kesalahan saat memproses pembayaran.');
        }
    }

    // public function callback(Request $request)
    // {
    //     Log::info('Callback data:', $request->all());

    //     $orderId = $request->input('order_id');

    //     if (!$orderId) {
    //         Log::warning('Callback tanpa order_id!');
    //         return response()->json(['error' => 'order_id kosong'], 400);
    //     }

    //     try {
    //         // Ambil status transaksi dari Midtrans
    //         $status = Transaction::status($orderId);
    //         Log::info('Midtrans status:', (array)$status);

    //         // Update status transaksi di database
    //         $this->updateTransactionStatus($orderId, $status->transaction_status, $status->gross_amount);

    //         return response()->json(['success' => true]);
    //     } catch (\Exception $e) {
    //         Log::error('Midtrans API Error: ' . $e->getMessage());
    //         return response()->json(['error' => $e->getMessage()], 400);
    //     }
    // }

    public function success(Request $request)
    {
        Log::info('Success page accessed', $request->all());
        $orderId = $request->query('order_id');
        $transaksi = Transaksi::where('kode_transaksi', $orderId)->first();

        broadcast(new TransaksiUpdated($transaksi))->toOthers();

        if (!$transaksi) {
            return redirect()->route('home')->with('error', 'Transaksi tidak ditemukan.');
        }

        return view('checkout.success', compact('transaksi'));
    }

    public function failed(Request $request)
    {
        Log::info('Failed page accessed', $request->all());
        $orderId = $request->get('order_id');
        $transaksi = Transaksi::where('kode_transaksi', $orderId)->first();

        broadcast(new TransaksiUpdated($transaksi))->toOthers();


        return view('checkout.failed', compact('transaksi', 'orderId'));
    }

    public function pending(Request $request)
    {
        $order_id = $request->query('order_id');

        // Debug log
        Log::info('Pending page accessed', ['order_id' => $order_id]);

        // Ambil data transaksi jika diperlukan
        $transaksi = Transaksi::where('kode_transaksi', $order_id)->first();

        return view('checkout.pending', [
            'order_id' => $order_id,
            'transaksi' => $transaksi
        ]);
    }
}
