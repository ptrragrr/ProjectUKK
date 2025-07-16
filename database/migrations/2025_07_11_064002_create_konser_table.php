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
    Schema::create('konser', function (Blueprint $table) {
        $table->id();
        $table->string('nama_konser');
        $table->string('lokasi');
        $table->dateTime('tanggal');
        $table->text('deskripsi')->nullable();
        $table->string('banner')->nullable(); // path file/banner
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('konser');
    }
};
