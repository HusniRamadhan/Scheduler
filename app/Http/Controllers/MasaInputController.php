<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\MasaInput;
use App\Models\MakulInput;
use App\Models\MakulAktif;
use App\Models\JadwalFile;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log; // Import the Log facade

class MasaInputController extends Controller
{
    public function index()
    {
        $masaInputs = MasaInput::all();
        return view('content_admin.adminPraLirs', compact('masaInputs'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'tahun_ajaran' => 'required|string',
            'semester' => 'required|boolean',
            'masa_input' => 'required|string',
            'keterangan' => 'nullable|string',
        ]);

        // Generate `kode_masa_input`
        $kodeMasaInput = 'TA' . substr($data['tahun_ajaran'], 2, 3) . substr($data['tahun_ajaran'], 7, 3);
        $kodeMasaInput .= $data['semester'] ? 'EVEN' : 'ODD';

        $data['kode_masa_input'] = $kodeMasaInput;

        // Check if `kode_masa_input` exists
        if (MasaInput::where('kode_masa_input', $kodeMasaInput)->exists()) {
            return response()->json(['error' => 'Masa input with this academic year and semester already exists.'], 400);
        }

        // Parse `jangka_waktu`
        [$startDate, $endDate] = explode(' - ', $data['masa_input']);
        $data['jangka_waktu'] = Carbon::createFromFormat('d/m/Y', $startDate)->format('d/m/Y') . ' - ' .
            Carbon::createFromFormat('d/m/Y', $endDate)->format('d/m/Y');

        // Save to database
        MasaInput::create($data);

        return response()->json(['success' => 'New masa input has been saved successfully.']);
    }

    public function destroy($id)
    {
        $masaInput = MasaInput::findOrFail($id);
        $masaInput->delete();

        // You can return a response or redirect as needed
        return response()->json(['success' => 'Record deleted successfully']);
    }

    public function edit($id)
    {
        try {
            $masaInput = MasaInput::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Record not found.'], 404);
        }

        $dateFormat = 'd/m/Y';
        $dates = explode(' - ', $masaInput->jangka_waktu);

        try {
            $startDate = Carbon::createFromFormat($dateFormat, trim($dates[0]));
            $endDate = Carbon::createFromFormat($dateFormat, trim($dates[1]));
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to parse dates.'], 400);
        }

        return response()->json([
            'tahun_ajaran' => $masaInput->tahun_ajaran,
            'semester' => $masaInput->semester,
            'start_date' => $startDate->format($dateFormat),
            'end_date' => $endDate->format($dateFormat),
            'keterangan' => $masaInput->keterangan
        ]);
    }

    public function update(Request $request, $kode_masa_input)
    {
        // Validate the input data
        $data = $request->validate([
            'tahun_ajaran' => 'required|string',
            'semester' => 'required|boolean', // '0' or '1' expected
            'masa_input' => 'required|string',
            'keterangan' => 'nullable|string',
        ]);

        // Parse the date range for `masa_input`
        [$start_date, $end_date] = explode(' - ', $data['masa_input']);
        $startOfDay = Carbon::createFromFormat('d/m/Y', $start_date)->startOfDay();
        $endOfDay = Carbon::createFromFormat('d/m/Y', $end_date)->endOfDay();

        // Format and set `jangka_waktu`
        $data['jangka_waktu'] = $startOfDay->format('d/m/Y') . ' - ' . $endOfDay->format('d/m/Y');

        // Find the MasaInput by `kode_masa_input`
        $masaInput = MasaInput::where('kode_masa_input', $kode_masa_input)->firstOrFail();

        // Update `masa_inputs` table
        $masaInput->update($data);

        // // Cascade update on related tables (if necessary)
        // MakulInput::where('kode_masa_input', $kode_masa_input)->update([
        //     'tahun_ajaran' => $data['tahun_ajaran'],
        //     'semester' => $data['semester'],
        // ]);

        // MakulAktif::where('kode_masa_input', $kode_masa_input)->update([
        //     'tahun_ajaran' => $data['tahun_ajaran'],
        //     'semester' => $data['semester'],
        // ]);

        // JadwalFile::where('kode_masa_input', $kode_masa_input)->update([
        //     'tahun_ajaran' => $data['tahun_ajaran'],
        //     'semester' => $data['semester'],
        // ]);

        // Return a success response with SweetAlert2
        return response()->json(['success' => 'Data updated successfully']);
    }
}
