<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMapLayerRequest;
use App\Models\MapLayer;
use Illuminate\Http\JsonResponse;

class MapLayerController extends Controller
{
    /**
     * View for admin layer management.
     */
    public function index()
    {
        return view('layer');
    }

    /**
     * Return all active layers with their items (public API).
     */
    public function apiIndex(): JsonResponse
    {
        $layers = MapLayer::where('is_active', true)
            ->with(['items.kecamatans:id,nama_kecamatan'])
            ->get();

        return response()->json($layers);
    }

    /**
     * Store a new map layer.
     */
    public function store(StoreMapLayerRequest $request): JsonResponse
    {
        $layer = MapLayer::create($request->validated());

        return response()->json($layer, 201);
    }

    /**
     * Update an existing map layer.
     */
    public function update(StoreMapLayerRequest $request, MapLayer $mapLayer): JsonResponse
    {
        $mapLayer->update($request->validated());

        return response()->json($mapLayer);
    }

    /**
     * Delete a map layer and all its items.
     */
    public function destroy(MapLayer $mapLayer): JsonResponse
    {
        $mapLayer->delete();

        return response()->json(['message' => 'Layer berhasil dihapus.']);
    }
}
