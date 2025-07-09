<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderTiket extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'tiket_id',
        'jumlah',
        'total',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tiket()
    {
        return $this->belongsTo(Tiket::class);
    }
}
