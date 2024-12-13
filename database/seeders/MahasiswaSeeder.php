<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Mahasiswa;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MahasiswaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Mahasiswa::create([
            'name' => 'Husni Ramadhan',
            'NIM' => 'D1041181024',
            // 'semester' => 11,
            'angkatan' => 2018,
            'user_id' => 2,
        ]);
        Mahasiswa::create([
            'name' => 'Test Mahasiswa',
            'NIM' => 'D1041181025',
            // 'semester' => 1,
            'angkatan' => 2024,
            'user_id' => 3,
        ]);
    }
}
