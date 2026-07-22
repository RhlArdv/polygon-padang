<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMapLayerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'nama' => 'required|string|max:100',
            'deskripsi' => 'nullable|string',
            'tipe' => 'required|in:marker,polygon,both',
            'warna' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'ikon' => 'nullable|string|max:50',
        ];
    }
}
