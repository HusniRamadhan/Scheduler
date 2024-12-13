<?php

namespace App\Http\Controllers;

use App\Models\Makul;
use App\Models\MakulInput;
use App\Models\Dosen;
use App\Models\MasaInput;
use Illuminate\Http\Request;
use App\Http\Requests\StoreMakulRequest;
use App\Http\Requests\UpdateMakulRequest;

class MakulController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMakulRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Makul $makul)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Makul $makul)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $makul = Makul::findOrFail($id);
        $makul->update($request->all());

        return redirect()->route('adminSubject')->with('success', 'Mata Kuliah updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $makul = Makul::findOrFail($id);
        $makul->delete();

        // You can return a response or redirect as needed
        return response()->json(['success' => true]);
    }
}
