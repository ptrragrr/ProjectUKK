<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Konser extends Model
{
    protected $table = 'konser';

    protected $fillable = [
        'nama_konser', 'lokasi', 'tanggal', 'deskripsi', 'banner',
    ];

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    // app/Models/Konser.php

public function getBannerAttribute($value)
{
    return $value ? asset('storage/' . $value) : null;
}

}
