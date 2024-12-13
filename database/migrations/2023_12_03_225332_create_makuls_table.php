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
        Schema::create('makuls', function (Blueprint $table) {
            $table->id();
            $table->string('mata_kuliah');
            $table->string('kode')->unique();
            $table->integer('sks');
            $table->integer('semester');
            //$table->string('prasyarat')->nullable();
            //$table->integer('tahun_ajaran'); //Gasal = 1, Genap = 2, Pilihan = 3
            //$table->boolean('IsIntitusional')->default(false); //Inti = false, Institusional = true
            //$table->string('kelompok');
            // $table->boolean('jenis_makul')->default(false); //Prodi= 0, Umum = 1
            // $table->boolean('IsAtas')->default(false);
            $table->boolean('IsPilihan')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('makuls');
    }
};

