<?php

namespace App\Http\Controllers\users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        return view('user.orders.index');
    }

    public function store(Request $request)
    {
        // proses simpan data pesanan
    }
}
