<?php

namespace App\Events;

use App\Models\Ticket; // <-- WAJIB BENAR
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class TicketUpdated implements ShouldBroadcast
{
    use SerializesModels;

    public $ticket;

    public function __construct(Ticket $ticket) // <-- TERIMA MODEL Ticket
    {
        Log::info('TicketUpdated event triggered for Ticket ID: ' . $ticket->id);
        $this->ticket = $ticket;
    }

    public function broadcastOn()
    {
        return new Channel('tickets');
    }

    public function broadcastAs()
    {
        return 'ticket.updated';
    }

    public function broadcastWith()
    {
        return [
            'ticket' => $this->ticket
        ];
    }
}
