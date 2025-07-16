<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $fillable = [
        'konser_id',
        'jenis_tiket',
        'harga_tiket',
        'stok_tiket',
    ];

    // Relasi ke konser
    public function konser()
    {
        return $this->belongsTo(Konser::class);
    }

    // Relasi ke transaksi detail
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
}
