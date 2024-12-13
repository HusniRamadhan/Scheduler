<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Classroom;

class ClassroomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $classrooms = [
            ['ruang_kelas' => 'D1', 'jenis_kelas' => false],
            ['ruang_kelas' => 'D2', 'jenis_kelas' => false],
            ['ruang_kelas' => 'D3', 'jenis_kelas' => false],
            ['ruang_kelas' => 'D4', 'jenis_kelas' => false],
            ['ruang_kelas' => 'D5', 'jenis_kelas' => true],
            ['ruang_kelas' => 'D6', 'jenis_kelas' => true],
            ['ruang_kelas' => 'D7', 'jenis_kelas' => true],
        ];

        Classroom::insert($classrooms);
    }
}
