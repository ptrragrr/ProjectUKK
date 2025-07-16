<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('transaksi_details', function (Blueprint $table) {
        $table->id();
        $table->foreignId('transaksi_id')->constrained('transaksi')->onDelete('cascade');
        $table->foreignId('ticket_id')->constrained('tickets')->onDelete('cascade');
        $table->integer('jumlah');
        $table->decimal('harga_satuan', 10, 2);
        $table->decimal('total_harga', 10, 2);
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksi_details');
    }
};
