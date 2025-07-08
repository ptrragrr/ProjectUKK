<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Management extends Model
{
    protected $table = 'tickets';
    protected $fillable = ['nama_destinasi', 'harga_tiket', 'foto_destinasi', 'stok_tiket'];
}
