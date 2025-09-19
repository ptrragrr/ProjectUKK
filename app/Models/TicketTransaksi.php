<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketTransaksi extends Model
{
    protected $table = 'ticket_transaksi';

    protected $fillable = [
        'transaksi_id',
        'ticket_id',
        'jumlah',
    ];

    public function transaksi()
    {
        return $this->belongsTo(Transaksi::class);
    }

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }
}
