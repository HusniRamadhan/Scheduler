<?php

namespace Database\Seeders;

use App\Models\MahasiswaSemester;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MahasiswaSemesterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        MahasiswaSemester::create([
            'name' => 'Husni Ramadhan',
            'NIM' => 'D1041181024',
            'semester' => '11',
        ]);
        MahasiswaSemester::create([
            'name' => 'Test Mahasiswa',
            'NIM' => 'D1041181025',
            'semester' => '1',
        ]);
    }
}
