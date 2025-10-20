<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TicketPaidMail extends Mailable
{
    use Queueable, SerializesModels;

    public $transaksi;
    public $codes; // ✅ Tambahkan ini

    public function __construct($transaksi, $codes)
    {
        $this->transaksi = $transaksi;
        $this->codes = $codes; // ✅ Simpan ke property
    }

    public function build()
    {
        return $this->subject('Tiket Konser Anda Telah Dibayar')
            ->view('emails.ticket_paid')
            ->with([
                'transaksi' => $this->transaksi,
                'codes' => $this->codes, // ✅ Kirim ke view
            ]);
    }
}
