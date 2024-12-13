<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('makul_aktif', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->string('kode_masa_input'); // Foreign key to masa_inputs table
            $table->string('kode_makul'); // Foreign key to makuls table
            $table->boolean('status_aktif')->default(false); // Active status

            // Foreign key constraints
            $table->foreign('kode_masa_input')
                ->references('kode_masa_input')
                ->on('masa_inputs')
                ->onDelete('cascade'); // Delete if related masa_input is deleted

            $table->foreign('kode_makul')
                ->references('kode')
                ->on('makuls')
                ->onDelete('cascade'); // Delete if related makul is deleted

            $table->timestamps(); // Created at, Updated at
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('makul_aktif');
    }
};
