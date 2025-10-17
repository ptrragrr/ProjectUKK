<?php

namespace App\Models\pengguna;

use App\Models\TransaksiDetail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TiketKode extends Model
{
    use HasFactory;

    protected $fillable = ['transaksi_detail_id', 'kode_tiket'];

    public function detail()
    {
        return $this->belongsTo(TransaksiDetail::class);
    }
}

