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
        Schema::create('makul_inputs', function (Blueprint $table) {
            $table->id();
            $table->integer('semester');
            // $table->string('tahun_ajaran'); //kode_masa_input
            $table->string('kode_masa_input'); // Reference to kode_masa_input in masa_inputs
            $table->text('makul_input');
            $table->unsignedBigInteger('user_id');
            $table->string('NIM');
            $table->timestamps();

            // Foreign keys
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('NIM')->references('NIM')->on('mahasiswas')->onDelete('cascade');
            $table->foreign('kode_masa_input')->references('kode_masa_input')->on('masa_inputs')->onDelete('cascade'); // Reference to masa_inputs
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('makul_input');
    }
};
