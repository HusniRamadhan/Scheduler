<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMakulRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Change this based on your authorization logic
    }

    public function rules()
    {
        return [
            'mata_kuliah' => 'required|string|max:255',
            'kode' => [
                'required',
                'string',
                'max:255',
                Rule::unique('makuls')->ignore($this->route('makul')), // Ensure uniqueness except for the current record
            ],
            'sks' => 'required|integer|min:1|max:4',
            'semester' => 'required|integer|min:1|max:8',
            'IsPilihan' => 'sometimes|boolean',
        ];
    }
}
