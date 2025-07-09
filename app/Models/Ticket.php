<?php
// app/Models/Ticket.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;
    
    protected $table = 'tickets'; // pastikan nama tabel benar
   protected $fillable = [
    'nama_destinasi',
    'harga_tiket',
    'stok_tiket',
    // 'foto_destinasi',
];
}
