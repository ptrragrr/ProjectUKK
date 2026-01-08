<?php

namespace App\Models;

use App\Models\Ticket;
use App\Models\Transaksi;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransaksiDetail extends Model
{
    use HasFactory;

    // Nama tabel di database
    protected $table = 'transaksi_details';

    // Kolom yang boleh diisi secara mass-assignment
    // protected $fillable = [
    //     'transaksi_id',
    //     'ticket_id',
    //     'jumlah',
    //     'harga_satuan',
    //     'total_harga',
    //     'kode_tiket',
    // ];

      protected $fillable = [
        'transaksi_id',
        'ticket_id',
        'jumlah',
        'harga_satuan',
        'total_harga',
        'kode_tiket',
        'status',
    ];
    
    /**
     * Relasi ke tabel transaksi
     * Satu detail transaksi milik satu transaksi utama
     */
    public function transaksi()
    {
        return $this->belongsTo(Transaksi::class, 'transaksi_id');
    }

    /**
     * Relasi ke tabel ticket
     * Satu detail transaksi memiliki satu ticket
     */
    public function ticket()
    {
        return $this->belongsTo(Ticket::class, 'ticket_id');
    }

    /**
     * (Opsional) Relasi ke kode tiket jika kamu punya tabel tiket_kodes
     */
    public function tiketKodes()
{
    return $this->hasMany(\App\Models\pengguna\TiketKode::class, 'transaksi_detail_id');
}
}
