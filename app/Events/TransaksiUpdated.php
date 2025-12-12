<?php

namespace App\Events;

use App\Models\Transaksi;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\InteractsWithSockets;

class TransaksiUpdated implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

    public $transaksi;

    public function __construct(Transaksi $transaksi)
    {
        $this->transaksi = $transaksi;
    }

    public function broadcastOn()
    {
        return new Channel('transaksis');
    }

    public function broadcastAs()
    {
        return 'transaksi.updated';
    }

    public function broadcastWith()
    {
        return [
            'transaksi' => $this->transaksi
        ];
    }
}

// class TransaksiUpdated implements ShouldBroadcast
// {
//     use InteractsWithSockets, SerializesModels;

//     public $transaksi;

//     public function __construct()
//     {
//     }

//     public function broadcastOn()
//     {
//         return new Channel('transaksis');
//     }

//     public function broadcastAs()
//     {
//         return 'transaksi.updated';
//     }

//     public function broadcastWith()
//     {
//         return [
//             'transaksi' => $this->transaksi
//         ];
//     }
// }
