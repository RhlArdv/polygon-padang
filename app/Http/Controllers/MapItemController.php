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
        $item = MapItem::create($request->validated());
        $item->load('mapLayer', 'kecamatan:id,nama_kecamatan');

        return response()->json($item, 201);
    }

    /**
     * Update an existing map item.
     */
    public function update(UpdateMapItemRequest $request, MapItem $mapItem): JsonResponse
    {
        $mapItem->update($request->validated());
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
