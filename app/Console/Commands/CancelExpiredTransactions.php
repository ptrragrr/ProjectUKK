<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Transaksi;

class CancelExpiredTransactions extends Command
{
    protected $signature = 'transactions:cancel-expired';
    protected $description = 'Membatalkan transaksi pending yang sudah lebih dari 5 menit';

    public function handle()
    {
        $count = Transaksi::where('status_payment', 'pending')
            ->where('created_at', '<=', now()->subMinutes(1))
            ->update([
                'status_payment' => 'cancelled',
            ]);

        $this->info("Berhasil membatalkan {$count} transaksi yang pending lebih dari 5 menit.");
    }
}
