<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detail_transaksi', function (Blueprint $table) {
            $table->id('id_detail');
            $table->unsignedBigInteger('id_transaksi');
            $table->unsignedBigInteger('id_ticket');
            $table->integer('jumlah');
            $table->decimal('subtotal', 10, 2);
            $table->timestamps();

            // Foreign Key
            // $table->foreign('id_transaksi')->references('id_transaksi')->on('transaksi')->onDelete('cascade');
            // $table->foreign('id_ticket')->references('id')->on('tickets')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_transaksi');
    }
};
