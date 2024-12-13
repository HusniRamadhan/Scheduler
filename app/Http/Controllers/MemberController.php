<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use App\Models\MasaInput;
use App\Models\Makul;
use App\Models\User;
use App\Models\Mahasiswa;
use App\Models\MakulInput;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class MemberController extends Controller
{
    public function user()
    {
        $currentDate = Carbon::now();

        // Fetch the active input period based on the current date
        $masaInputs = MasaInput::all()->sortBy(function ($masaInput) {
            $dateRange = explode(' - ', $masaInput->jangka_waktu);
            return Carbon::createFromFormat('d/m/Y', $dateRange[0]);
        });

        $activeInput = null;
        $nextActiveInput = null;

        foreach ($masaInputs as $masaInput) {
            $dateRange = explode(' - ', $masaInput->jangka_waktu);
            $startDate = Carbon::createFromFormat('d/m/Y', $dateRange[0])->startOfDay();
            $endDate = Carbon::createFromFormat('d/m/Y', $dateRange[1])->endOfDay();

            if ($currentDate->between($startDate, $endDate)) {
                $activeInput = $masaInput;
                break;
            } elseif ($startDate->isAfter($currentDate)) {
                // Check if it's the closest future input period
                if (!$nextActiveInput || $startDate->isBefore(Carbon::createFromFormat('d/m/Y', explode(' - ', $nextActiveInput->jangka_waktu)[0]))) {
                    $nextActiveInput = $masaInput;
                }
            }
        }

        // Log activeInput and nextActiveInput for debugging
        Log::info('Active Input: ', [$activeInput]);
        Log::info('Next Active Input: ', [$nextActiveInput]);

        // Retrieve the current authenticated user's ID
        $userId = Auth::id();
        Log::info('Current Authenticated User ID: ', [$userId]);

        // Fetch MakulInput based on the activeInput or nextActiveInput's kode_masa_input and the current user
        $makulInput = null;
        if ($activeInput) {
            $makulInput = MakulInput::where('kode_masa_input', $activeInput->kode_masa_input)
                ->where('user_id', $userId)
                ->first();
        } elseif ($nextActiveInput) {
            $makulInput = MakulInput::where('kode_masa_input', $nextActiveInput->kode_masa_input)
                ->where('user_id', $userId)
                ->first();
        }

        // Log makulInput for debugging
        Log::info('Makul Input: ', [$makulInput]);

        $makulData = [];

        if ($makulInput) {
            // Decode the 'makul_input' JSON field
            $makulArray = json_decode($makulInput->makul_input, true);

            if ($makulArray) {
                foreach ($makulArray as $makulItem) {
                    // Retrieve subject data from 'Makuls' table using 'kode'
                    $makul = Makul::where('kode', $makulItem['kode'])->first();
                    if ($makul) {
                        $makulData[] = [
                            'urutan' => $makulItem['urutan'],
                            'mata_kuliah' => $makul->mata_kuliah,
                            'sks' => $makul->sks,
                        ];
                    }
                }
            } else {
                Log::warning('Makul Array is empty or invalid for makulInput ID: ', [$makulInput->id]);
            }
        } else {
            Log::warning('No MakulInput found for kode_masa_input: ', [$activeInput ? $activeInput->kode_masa_input : ($nextActiveInput ? $nextActiveInput->kode_masa_input : 'N/A')]);
        }

        // Return data to the view
        return view('content_member.memberHome', compact('masaInputs', 'activeInput', 'nextActiveInput', 'makulData'));
    }

    public function userProfile()
    {
        $user = Auth::user();
        $mahasiswa = Mahasiswa::where('user_id', $user->id)->first();

        return view('content_member.memberProfile', compact('user', 'mahasiswa'));
    }

    // Pralirs
    public function userPraLirs()
    {
        $masaInputs = MasaInput::all();
        $currentDate = Carbon::now();
        $mahasiswa = Mahasiswa::where('user_id', auth()->id())->first();  // Fetch current Mahasiswa

        // Fetch only the makul inputs for the current authenticated user
        $makulInputs = MakulInput::where('user_id', auth()->id())->get();

        $makulInputs->each(function ($makulInput) {
            $makulInputData = json_decode($makulInput->makul_input, true);
            foreach ($makulInputData as &$input) {
                $makul = Makul::where('kode', $input['kode'])->first();
                $input['mata_kuliah'] = $makul ? $makul->mata_kuliah : 'N/A';
            }
            $makulInput->makul_input_data = $makulInputData;
        });

        // Determine the active input period
        $activeInput = null;
        foreach ($masaInputs as $masaInput) {
            $dateRange = explode(' - ', $masaInput->jangka_waktu);
            $startDate = Carbon::createFromFormat('d/m/Y', $dateRange[0])->startOfDay();
            $endDate = Carbon::createFromFormat('d/m/Y', $dateRange[1])->endOfDay();

            if ($currentDate->between($startDate, $endDate)) {
                $activeInput = $masaInput;
                break;
            }
        }

        return view('content_member.memberPraLirs', compact('masaInputs', 'currentDate', 'mahasiswa', 'makulInputs', 'activeInput'));
    }

    public function fetchMakulInputs($kodeMasaInput)
    {
        $makulInputs = MakulInput::where('user_id', auth()->id())
            ->where('kode_masa_input', $kodeMasaInput)
            ->get();

        // Ensure each makul input is enriched with mata_kuliah
        $makulInputs->each(function ($makulInput) {
            $makulInputData = json_decode($makulInput->makul_input, true);
            foreach ($makulInputData as &$input) {
                $makul = Makul::where('kode', $input['kode'])->first();
                $input['mata_kuliah'] = $makul ? $makul->mata_kuliah : 'N/A'; // Add mata_kuliah to the data
            }
            $makulInput->makul_input = json_encode($makulInputData); // Re-encode the enriched data
        });

        return response()->json(['makul_inputs' => $makulInputs]);
    }

    public function showMakulInput($id)
    {
        // Fetch the record from the makul_inputs table by its ID
        $makulInputRecord = MakulInput::find($id);

        if (!$makulInputRecord) {
            return redirect()->back()->with('error', 'Input record not found.');
        }

        // Decode the JSON stored in the makul_input column
        $inputArray = json_decode($makulInputRecord->makul_input, true);

        // Check if JSON decoding was successful
        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::error('JSON decoding failed: ' . json_last_error_msg());
            return redirect()->back()->with('error', 'Failed to decode input data.');
        }

        // Initialize an array to hold the data for the table
        $tableData = [];

        // Loop through each input item and fetch the corresponding mata_kuliah
        foreach ($inputArray as $item) {
            Log::info('Processing item: ' . json_encode($item));

            $makul = Makul::where('kode', $item['kode'])->first();

            if ($makul) {
                $tableData[] = [
                    'no' => $item['urutan'],
                    'mata_kuliah' => $makul->mata_kuliah,
                    'sks' => $item['sks'],
                ];
                Log::info('Makul found: ' . $makul->mata_kuliah);
            } else {
                Log::warning('No Makul found for kode: ' . $item['kode']);
            }
        }

        Log::info('Final tableData: ' . json_encode($tableData));

        // Ensure that $tableData is passed to the view
        return view('content_member.memberPraLirs', compact('tableData'));
    }

    public function checkExistingInput(Request $request)
    {
        $kodeMasaInput = $request->input('kode_masa_input');
        $userId = auth()->id();

        // Check if an entry exists for the given kode_masa_input and user
        $existingInput = MakulInput::where('user_id', $userId)
            ->where('kode_masa_input', $kodeMasaInput)
            ->first();

        if ($existingInput) {
            return response()->json(['status' => 'exist']);  // Input exists
        }

        return response()->json(['status' => 'not_exist']);  // Input does not exist
    }

    public function userSubject()
    {
        $makuls = Makul::all();
        return view('content_member.memberSubject', compact('makuls'));
    }

    // INPUT PRA LIRS
    public function userInput(Request $request)
    {
        // Fetch all regular courses (excluding Mata Kuliah Pilihan and special courses)
        $makuls = Makul::where('IsPilihan', false)
            ->whereNotIn('kode', ['INF-55201-308', 'INF-55201-402', 'INF-55201-406', 'INF-55201-407']) // Exclude special courses
            ->get();

        // Fetch Mata Kuliah Pilihan separately
        $makulPilihan = Makul::where('IsPilihan', true)->get();

        // Fetch special courses separately
        $specialCourses = Makul::whereIn('kode', [
            'INF-55201-308',  // Kerja Praktik
            'INF-55201-402',  // PMKM
            'INF-55201-406',  // TA 1 (Proposal)
            'INF-55201-407'   // TA 2
        ])->get();

        // Get the maximum semester for regular courses
        $maxSemester = Makul::where('IsPilihan', false)->max('semester');

        // Pass data to the view
        return view('content_member.memberPraLirsInput', [
            'makuls' => $makuls,
            'makulPilihan' => $makulPilihan, // Pass Mata Kuliah Pilihan
            'specialCourses' => $specialCourses, // Pass special courses
            'maxSemester' => $maxSemester,
            'id' => $request->query('id'),
            'value' => $request->query('value'),
            'detail' => $request->query('detail'),
        ]);
    }

    // Store Input Method
    public function storeInput(Request $request)
    {
        // Use this to debug the data being submitted
        // dd($request->all());

        // Validate the incoming request
        $request->validate([
            'semester' => 'required|integer',
            'tahunAjaranSelect' => 'required|string',
            'inputDescription' => 'required|string',
        ]);

        $semester = $request->input('semester');
        $tahunAjaran = $request->input('tahunAjaranSelect');
        $makulInput = $request->input('inputDescription');
        $userId = Auth::id();
        $nim = Mahasiswa::where('user_id', $userId)->value('nim');

        // Check if a record already exists for this user and tahun_ajaran
        $existingInput = MakulInput::where('user_id', $userId)
            ->where('kode_masa_input', $tahunAjaran)
            ->first();

        if ($existingInput) {
            // If the record exists, update it
            $existingInput->semester = $semester;  // Update semester if necessary
            $existingInput->makul_input = $makulInput;
            $existingInput->updated_at = now();

            if ($existingInput->save()) {
                // Redirect to the custom URL on successful update
                return redirect()->route('userPraLirs')->with('success', 'Input successfully updated.');
            } else {
                return redirect()->route('userInput')->with('error', 'Failed to update input.');
            }
        } else {
            // If no record exists, create a new one
            $input = new MakulInput();
            $input->semester = $semester;
            $input->tahun_ajaran = $tahunAjaran;
            $input->makul_input = $makulInput;
            $input->user_id = $userId;
            $input->NIM = $nim;

            if ($input->save()) {
                return redirect()->route('userPraLirs')->with('success', 'Input successfully saved.');
            } else {
                return redirect()->route('userInput')->with('error', 'Failed to save input.');
            }
        }
    }
}
