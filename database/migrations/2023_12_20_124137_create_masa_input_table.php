<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMasaInputTable extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up(): void
    {
        Schema::create('masa_inputs', function (Blueprint $table) {
            $table->id();
            $table->string('tahun_ajaran'); // String for academic year (e.g., "2023/2024")
            $table->boolean('semester')->default(false); // Boolean for semester (false = ganjil, true = genap)

            // You can choose to use two separate columns for start and end dates or a single column for the date range
            // Uncomment one of the following options based on your preference:

            // Option 1: Using two separate columns for start and end dates
            // $table->date('jangka_waktu_start'); // Date for the start date of the input period
            // $table->date('jangka_waktu_end');   // Date for the end date of the input period

            // Option 2: Using a single column for the date range
            $table->string('jangka_waktu'); // String for the date range (e.g., "01/01/2023 - 01/15/2023")
            $table->string('keterangan')->nullable(); // String for additional information
            $table->string('kode_masa_input')->unique();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('masa_inputs');
    }
};
