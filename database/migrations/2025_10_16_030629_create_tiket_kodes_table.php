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
       Schema::create('tiket_kodes', function (Blueprint $table) {
    $table->id();
    $table->foreignId('transaksi_detail_id')->constrained()->onDelete('cascade');
    $table->string('kode_tiket')->unique();
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tiket_kodes');
    }
};
