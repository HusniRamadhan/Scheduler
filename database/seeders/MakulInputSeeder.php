<?php

namespace Database\Seeders;

use App\Models\MakulInput;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MakulInputSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        MakulInput::create([
            'semester' => '13',
            'kode_masa_input' => 'TA024025ODD',
            'makul_input' => '[{"urutan":1,"kode":"INF-55201-407","sks":"4"},{"urutan":2,"kode":"INF-55201-540","sks":"3"}]',
            'user_id' => '2',
            'NIM' => 'D1041181024',
        ]);
    }
}
