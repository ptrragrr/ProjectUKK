<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $fillable = [
        'nama_event',
        'tanggal',
        'jenis_tiket',
        'harga_tiket',
        'stok_tiket',
        'stok_reserved',
        'deskripsi',
    ];

    public function transaksiDetails()
    {
        return $this->hasMany(TransaksiDetail::class);
    }

    public function transaksi()
    {
        return $this->belongsToMany(Transaksi::class, 'ticket_transaksi')
            ->withPivot('jumlah')
            ->withTimestamps();
    }

    public function jenisTiket()
{
    return $this->belongsTo(JenisTiket::class, 'jenis_tiket_id');
}
}
