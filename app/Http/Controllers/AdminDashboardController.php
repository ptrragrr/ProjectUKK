<?php
namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Transaksi;

class AdminDashboardController extends Controller
{
    public function index()
    {
        return response()->json([
            'total_users' => User::count(),
            'total_transaksi' => Transaksi::count(),
            'total_omset' => Transaksi::sum('total_harga'), // sesuaikan field total harga
        ]);
    }
}
