<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMapItemRequest extends FormRequest
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
            'judul' => 'sometimes|required|string|max:255',
            'deskripsi' => 'nullable|string',
            'tipe' => 'sometimes|required|in:marker,polygon',
            'latitude' => 'nullable|numeric|between:-2,1',
            'longitude' => 'nullable|numeric|between:99,102',
            'polygon_coords' => 'nullable|array|min:3',
            'polygon_coords.*' => 'array|size:2',
            'polygon_coords.*.0' => 'numeric',
            'polygon_coords.*.1' => 'numeric',
            'gambar' => 'nullable|image|max:2048',
            'tanggal' => 'nullable|date',
        ];
    }
}
