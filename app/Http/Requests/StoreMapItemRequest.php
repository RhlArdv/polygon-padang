<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMapItemRequest extends FormRequest
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
            'map_layer_id' => 'required|exists:map_layers,id',
            'kecamatan_ids' => 'nullable|array',
            'kecamatan_ids.*' => 'exists:kecamatans,id',
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'tipe' => 'required|in:marker,polygon',
            'latitude' => 'nullable|required_if:tipe,marker|numeric|between:-2,1',
            'longitude' => 'nullable|required_if:tipe,marker|numeric|between:99,102',
            'polygon_coords' => 'nullable|required_if:tipe,polygon|array|min:3',
            'polygon_coords.*' => 'array|size:2',
            'polygon_coords.*.0' => 'numeric',
            'polygon_coords.*.1' => 'numeric',
            'gambar' => 'nullable|image|max:2048',
            'tanggal' => 'nullable|date',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'polygon_coords.min' => 'Polygon harus memiliki minimal 3 titik.',
            'latitude.required_if' => 'Latitude wajib diisi untuk tipe marker.',
            'longitude.required_if' => 'Longitude wajib diisi untuk tipe marker.',
            'polygon_coords.required_if' => 'Koordinat polygon wajib diisi untuk tipe polygon.',
        ];
    }
}
