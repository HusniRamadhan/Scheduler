<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Mahasiswa;
use App\Models\MakulInput;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use Faker\Factory as Faker;

class UserMakulInputFaker extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        // Generate between 300-400 users
        $userCount = rand(300, 400);

        // Load semester data from config
        $semesters = config('semester.semesters');

        // Current academic year (TA024025)
        $currentYear = 2024;

        // Loop through and generate users
        for ($i = 0; $i < $userCount; $i++) {
            // Create User
            $user = User::create([
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'password' => bcrypt('password'), // Default password
                'role' => 'member', // Default role is member
            ]);

            // Generate Mahasiswa data
            $angkatan = rand(2018, 2024); // Random angkatan from 2018 to 2024
            $nim = $this->generateUniqueNim();
            $mahasiswa = Mahasiswa::create([
                'name' => $user->name,
                'NIM' => $nim,
                'angkatan' => $angkatan,
                'user_id' => $user->id,
            ]);

            // Generate inputs for both TA024025ODD and TA024025EVEN
            $this->generateMakulInput($user->id, $nim, $angkatan, $semesters, $currentYear, 'ODD');
            $this->generateMakulInput($user->id, $nim, $angkatan, $semesters, $currentYear, 'EVEN');
        }
    }

    // Helper method to generate a unique NIM
    private function generateUniqueNim()
    {
        $nim = 'D104' . rand(10000, 99999);
        while (Mahasiswa::where('NIM', $nim)->exists()) {
            $nim = 'D104' . rand(10000, 99999); // Regenerate NIM if duplicate found
        }
        return $nim;
    }

    // Helper method to generate MakulInput based on the user's semester and priorities
    // Helper method to generate MakulInput based on the user's semester and priorities
    private function generateMakulInput($userId, $nim, $angkatan, $semesters, $currentYear, $oddOrEven)
    {
        // Calculate the semester based on angkatan and whether it's ODD or EVEN
        $yearDifference = $currentYear - $angkatan;
        $semester = $oddOrEven === 'ODD' ? ($yearDifference * 2) + 1 : ($yearDifference * 2) + 2;

        // Ensure semester does not exceed 14
        if ($semester > 14) {
            $semester = 14;
        }

        // Set tahun_ajaran based on whether semester is ODD or EVEN
        $tahunAjaran = 'TA024025' . $oddOrEven;

        // Generate MakulInput data according to priorities
        $sksTotal = 0;
        $makulInput = [];

        // Priority logic based on the current semester
        $priorityCourses = $this->getPriorityCourses($semesters, $semester, $oddOrEven);

        // Debugging output: Check what courses are being returned
        if (empty($priorityCourses)) {
            // Log the semester and oddOrEven value if no courses are found
            Log::debug('No courses found for semester: ' . $semester . ', odd/even: ' . $oddOrEven);
        } else {
            Log::debug('Courses found for semester ' . $semester . ': ' . json_encode($priorityCourses));
        }

        // For Semester 8 and above: Prioritize PMKM, Kerja Praktek, TA 1, TA 2, keep SKS <= 10 unless more are needed
        $maxSks = $semester >= 8 ? 10 : 24;

        // Randomly select courses based on priority while keeping SKS <= max SKS
        foreach ($priorityCourses as $courses) {
            foreach ($courses as $index => $course) {
                // Check if 'sks' exists before using it
                if (!isset($course['sks'])) {
                    Log::debug('Missing sks for course: ' . json_encode($course)); // Log missing SKS
                    continue; // Skip courses without 'sks'
                }

                $sks = $course['sks'];
                if ($sksTotal + $sks > $maxSks) {
                    break;
                }
                $makulInput[] = [
                    'urutan' => count($makulInput) + 1,
                    'kode' => $course['kode'],
                    'sks' => $sks,
                ];
                $sksTotal += $sks;
            }
        }

        // Debugging output: Check the final makulInput array
        Log::debug('MakulInput for semester ' . $semester . ': ' . json_encode($makulInput));

        // Insert into MakulInput table
        MakulInput::create([
            'semester' => $semester,
            'kode_masa_input' => $tahunAjaran,
            'makul_input' => json_encode($makulInput),
            'user_id' => $userId,
            'NIM' => $nim,
        ]);
    }

    // Helper method to retrieve courses based on priority logic
    private function getPriorityCourses($semesters, $semester, $oddOrEven)
    {
        $courses = [];

        // Fetch courses based on semester
        if ($semester == 1) {
            $courses[] = $this->combineCourses($semesters['1'] ?? []); // Semester 1 only
        } elseif ($semester == 2) {
            $courses[] = $this->combineCourses($semesters['2'] ?? []); // Semester 2 only
        } else {
            // Add current semester if exists and is <= 8 (no regular courses beyond Semester 8)
            if ($semester <= 8 && isset($semesters[$semester])) {
                $courses[] = $this->combineCourses($semesters[$semester]);
            }

            // Add next higher odd/even semester if exists and is <= 8
            $nextSemester = $oddOrEven === 'ODD' ? $semester + 2 : $semester + 2;
            if ($nextSemester <= 8 && isset($semesters[$nextSemester])) {
                $courses[] = $this->combineCourses($semesters[$nextSemester]);
            }

            // For semesters 8 and higher, prioritize PMKM, Kerja Praktek, TA 1, TA 2
            if ($semester >= 8) {
                if (isset($semesters['PMKM'])) {
                    $courses[] = $this->combineCourses($semesters['PMKM']);
                }
                if (isset($semesters['KerjaPraktek'])) {
                    $courses[] = $this->combineCourses($semesters['KerjaPraktek']);
                }
                if (isset($semesters['TA1'])) {
                    $courses[] = $this->combineCourses($semesters['TA1']);
                }
                if (isset($semesters['TA2']) && $semester >= 9) {
                    $courses[] = $this->combineCourses($semesters['TA2']);
                }
            }

            // Add additional courses for semester 5 and higher
            if ($semester >= 5 && isset($semesters['6_additional'])) {
                $courses[] = $this->combineCourses($semesters['6_additional']);
            }

            // Add lower semesters of the same odd/even pattern (up to semester 8)
            for ($lowerSemester = $semester - 2; $lowerSemester >= 1; $lowerSemester -= 2) {
                if (isset($semesters[$lowerSemester])) {
                    $courses[] = $this->combineCourses($semesters[$lowerSemester]);
                }
            }
        }

        return $courses;
    }

    // Helper method to combine 'kode' and 'sks' into a structured course array
    private function combineCourses($semesterData)
    {
        $combined = [];

        // Ensure that 'kode', 'sks', and other fields have the same count
        $kode = $semesterData['kode'] ?? [];
        $sks = $semesterData['sks'] ?? [];

        for ($i = 0; $i < count($kode); $i++) {
            // Combine 'kode' and 'sks' into a structured format
            $combined[] = [
                'kode' => $kode[$i],
                'sks' => $sks[$i],
            ];
        }

        return $combined;
    }
}
