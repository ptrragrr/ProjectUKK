<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transaksi', function (Blueprint $table) {
    $table->id('id_transaksi');
    $table->unsignedBigInteger('id_pelanggan'); // <-- TAMBAHKAN KOLOMNYA DULU
    $table->foreign('id_pelanggan')->references('id_pelanggan')->on('pelanggan')->onDelete('cascade');
    
    $table->date('tanggal');
    $table->decimal('total_harga', 10, 2)->default(0);
    $table->timestamps();
});

        // Schema::create('transaksi', function (Blueprint $table) {
        //     $table->id('id_transaksi');
        //     $table->foreign('id_pelanggan')->references('id_pelanggan')->on('pelanggan')->onDelete('cascade');
        //     // $table->foreignId('id_pelanggan')->constrained('pelanggan')->onDelete('cascade');
        //     $table->date('tanggal');
        //     $table->decimal('total_harga', 10, 2)->default(0);
        //     $table->timestamps();
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksi');
    }
};
