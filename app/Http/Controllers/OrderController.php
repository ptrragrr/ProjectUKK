<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Order;

class OrderController extends Controller
{
     public function index(Request $request)
    {
        $per = $request->per ?? 10;
        $page = $request->page ? $request->page - 1 : 0;

        DB::statement('set @no=0+' . $page * $per);
        $data = Order::when($request->search, function (Builder $query, string $search) {
            $query->where('name', 'like', "%$search%")
                ->orWhere('email', 'like', "%$search%")
                ->orWhere('phone', 'like', "%$search%");
        })->latest()->paginate($per, ['*', DB::raw('@no := @no + 1 AS no')]);

        return response()->json($data);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'tiket_id' => 'required|exists:tikets,id',
            'jumlah' => 'required|integer',
            'total' => 'required|numeric',
            'status' => 'required|in:pending,lunas'
        ]);

        $order = OrderTiket::create($validated);
        return response()->json($order, 201);
    }

    public function show($id)
    {
        $order = OrderTiket::with(['user', 'tiket'])->findOrFail($id);
        return response()->json($order);
    }
}
