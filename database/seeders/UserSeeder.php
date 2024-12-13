<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Admin',
            'email' => 'testadmin@gmail.com',
            'password' => bcrypt('123123123'),
            'role' => 'admin',
        ]);
        User::create([
            'name' => 'Husni Ramadhan',
            'email' => 'test@gmail.com',
            'password' => bcrypt('123123123'),
            'role' => 'member',
        ]);
        User::create([
            'name' => 'Test Mahasiswa',
            'email' => 'test2@gmail.com',
            'password' => bcrypt('123123123'),
            'role' => 'member',
        ]);
    }
}
