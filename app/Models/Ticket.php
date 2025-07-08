<?php
// app/Models/Ticket.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $table = 'tickets'; // pastikan nama tabel benar
   protected $fillable = [
    'nama_destinasi',
    'harga',
    'stok_tiket',
    'foto_destinasi',
];
}
