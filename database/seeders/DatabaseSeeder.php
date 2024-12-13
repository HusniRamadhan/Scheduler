<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Fixed test data for web testing
        $this->call(UserSeeder::class);
        $this->call(MakulSeeder::class);
        $this->call(DosenSeeder::class);
        $this->call(MasaInputSeeder::class);
        $this->call(MahasiswaSeeder::class); // Always below UserSeeder
        $this->call(MakulInputSeeder::class);
        $this->call(ClassroomSeeder::class);

        // Faker-generated dynamic student data
        $this->call(UserMakulInputFaker::class); // Adds the necessary student data
    }
}
