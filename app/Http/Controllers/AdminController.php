<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MasaInput;
use App\Models\MakulInput;
use App\Models\Mahasiswa;
use App\Models\Makul;
use App\Models\Dosen;
use App\Models\User;
use App\Models\Classroom;
use App\Models\MakulAktif;
use App\Models\MakulClass;
use App\Models\DosenMakul;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    public function admin()
    {
        // Fetch all kode_masa_input from the masa_inputs table
        $masaInputs = MasaInput::all();
        $makuls = Makul::all();

        // Add classroom count
        $classrooms = Classroom::all(); // Assuming you have a Classroom model
        $classroomCount = $classrooms->count();

        // Pass the data to the view
        return view('content_admin.adminHome', compact('masaInputs', 'makuls', 'classroomCount'));
    }

    public function getJadwalDataHome(Request $request)
    {
        $kodeMasaInput = $request->kode_masa_input;

        // Fetch schedule data based on kode_masa_input
        $jadwalData = DB::table('jadwal_file')
            ->where('kode_masa_input', $kodeMasaInput)
            ->orderByRaw("FIELD(data_hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat')")
            ->get();

        // If no data found, return an empty array
        if ($jadwalData->isEmpty()) {
            return response()->json([
                'status' => 'empty',
                'message' => 'BELUM ADA DATA JADWAL'
            ]);
        }

        // Return the data as JSON
        return response()->json([
            'status' => 'success',
            'data' => $jadwalData->map(function ($item) {
                return json_decode($item->data_jadwal_per_hari, true); // Decode JSON into an array
            })
        ]);
    }

    //Profile
    public function adminProfile()
    {
        return view('content_admin.adminProfile');
    }
    public function updateEmail(Request $request)
    {
        $request->validate([
            'current_email' => 'required|email',
            'new_email' => 'required|email|unique:users,email',
            'new_email_confirmation' => 'required|same:new_email',
        ]);

        $user = User::find(Auth::id());

        if (!$user || $user->email !== $request->current_email) {
            return back()->with('email_error', 'Email saat ini tidak sesuai.');
        }

        $user->email = $request->new_email;
        $user->save();

        return redirect()->back()->with('email_success', 'Email berhasil diperbarui.');
    }
    // Halaman List User
    public function adminPengguna()
    {
        // Menggunakan Eager Loading untuk mengambil data semesters bersama dengan mahasiswas
        $mahasiswas = Mahasiswa::all();
        $dosens = Dosen::all();
        $users = User::where('role', 'admin')->get();

        return view('content_admin.adminUserList', compact('mahasiswas', 'dosens', 'users'));
    }
    public function AdminDeleteUser($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json(['success' => 'User deleted successfully.']);
    }

    public function AdminDeleteDosen($id)
    {
        $dosen = Dosen::findOrFail($id);
        $dosen->delete();

        return response()->json(['success' => 'Dosen deleted successfully.']);
    }

    public function AdminDeleteMahasiswa($id)
    {
        $mahasiswa = Mahasiswa::findOrFail($id);
        $mahasiswa->delete();

        return response()->json(['success' => 'Mahasiswa deleted successfully.']);
    }
    public function store(Request $request)
    {
        $role = $request->input('role');
        $name = $request->input('name');
        $email = $request->input('email');
        $password = Hash::make($request->input('password'));

        // Create a new user
        $user = new User();
        $user->name = $name;
        $user->email = $email;
        $user->password = $password;
        $user->role = $role;

        // Save user data based on the role
        if ($role == 'admin') {
            $user->save(); // Only save to User table
        } elseif ($role == 'dosen') {
            $user->save(); // Save to User table first

            $dosen = new Dosen();
            $dosen->nama_dosen = $name;
            $dosen->kode_dosen = $request->input('kode_dosen');
            $dosen->save(); // Save to Dosens table
        } elseif ($role == 'mahasiswa') {
            $user->save(); // Save to User table first

            $mahasiswa = new Mahasiswa();
            $mahasiswa->name = $name;
            $mahasiswa->NIM = $request->input('NIM');
            $mahasiswa->angkatan = $request->input('angkatan');
            $mahasiswa->user_id = $user->id; // Link to User table
            $mahasiswa->save(); // Save to Mahasiswas table
        }

        return redirect()->back()->with('success', 'Data berhasil disimpan.');
    }
    public function updateAdmin(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        if ($request->input('password')) {
            $user->password = Hash::make($request->input('password'));
        }
        $user->save();

        return redirect()->back()->with('success', 'Admin updated successfully.');
    }

    public function updateDosen(Request $request, $id)
    {
        $dosen = Dosen::findOrFail($id);
        $dosen->nama_dosen = $request->input('name');
        $dosen->kode_dosen = $request->input('kode_dosen');
        $dosen->save();

        return redirect()->back()->with('success', 'Dosen updated successfully.');
    }

    public function updateMahasiswa(Request $request, $id)
    {
        // Step 1: Validate NIM uniqueness and required fields
        $request->validate([
            'name' => 'required|string|max:255',
            'NIM' => 'required|string|max:255|unique:mahasiswas,NIM,' . $id, // Unique for other records
            'angkatan' => 'required|integer'
        ]);

        // Step 2: Begin Transaction
        DB::transaction(function () use ($request, $id) {
            $mahasiswa = Mahasiswa::findOrFail($id);
            $oldNIM = $mahasiswa->NIM;
            $newNIM = $request->input('NIM');

            // Update the Mahasiswa fields
            $mahasiswa->name = $request->input('name');
            $mahasiswa->NIM = $newNIM;
            $mahasiswa->angkatan = $request->input('angkatan');
            $mahasiswa->save();

            // Update related records in makul_inputs to reflect the new NIM
            MakulInput::where('NIM', $oldNIM)->update(['NIM' => $newNIM]);
        });

        // Step 3: Return with Success Message
        return redirect()->back()->with('success', 'Mahasiswa updated successfully.');
    }

    // Halaman Pra Lirs
    public function adminPraLirs()
    {
        // Retrieve all records from the MasaInput model
        $masaInputs = MasaInput::all();

        return view('content_admin.adminPraLirs', compact('masaInputs'));
    }

    // Halaman Scheduling
    public function adminScheduling()
    {
        $masaInputs = MasaInput::all();
        $inputCounts = [];

        foreach ($masaInputs as $masaInput) {
            // Extract the starting year from 'tahun_ajaran' (e.g., "2017/2018" -> 2017)
            $tahunAjaranParts = explode('/', $masaInput->tahun_ajaran);
            $startYear = (int) $tahunAjaranParts[0];

            // Calculate the batch year limit (7 years back from the start year of 'tahun_ajaran')
            $batchYearLimit = $startYear - 7;

            // Calculate Total Mahasiswa for this 'tahun_ajaran'
            $totalMahasiswa = Mahasiswa::whereBetween('angkatan', [$batchYearLimit, $startYear])->count();

            // Calculate Total KRS Mahasiswa (count of 'makul_inputs' for this 'kode_masa_input')
            $totalKRS = MakulInput::where('kode_masa_input', $masaInput->kode_masa_input) // match against kode_masa_input
                ->whereIn('NIM', function ($query) use ($batchYearLimit, $startYear) {
                    $query->select('NIM')
                        ->from('mahasiswas')
                        ->whereBetween('angkatan', [$batchYearLimit, $startYear]);
                })
                ->count();

            // Store both total mahasiswa and total KRS mahasiswa for this tahun_ajaran
            $inputCounts[$masaInput->tahun_ajaran] = [
                'totalMahasiswa' => $totalMahasiswa,
                'totalKRS' => $totalKRS
            ];
        }

        // Fetch mata kuliah (makuls)
        $makuls = Makul::all();

        // Fetch classroom count
        $classrooms = Classroom::all();
        $classroomCount = $classrooms->count();

        return view('content_admin.adminScheduling', compact('masaInputs', 'inputCounts', 'makuls', 'classroomCount'));
    }

    public function getMahasiswaInputCounts()
    {
        try {
            // Get the current year and calculate the batch year limit
            $batchYearLimit = Carbon::now()->year - 7;

            // Count the total number of students within the 7-year limit
            $totalMahasiswa = Mahasiswa::where('angkatan', '>=', $batchYearLimit)->count();

            // Get the counts of students who have input their schedules, grouped by tahun_ajaran
            $inputCounts = MakulInput::select('kode_masa_input', DB::raw('count(*) as count'))
                ->whereIn('nim', function ($query) use ($batchYearLimit) {
                    $query->select('NIM')
                        ->from('mahasiswas')
                        ->where('angkatan', '>=', $batchYearLimit);
                })
                ->groupBy('kode_masa_input')
                ->pluck('count', 'kode_masa_input');

            return response()->json([
                'totalMahasiswa' => $totalMahasiswa,
                'inputCounts' => $inputCounts
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while fetching data.'], 500);
        }
    }

    public function checkJadwalExists(Request $request)
    {
        $kodeMasaInput = $request->kode_masa_input;

        // Check if the schedule exists in jadwal_file
        $exists = DB::table('jadwal_file')
            ->where('kode_masa_input', $kodeMasaInput)
            ->exists();

        return response()->json(['exists' => $exists]);
    }

    public function getJadwalDataScheduling(Request $request)
    {
        $kodeMasaInput = $request->kode_masa_input;

        // Log untuk memastikan kode yang diterima
        Log::info('Kode Masa Input:', ['kode' => $kodeMasaInput]);

        // Ambil data jadwal berdasarkan kode_masa_input
        $jadwalData = DB::table('jadwal_file')
            ->where('kode_masa_input', $kodeMasaInput)
            ->orderByRaw("FIELD(data_hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat')")
            ->get();

        // Cek apakah query benar-benar mengembalikan data
        if ($jadwalData->isEmpty()) {
            Log::info('Tidak ada data jadwal untuk kode:', ['kode' => $kodeMasaInput]);
            return response()->json([
                'status' => 'empty',
                'message' => 'BELUM ADA DATA JADWAL'
            ]);
        }

        // Mengembalikan response JSON jika data ada
        return response()->json([
            'status' => 'success',
            'data' => $jadwalData->map(function ($item) {
                return json_decode($item->data_jadwal_per_hari, true); // Decode JSON ke array
            })
        ]);
    }

    // AdminSchedulingInput
    public function adminSchedulingInput(Request $request)
    {
        $tahunAjaran = $request->query('tahun_ajaran');
        $semester = $request->query('semester');

        // Fetch Masa Input record for the given tahun_ajaran and semester
        $masaInput = MasaInput::where('tahun_ajaran', $tahunAjaran)
            ->where('semester', $semester)
            ->first();

        if (!$masaInput) {
            return redirect()->back()->with('error', 'Masa input not found for the given year and semester.');
        }

        // Exclude special Mata Kuliah (Kerja Praktik, PMKM, TA 1, TA 2)
        $excludedMakuls = ['INF-55201-406', 'INF-55201-402', 'INF-55201-308', 'INF-55201-407'];

        // Fetch regular Mata Kuliah (Makul) with IsPilihan = False and status_aktif = 1
        $makuls = Makul::join('makul_aktif', 'makuls.kode', '=', 'makul_aktif.kode_makul')
            ->where('makul_aktif.kode_masa_input', $masaInput->kode_masa_input)
            ->where('makul_aktif.status_aktif', 1)  // Only active courses
            ->where('IsPilihan', false)  // Exclude IsPilihan courses
            ->whereNotIn('kode', $excludedMakuls)  // Exclude special courses
            ->get();

        // Fetch Mata Kuliah Pilihan where IsPilihan = True and status_aktif = 1
        $makulPilihan = Makul::join('makul_aktif', 'makuls.kode', '=', 'makul_aktif.kode_makul')
            ->where('makul_aktif.kode_masa_input', $masaInput->kode_masa_input)
            ->where('makul_aktif.status_aktif', 1)  // Only active courses
            ->where('IsPilihan', true)  // Only IsPilihan courses
            ->get();

        // Fetch special Mata Kuliah (Kerja Praktik, PMKM, TA 1, TA 2) with status_aktif = 1
        // $specialMakuls = Makul::join('makul_aktif', 'makuls.kode', '=', 'makul_aktif.kode_makul')
        //     ->where('makul_aktif.kode_masa_input', $masaInput->kode_masa_input)
        //     ->where('makul_aktif.status_aktif', 1)  // Only active special courses
        //     ->whereIn('kode', $excludedMakuls)
        //     ->get();

        // Fetch special Mata Kuliah (Kerja Praktik, PMKM, TA 1, TA 2)
        $specialMakuls = Makul::whereIn('kode', $excludedMakuls)->get();

        // Fetch all Dosen (lecturers)
        $dosens = Dosen::all();

        // Initialize array to store the count of students for each Mata Kuliah (Makul)
        $jumlahMahasiswa = [];

        // Fetch MakulInput records for the given tahun_ajaran
        $makulInputs = MakulInput::where('kode_masa_input', $masaInput->kode_masa_input)->get();

        foreach ($makulInputs as $makulInput) {
            $selectedMakul = json_decode($makulInput->makul_input, true);

            if (is_array($selectedMakul)) {
                foreach ($selectedMakul as $makul) {
                    $kode = $makul['kode'];

                    // Increment the count for the Mata Kuliah's 'kode'
                    if (isset($jumlahMahasiswa[$kode])) {
                        $jumlahMahasiswa[$kode]++;
                    } else {
                        $jumlahMahasiswa[$kode] = 1;
                    }
                }
            }
        }

        // Fetch the class data from makul_class table using kode_masa_input
        $makulClass = MakulClass::where('kode_masa_input', $masaInput->kode_masa_input)->first();
        $dataKelas = $makulClass ? $makulClass->data_kelas : 'No class data available';

        return view('content_admin.adminScheduleInput', [
            'masaInput' => $masaInput,
            'tahunAjaran' => $tahunAjaran,
            'semester' => $semester,
            'makuls' => $makuls,  // Regular Makul for the selected semester
            'makulPilihan' => $makulPilihan, // Mata Kuliah Pilihan in a separate table
            'specialMakuls' => $specialMakuls, // Kerja Praktik, PMKM, TA 1, TA 2
            'dosens' => $dosens,
            'jumlahMahasiswa' => $jumlahMahasiswa,
            'dataKelas' => $dataKelas, // Pass the dataKelas to the view
        ]);
    }


    public function adminSchedulingInputAjax(Request $request)
    {
        try {
            $semester = $request->query('semester');
            $isMakulPilihan = $request->query('is_pilihan', false);  // Detect if it's a Mata Kuliah Pilihan request

            $excludedMakuls = ['INF-55201-406', 'INF-55201-402', 'INF-55201-308', 'INF-55201-407'];
            if ($semester == 1) {
                $excludedMakuls[] = 'INF-55201-401';  // Exclude Teknopreneur if semester is Genap
            }

            if ($isMakulPilihan) {
                // Mata Kuliah Pilihan request
                $makuls = Makul::where('IsPilihan', true)->get();
            } else {
                // Regular Mata Kuliah request
                $makuls = Makul::whereIn('semester', $semester == 0 ? [1, 3, 5, 7, 8] : [2, 4, 6, 7, 8])
                    ->where('IsPilihan', false)
                    ->whereNotIn('kode', $excludedMakuls)
                    ->get();
            }

            $dosens = Dosen::all();

            // Render the partial view and pass the $isMakulPilihan flag
            $html = view('partials.adminSchedulingInput_makul_table_rows', compact('makuls', 'dosens', 'isMakulPilihan', 'semester'))->render();

            return response()->json(['html' => $html]);
        } catch (\Exception $e) {
            Log::error('Error in adminSchedulingInputAjax: ' . $e->getMessage());
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }

    public function getJumlahMahasiswa(Request $request)
    {
        $kodeMasaInput = $request->query('kode_masa_input');

        // Fetch MakulInput records for the given 'kode_masa_input'
        $makulInputs = MakulInput::where('kode_masa_input', $kodeMasaInput)->get();

        $jumlahMahasiswa = [];

        foreach ($makulInputs as $makulInput) {
            $selectedMakul = json_decode($makulInput->makul_input, true);

            if (is_array($selectedMakul)) {
                foreach ($selectedMakul as $makul) {
                    $kode = $makul['kode'];

                    if (isset($jumlahMahasiswa[$kode])) {
                        $jumlahMahasiswa[$kode]++;
                    } else {
                        $jumlahMahasiswa[$kode] = 1;
                    }
                }
            }
        }

        return response()->json($jumlahMahasiswa);
    }

    public function adminSchedulingCreate(Request $request)
    {
        // Fetch the number of classrooms
        $classrooms = Classroom::all();
        $classroomCount = $classrooms->count();

        // Define the days of the week
        $daysOfWeek = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];

        // Generate the schedule structure based on the rules for SKS and time slots
        $schedule = [];

        foreach ($daysOfWeek as $day) {
            for ($session = 0; $session < 4; $session++) {
                for ($i = 0; $i < $classroomCount; $i++) {
                    // Define the start and end time based on the day and session, and modify according to SKS
                    $session_time = '';

                    // Senin to Kamis
                    if (in_array($day, ['Senin', 'Selasa', 'Rabu', 'Kamis'])) {
                        if ($session == 0) {
                            // Sesi 1
                            $session_time = '07:30 s/d ##:##';  // Default
                        } elseif ($session == 1) {
                            // Sesi 2
                            $session_time = '##:## s/d 11:50'; // Default
                        } elseif ($session == 2) {
                            // Sesi 3
                            $session_time = '12:45 s/d ##:##'; // Default
                        } elseif ($session == 3) {
                            // Sesi 4
                            $session_time = '14:45 s/d ##:##'; // Default
                        }
                    }

                    // Jumat specific rules
                    if ($day === 'Jumat') {
                        if ($session == 0) {
                            // Sesi 1
                            $session_time = '07:30 s/d ##:##';  // Default, max 2 SKS
                        } elseif ($session == 1) {
                            // Sesi 2
                            $session_time = '##:## s/d 11:00';  // Default, max 2 SKS
                        } elseif ($session == 2) {
                            // Sesi 3
                            $session_time = '13:00 s/d ##:##';  // Default
                        } elseif ($session == 3) {
                            // Sesi 4
                            $session_time = '15:35 s/d ##:##';  // Default, max 2 SKS
                        }
                    }

                    // Add to schedule array
                    $schedule[] = [
                        'day' => $day,
                        'session' => $session + 1,
                        'session_time' => $session_time,
                        'classroom' => $classrooms[$i]->ruang_kelas,
                    ];
                }
                // Add an empty row to separate each session
                $schedule[] = ['empty' => true];
            }
            // Add an empty row to separate each day
            $schedule[] = ['empty' => true];
        }

        // Return the schedule data as a JSON response
        return response()->json($schedule);
    }

    public function storeJadwalArray(Request $request)
    {
        try {
            $kodeMasaInput = $request->input('kodeMasaInput');
            $dataHari = $request->input('data_hari');
            $jadwalData = $request->input('jadwalData');

            // Cek apakah data dengan kodeMasaInput dan data_hari sudah ada
            $existingJadwal = DB::table('jadwal_file')
                ->where('kode_masa_input', $kodeMasaInput)
                ->where('data_hari', $dataHari)
                ->first();

            if ($existingJadwal) {
                // Jika sudah ada, kirimkan response ke frontend untuk menampilkan konfirmasi
                return response()->json(['exists' => true], 200);
            } else {
                // Jika tidak ada, simpan data baru
                DB::table('jadwal_file')->insert([
                    'kode_masa_input' => $kodeMasaInput,
                    'data_hari' => $dataHari,
                    'data_jadwal_per_hari' => json_encode($jadwalData), // Simpan sebagai JSON
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                return response()->json(['success' => true], 200);
            }
        } catch (\Exception $e) {
            Log::error('Error saving jadwal for ' . $dataHari . ': ' . $e->getMessage());
            return response()->json(['error' => 'Failed to save jadwal for ' . $dataHari], 500);
        }
    }

    public function overwriteJadwal(Request $request)
    {
        try {
            $kodeMasaInput = $request->input('kodeMasaInput');
            $dataHari = $request->input('data_hari');
            $jadwalData = $request->input('jadwalData');

            // Update data yang sudah ada berdasarkan kodeMasaInput dan data_hari
            DB::table('jadwal_file')
                ->where('kode_masa_input', $kodeMasaInput)
                ->where('data_hari', $dataHari)
                ->update([
                    'data_jadwal_per_hari' => json_encode($jadwalData),
                    'updated_at' => now()
                ]);

            return response()->json(['success' => true], 200);
        } catch (\Exception $e) {
            Log::error('Error updating jadwal for ' . $dataHari . ': ' . $e->getMessage());
            return response()->json(['error' => 'Failed to update jadwal for ' . $dataHari], 500);
        }
    }

    // Mata Kuliah
    public function adminSubject()
    {
        $makuls = Makul::all();
        $classrooms = Classroom::all(); // Fetch classrooms
        return view('content_admin.adminSubject', compact('makuls', 'classrooms'));
    }

    public function makulstore(Request $request) // Renamed from 'store' to 'makulstore'
    {
        // Validate the request
        $request->validate([
            'mata_kuliah' => 'required|string|max:255',
            'kode' => 'required|string|max:255|unique:makuls',
            'sks' => 'required|integer',
            'semester' => 'required|integer',
            'IsPilihan' => 'boolean',
        ]);

        // Create new makul
        Makul::create([
            'mata_kuliah' => $request->mata_kuliah,
            'kode' => $request->kode,
            'sks' => $request->sks,
            'semester' => $request->semester,
            'IsPilihan' => $request->has('IsPilihan') ? true : false,
        ]);

        // Redirect with success message
        return redirect()->route('adminSubject')->with('success', 'Mata Kuliah created successfully');
    }

    public function destroyMakul($id)
    {
        // Find the makul by ID
        $makul = Makul::findOrFail($id);

        // Delete the makul
        $makul->delete();

        return response()->json(['success' => 'Mata Kuliah deleted successfully']);
    }

    public function updateMakul(Request $request, $id)
    {
        // Validasi input yang diterima
        $request->validate([
            'mata_kuliah' => 'required|string|max:255',
            'kode' => [
                'required',
                'string',
                'max:255',
                Rule::unique('makuls')->ignore($id),
            ],
            'sks' => 'required|integer',
            'semester' => 'required|integer',
            'IsPilihan' => 'boolean', // Diharapkan nilai boolean
        ]);

        // Cari data makul berdasarkan id
        $makul = Makul::findOrFail($id);

        // Ambil nilai kode lama sebelum diubah
        $oldKode = $makul->kode;

        // Tentukan nilai `IsPilihan` dari checkbox
        $isPilihan = $request->has('IsPilihan') ? true : false;

        // Update data di tabel `makuls`
        $makul->update([
            'mata_kuliah' => $request->mata_kuliah,
            'kode' => $request->kode,
            'sks' => $request->sks,
            'semester' => $request->semester,
            'IsPilihan' => $isPilihan,
        ]);

        // Update kode di tabel `makul_aktif` jika kode lama diubah
        if ($oldKode !== $request->kode) {
            MakulAktif::where('kode_makul', $oldKode)
                ->update(['kode_makul' => $request->kode]);
        }

        // Redirect dengan pesan sukses
        return redirect()->route('adminSubject')->with('success', 'Mata Kuliah updated successfully');
    }

    public function adminClassroomStore(Request $request)
    {
        $request->validate([
            'ruang_kelas' => 'required|string|max:255',
            'jenisKelas' => 'required|boolean', // Validate 'jenis_kelas' as boolean
        ]);

        Classroom::create([
            'ruang_kelas' => $request->ruang_kelas,
            'jenis_kelas' => $request->jenisKelas, // Store the 'jenis_kelas'
        ]);

        return redirect()->back()->with('success', 'Ruang kelas berhasil ditambahkan');
    }

    public function adminClassroomUpdate(Request $request, $id)
    {
        $request->validate([
            'ruang_kelas' => 'required|string|max:255',
            'jenisKelas' => 'required|boolean', // Validate 'jenis_kelas' as boolean
        ]);

        $classroom = Classroom::findOrFail($id); // Find by ID from the URL
        $classroom->update([
            'ruang_kelas' => $request->ruang_kelas,
            'jenis_kelas' => $request->jenisKelas, // Update the 'jenis_kelas'
        ]);

        return redirect()->back()->with('success', 'Ruang kelas berhasil diupdate');
    }

    public function adminClassroomDelete($id)
    {
        $classroom = Classroom::findOrFail($id);
        $classroom->delete();

        // Return JSON response to indicate success
        return response()->json(['success' => true]);
    }

    public function getAvailableDosens(Request $request)
    {
        // Get the selected Dosen 1 from the request
        $selectedDosen1 = $request->query('selected_dosen1');

        // Fetch all dosens except the one selected as Dosen 1
        $availableDosens = Dosen::where('id', '!=', $selectedDosen1)->get();

        // Return the dosens as a JSON response
        return response()->json(['dosens' => $availableDosens]);
    }

    // Admin Aktivasi Makul
    public function adminAktivasi()
    {
        // Fetch all kode_masa_input from the masa_inputs table
        $masaInputs = MasaInput::all();

        // Fetch makuls based on the tab requirements
        $gasalMakul = Makul::whereIn('semester', [1, 3, 5, 7])
            ->where('IsPilihan', false)
            ->whereNotIn('mata_kuliah', ['Kerja Praktik', 'PMKM', 'TA 1 (Proposal)', 'TA 2'])
            ->whereNotIn('kode', ['INF-55201-308', 'INF-55201-402', 'INF-55201-406', 'INF-55201-407'])
            ->get();

        $genapMakul = Makul::whereIn('semester', [2, 4, 6])
            ->where('IsPilihan', false)
            ->whereNotIn('mata_kuliah', ['Kerja Praktik', 'PMKM', 'TA 1 (Proposal)', 'TA 2'])
            ->whereNotIn('kode', ['INF-55201-308', 'INF-55201-402', 'INF-55201-406', 'INF-55201-407'])
            ->get();

        $pilihanMakul = Makul::where('IsPilihan', true)->get();

        $khususMakul = Makul::whereIn('mata_kuliah', ['Kerja Praktik', 'PMKM', 'TA 1 (Proposal)', 'TA 2'])
            ->orWhereIn('kode', ['INF-55201-308', 'INF-55201-402', 'INF-55201-406', 'INF-55201-407'])
            ->get();

        // Pass the data to the view
        return view('content_admin.adminAktifMakul', compact('masaInputs', 'gasalMakul', 'genapMakul', 'pilihanMakul', 'khususMakul'));
    }

    public function saveAktivasi(Request $request)
    {
        $data = $request->input('data'); // Get the 'data' from the request

        // Iterate through the data and insert/update 'makul_aktif'
        foreach ($data as $item) {
            MakulAktif::updateOrCreate(
                [
                    'kode_masa_input' => $item['kode_masa_input'],
                    'kode_makul' => $item['kode_makul']
                ],
                [
                    'status_aktif' => $item['status_aktif']
                ]
            );
        }

        return response()->json(['status' => 'success']);
    }

    public function updateAktivasi(Request $request)
    {
        $kodeMasaInput = $request->input('kode_masa_input');
        $data = $request->input('data');

        // Delete all entries with the same kode_masa_input
        MakulAktif::where('kode_masa_input', $kodeMasaInput)->delete();

        // Insert new data
        foreach ($data as $item) {
            MakulAktif::create([
                'kode_masa_input' => $item['kode_masa_input'],
                'kode_makul' => $item['kode_makul'],
                'status_aktif' => $item['status_aktif'],
            ]);
        }

        return response()->json(['status' => 'success']);
    }

    public function checkKodeMasa($kodeMasaInput)
    {
        // Check if there's any data with the same kode_masa_input in makul_aktif
        $exists = MakulAktif::where('kode_masa_input', $kodeMasaInput)->exists();

        return response()->json(['exists' => $exists]);
    }

    public function getStatusLecturer($kode_masa_input)
    {
        $statusData = MakulAktif::where('kode_masa_input', $kode_masa_input)->get(['kode_makul', 'status_aktif']);
        return response()->json($statusData);
    }

    // Admin Manajemen Kelas
    public function adminManajemen()
    {
        // Fetch data
        $masaInputs = MasaInput::all();
        $makuls = Makul::all();
        $dosens = Dosen::all();

        // Fetch active data from makul_aktif table
        $activeMakuls = MakulAktif::where('status_aktif', true)->get();

        // Separate Makul into categories
        $gasalMakul = Makul::whereIn('semester', [1, 3, 5, 7])
            ->where('IsPilihan', false)
            ->whereNotIn('mata_kuliah', ['Kerja Praktik', 'PMKM', 'TA 1 (Proposal)', 'TA 2'])
            ->whereNotIn('kode', ['INF-55201-308', 'INF-55201-402', 'INF-55201-406', 'INF-55201-407'])
            ->whereIn('kode', $activeMakuls->pluck('kode_makul')) // Match active makuls
            ->get();

        $genapMakul = Makul::whereIn('semester', [2, 4, 6])
            ->where('IsPilihan', false)
            ->whereNotIn('mata_kuliah', ['Kerja Praktik', 'PMKM', 'TA 1 (Proposal)', 'TA 2'])
            ->whereNotIn('kode', ['INF-55201-308', 'INF-55201-402', 'INF-55201-406', 'INF-55201-407'])
            ->whereIn('kode', $activeMakuls->pluck('kode_makul')) // Match active makuls
            ->get();

        $pilihanMakul = Makul::where('IsPilihan', true)
            ->whereIn('kode', $activeMakuls->pluck('kode_makul')) // Match active makuls
            ->get();

        // Fetching only special Makuls that are active in 'makul_aktif' table
        $khususMakul = Makul::WhereIn('kode', ['INF-55201-308', 'INF-55201-402', 'INF-55201-406', 'INF-55201-407'])
            ->whereIn('kode', $activeMakuls->pluck('kode_makul')) // Ensure only active ones
            ->get();

        // Fetch student count data
        $jumlahMahasiswa = []; // Example placeholder. This should be populated from the actual logic.

        $filteredMakuls = $gasalMakul->map(function ($makul) use ($dosens) {
            $relatedDosenIds = DosenMakul::where('makul_id', $makul->id)->pluck('dosen_id');
            if ($relatedDosenIds->isNotEmpty()) {
                $makul->dosens = Dosen::whereIn('id', $relatedDosenIds)->get();
            } else {
                $makul->dosens = $dosens; // Fallback to all dosens
            }
            return $makul;
        });

        // Render the partial view as HTML
        $html = view('partials.adminManageClass_makul_table', [
            'makuls' => $makuls,
            'dosens' => $dosens,
            'jumlahMahasiswa' => $jumlahMahasiswa
        ])->render();

        // Optionally, log or return the $html if necessary
        // Log::info($html); // Example logging the HTML if needed

        // Render the main view with all data
        return view('content_admin.adminManageClass', compact(
            'masaInputs',
            'gasalMakul',
            'dosens',
            'genapMakul',
            'pilihanMakul',
            'khususMakul',
            'filteredMakuls',
            'jumlahMahasiswa',
            'html'
        ));
    }

    public function checkMakulClass(Request $request)
    {
        $exists = MakulClass::where('kode_masa_input', $request->kode_masa_input)->exists();
        return response()->json(['exists' => $exists]);
    }

    public function getDosenForMakul(Request $request)
    {
        $makulKode = $request->input('makul_id'); // Perbaiki nama key jika `makul_id` sebenarnya adalah kode
        $makul = Makul::where('kode', $makulKode)->first(); // Cari berdasarkan kode

        if (!$makul) {
            return response()->json(['error' => 'Invalid Makul Code'], 400);
        }

        // Gunakan id yang valid untuk query
        $relatedDosenIds = DosenMakul::where('makul_id', $makul->id)->pluck('dosen_id');
        $dosens = $relatedDosenIds->isNotEmpty()
            ? Dosen::whereIn('id', $relatedDosenIds)->get()
            : Dosen::all(); // Fallback to all dosens

        return response()->json(['dosens' => $dosens]);
    }

    public function checkMasaInputClass(Request $request)
    {
        $kodeMasaInput = $request->input('kode_masa_input');

        // Check if the selected kode_masa_input exists in makul_aktif table
        $exists = MakulAktif::where('kode_masa_input', $kodeMasaInput)->exists();

        // Return the result as JSON
        return response()->json(['exists' => $exists]);
    }

    public function saveMakulClass(Request $request)
    {
        // Validate the request data
        $request->validate([
            'kode_masa_input' => 'required|string|exists:masa_inputs,kode_masa_input', // Ensure valid relation
            'data_kelas' => 'required|string', // Ensure data_kelas is provided
        ]);

        // Save to makul_class table
        $makulClass = new MakulClass();
        $makulClass->kode_masa_input = $request->kode_masa_input;
        $makulClass->data_kelas = $request->data_kelas;
        $makulClass->save();

        // Return success response
        return response()->json(['success' => true]);
    }

    public function updateMakulClass(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'kode_masa_input' => 'required|string|exists:makul_class,kode_masa_input',
            'data_kelas' => 'required|string',
        ]);

        // Find the existing record by kode_masa_input and update it
        $makulClass = MakulClass::where('kode_masa_input', $request->kode_masa_input)->first();
        $makulClass->data_kelas = $request->data_kelas;
        $makulClass->save();

        return response()->json(['success' => true]);
    }

    // Admin Lecturer
    public function adminPengajar()
    {
        $dosens = Dosen::all();
        $makuls = Makul::all();
        $dosenmakul = DosenMakul::with(['dosen', 'makul'])->get(); // Include related data

        return view('content_admin.adminLecturer', compact('dosens', 'makuls', 'dosenmakul'));
    }

    public function storeLecturer(Request $request)
    {
        $request->validate([
            'makul_id' => ['required', 'exists:makuls,id'], // Validate mata kuliah
            'dosen_id' => ['required', 'array', 'min:1'], // Ensure at least one dosen is selected
            'dosen_id.*' => ['exists:dosens,id'], // Validate each dosen ID
        ]);

        $makulId = $request->input('makul_id');
        $dosenIds = $request->input('dosen_id');

        $added = [];
        $skipped = [];

        foreach ($dosenIds as $dosenId) {
            $exists = DosenMakul::where('makul_id', $makulId)->where('dosen_id', $dosenId)->exists();
            if ($exists) {
                $skipped[] = Dosen::find($dosenId)->nama_dosen;
            } else {
                DosenMakul::create([
                    'makul_id' => $makulId,
                    'dosen_id' => $dosenId,
                ]);
                $added[] = Dosen::find($dosenId)->nama_dosen;
            }
        }

        // Bangun pesan HTML untuk alert
        $message = '';
        if (!empty($added)) {
            $message .= '<div class="alert alert-success alert-dismissible fade show" role="alert">';
            $message .= 'Data dosen berikut berhasil ditambahkan: ' . implode(', ', $added) . '.';
            $message .= '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
            $message .= '</div>';
        }
        if (!empty($skipped)) {
            $message .= '<div class="alert alert-danger alert-dismissible fade show" role="alert">';
            $message .= 'Data dosen berikut batal ditambahkan karena sudah ada: ' . implode(', ', $skipped) . '.';
            $message .= '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
            $message .= '</div>';
        }

        return redirect()->back()->with('alert', $message);
    }

    public function editLecturer($makulId)
    {
        // Ambil data terkait mata kuliah
        $dosenMakul = DosenMakul::where('makul_id', $makulId)->with('dosen')->get();
        $makul = Makul::findOrFail($makulId); // Pastikan makul ada
        $dosens = Dosen::all(); // Semua dosen untuk Select2

        return response()->json([
            'dosenMakul' => $dosenMakul,
            'makul' => $makul,
            'dosens' => $dosens,
        ]);
    }

    public function updateLecturer(Request $request, $makulId)
    {
        $request->validate([
            'dosen_id' => ['required', 'array', 'min:1'], // Minimal satu dosen dipilih
            'dosen_id.*' => ['exists:dosens,id'], // Validasi setiap dosen ID
        ]);

        $dosenIds = $request->input('dosen_id');
        $currentDosenMakul = DosenMakul::where('makul_id', $makulId)->pluck('dosen_id')->toArray();

        $added = [];
        $skipped = [];

        foreach ($dosenIds as $dosenId) {
            if (!in_array($dosenId, $currentDosenMakul)) {
                DosenMakul::create([
                    'makul_id' => $makulId,
                    'dosen_id' => $dosenId,
                ]);
                $added[] = Dosen::find($dosenId)->nama_dosen;
            } else {
                $skipped[] = Dosen::find($dosenId)->nama_dosen;
            }
        }

        DosenMakul::where('makul_id', $makulId)
            ->whereNotIn('dosen_id', $dosenIds)
            ->delete();

        // Bangun pesan HTML untuk alert
        $message = '';
        if (!empty($added)) {
            $message .= '<div class="alert alert-success alert-dismissible fade show" role="alert">';
            $message .= 'Data dosen berikut berhasil ditambahkan: ' . implode(', ', $added) . '.';
            $message .= '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
            $message .= '</div>';
        }
        if (!empty($skipped)) {
            $message .= '<div class="alert alert-danger alert-dismissible fade show" role="alert">';
            $message .= 'Data dosen berikut sudah ada dan tidak diubah: ' . implode(', ', $skipped) . '.';
            $message .= '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
            $message .= '</div>';
        }

        return redirect()->back()->with('alert', $message);
    }

    public function destroyLecturer(Request $request, $makulId)
    {
        $request->validate([
            'dosen_id' => ['nullable', 'array'], // Bisa kosong jika semua dosen akan dihapus
            'dosen_id.*' => ['exists:dosens,id'], // Validasi dosen ID
        ]);

        $dosenIds = $request->input('dosen_id');
        if ($dosenIds) {
            // Hapus dosen tertentu
            DosenMakul::where('makul_id', $makulId)->whereIn('dosen_id', $dosenIds)->delete();
        } else {
            // Hapus semua dosen terkait mata kuliah
            DosenMakul::where('makul_id', $makulId)->delete();
        }

        return redirect()->back()->with('success', 'Pengajar berhasil dihapus.');
    }
    // public function storeDosenMakul(Request $request)
    // {
    //     // Validasi input
    //     $request->validate([
    //         'dosen_id' => 'required|exists:dosens,id',
    //         'makul_id' => 'required|exists:makuls,id',
    //     ]);

    //     // Simpan data ke tabel dosen_makul
    //     DosenMakul::create([
    //         'dosen_id' => $request->dosen_id,
    //         'makul_id' => $request->makul_id,
    //     ]);

    //     // Redirect ke halaman sebelumnya dengan pesan sukses
    //     return redirect()->back()->with('success', 'Data berhasil disimpan.');
    // }
}
