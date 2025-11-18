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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();

            // Informasi event langsung disimpan di tabel tickets
            $table->string('nama_event');
            $table->date('tanggal');

            // Detail tiket
            $table->string('jenis_tiket'); // VIP, Reguler, dll
            $table->decimal('harga_tiket', 15, 2);
            $table->integer('stok_tiket')->default(0);
            $table->integer('stok_reserved')->default(0);

            // Tambahan deskripsi atau line-up event
            $table->text('deskripsi')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
