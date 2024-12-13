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
        Schema::create('makul_class', function (Blueprint $table) {
            $table->id();
            $table->string('kode_masa_input')->unique(); // Make this column unique
            $table->foreign('kode_masa_input')
                ->references('kode_masa_input')
                ->on('masa_inputs')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->longText('data_kelas');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('makul_class');
    }
};
