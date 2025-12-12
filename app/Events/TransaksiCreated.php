<?php

namespace App\Events;

use App\Models\Transaksi;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Support\Facades\Log;

class TransaksiCreated implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

    public $transaksi;

    public function __construct(Transaksi $transaksi)
    {
        Log::info("Transakssi Created");
        $this->transaksi = $transaksi;
    }

    public function broadcastOn()
    {
        return new Channel('transaksis');
    }

    public function broadcastAs(){
        return 'transaksi.created';
    }
}
