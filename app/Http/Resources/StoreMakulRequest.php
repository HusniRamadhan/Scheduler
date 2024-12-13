<?php

namespace App\Http\Resources;

use Illuminate\Foundation\Http\FormRequest;

class StoreMakulRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Set proper authorization logic here
    }

    public function rules()
    {
        return [
            'mata_kuliah' => 'required|string|max:255',
            'kode' => 'required|string|unique:makuls,kode|max:255',
            'sks' => 'required|integer|min:1|max:4',
            'semester' => 'required|integer|min:1|max:8',
            'IsPilihan' => 'sometimes|boolean',
        ];
    }
}
