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
         Schema::create('pelanggan', function (Blueprint $table) {
            $table->id('id_pelanggan'); // Custom nama kolom ID
            $table->string('nama', 100);
            $table->string('email')->unique();
            $table->string('no_telp', 20)->nullable();
            $table->string('alamat')->nullable();
            $table->string('password'); // Jika login diperlukan
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pelanggan');
    }
};
