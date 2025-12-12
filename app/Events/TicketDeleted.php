<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TicketDeleted implements ShouldBroadcast
{
    use SerializesModels;

    public $id;

    public function __construct($ticketId)
    {
        $this->id = $ticketId;
    }

    public function broadcastOn()
    {
        return new Channel('tickets');
    }

    public function broadcastAs()
    {
        return 'ticket.deleted';
    }

    public function broadcastWith()
    {
        return [
            'id' => $this->id
        ];
    }
}

