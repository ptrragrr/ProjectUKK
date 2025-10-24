<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TicketPaidMail extends Mailable
{
    use Queueable, SerializesModels;

    public $transaksi;
    public $codes;
    public $eventName;  // ✅ Tambahkan ini

    public function __construct($transaksi, $codes, $eventName)
    {
        $this->transaksi = $transaksi;
        $this->codes = $codes;
        $this->eventName = $eventName; // ✅ Simpan nama event
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