<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisTiket extends Model
{
    use HasFactory;

    protected $table = 'jenis_tiket';
    protected $fillable = ['jenis_tiket', 'harga'];

    public function tickets()
{
    return $this->hasMany(Ticket::class, 'jenis_tiket_id');
}
}
