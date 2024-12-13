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
        Schema::create('jadwal_file', function (Blueprint $table) {
            $table->id();
            $table->string('kode_masa_input');
            $table->foreign('kode_masa_input')->references('kode_masa_input')->on('masa_inputs')->onDelete('cascade');
            $table->string('data_hari'); // Hari spesifik (misal: Senin, Selasa, dst.)
            $table->json('data_jadwal_per_hari'); // Data jadwal per hari
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jadwal_file');
    }
};
