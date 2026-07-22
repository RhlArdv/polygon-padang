<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMapItemRequest;
use App\Http\Requests\UpdateMapItemRequest;
use App\Models\MapItem;
use Illuminate\Http\JsonResponse;

class MapItemController extends Controller
{
    /**
     * Store a new map item.
     */
    public function store(StoreMapItemRequest $request): JsonResponse
    {
        $data = $request->validated();
        if ($request->hasFile('gambar')) {
            $data['gambar'] = $request->file('gambar')->store('map_images', 'public');
        }

        $item = MapItem::create($data);
        $item->load('mapLayer', 'kecamatan:id,nama_kecamatan');

        return response()->json($item, 201);
    }

    /**
     * Update an existing map item.
     */
    public function update(UpdateMapItemRequest $request, MapItem $mapItem): JsonResponse
    {
        $data = $request->validated();
        if ($request->hasFile('gambar')) {
            if ($mapItem->gambar && \Illuminate\Support\Facades\Storage::disk('public')->exists($mapItem->gambar)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($mapItem->gambar);
            }
            $data['gambar'] = $request->file('gambar')->store('map_images', 'public');
        }

        $mapItem->update($data);
        $mapItem->load('mapLayer', 'kecamatan:id,nama_kecamatan');

        return response()->json($mapItem);
    }

    /**
     * Delete a map item.
     */
    public function destroy(MapItem $mapItem): JsonResponse
    {
        $mapItem->delete();

        return response()->json(['message' => 'Item berhasil dihapus.']);
    }
}
