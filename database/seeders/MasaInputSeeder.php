<?php

namespace Database\Seeders;

use App\Models\MasaInput;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class MasaInputSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // MasaInput::create([
        //     'tahun_ajaran' => '2024/2025',
        //     'semester' => '0',
        //     'jangka_waktu' => '26/06/2024 - 31/08/2024',
        //     'keterangan' => 'Seeder Example',
        //     'kode_masa_input' => 'TA024025ODD'
        // ]);

        $startYear = 2024;
        $endYear = $startYear + 6;

        for ($year = $startYear; $year <= $endYear; $year++) {
            $nextYear = $year + 1;
            // $currentYearString = substr($year, 2);
            // $nextYearString = substr($nextYear, 2);
            $currentYearString = sprintf('%02d', $year % 100);
            $nextYearString = sprintf('%02d', $nextYear % 100);

            // GANJIL Semester (Odd)
            MasaInput::create([
                'tahun_ajaran' => "$year/$nextYear",
                'semester' => '0', // GANJIL
                'jangka_waktu' => "01/08/$year - 30/11/$year",
                'keterangan' => 'Seeder Example GANJIL',
                'kode_masa_input' => "TA" . "0" . $currentYearString . "0" . $nextYearString . "ODD"
            ]);

            // GENAP Semester (Even)
            MasaInput::create([
                'tahun_ajaran' => "$year/$nextYear",
                'semester' => '1', // GENAP
                'jangka_waktu' => "01/02/$nextYear - 31/05/$nextYear",
                'keterangan' => 'Seeder Example GENAP',
                'kode_masa_input' => "TA" . "0" . $currentYearString . "0" . $nextYearString . "EVEN"
            ]);
        }
    }
}
