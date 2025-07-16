<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    protected $table = 'transaksi';
    protected $fillable = [
        'user_id', 'kode_transaksi', 'metode_pembayaran', 'total_harga', 'bayar', 'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function details()
    {
        return $this->hasMany(TransaksiDetail::class);
    }

    public function tickets()
    {
        return $this->belongsToMany(Ticket::class, 'ticket_transaksi')
            ->withPivot('jumlah')
            ->withTimestamps();
    }
}