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
        Schema::create('dosen_makul', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->unsignedBigInteger('dosen_id'); // Foreign key to dosens
            $table->unsignedBigInteger('makul_id'); // Foreign key to makuls
            $table->timestamps();

            // Define foreign key constraints
            $table->foreign('dosen_id')->references('id')->on('dosens')->onDelete('cascade');
            $table->foreign('makul_id')->references('id')->on('makuls')->onDelete('cascade');

            // Optional: Ensure unique combinations of dosen_id and makul_id
            // $table->unique(['dosen_id', 'makul_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dosen_makul');
    }
};
