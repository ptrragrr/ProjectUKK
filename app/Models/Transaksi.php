<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    protected $table = 'transaksis';

    protected $fillable = [
        'nama_pembeli', 'email', 'nomer_telpon',
        'kode_transaksi', 'status_payment', 'total_harga'
    ];

    public function user()
{
    return $this->belongsTo(User::class, 'user_id');
}

    public function details()
    {
        return $this->hasMany(TransaksiDetail::class, 'transaksi_id', 'id');
    }
}

