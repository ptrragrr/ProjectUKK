<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\InteractsWithSockets;

class TransaksiDeleted implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

    public $id; // hanya kirim ID untuk delete

    public function __construct($id)
    {
        $this->id = $id;
    }

    public function broadcastOn()
    {
        return new Channel('transaksi');
    }
}
