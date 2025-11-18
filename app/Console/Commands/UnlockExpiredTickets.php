<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Ticket;

class UnlockExpiredTickets extends Command
{
    protected $signature = 'tickets:unlock';
    protected $description = 'Unlock tiket yang dikunci lebih dari 5 menit lalu';

    public function handle()
    {
        $count = Ticket::where('is_locked', true)
            ->where('locked_at', '<', now()->subMinutes(5))
            ->update([
                'is_locked' => false,
                'locked_at' => null,
            ]);

        $this->info("Berhasil membuka kunci {$count} tiket yang kedaluwarsa.");
    }
}
