<?php

namespace App\Events;

use App\Models\Ticket;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Support\Facades\Log;

class TicketAdded implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

    public $ticket;

    public function __construct(Ticket $ticket)
    {
        Log::info('Broadcasting TicketAdded event for ticket ID: ' . $ticket);
        $this->ticket = $ticket;
    }

    public function broadcastOn()
    {
        return new Channel('tickets');
    }

    public function broadcastAs()
    {
        return 'ticket.added';
    }
}

// class TicketAdded implements ShouldBroadcast
// {
//     public $ticket;

//     public function __construct($ticket)
//     {
//         $this->ticket = $ticket;
//     }

//     public function broadcastOn()
//     {
//         return new Channel('tickets');
//     }
// }

