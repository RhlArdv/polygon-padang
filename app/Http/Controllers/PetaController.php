<?php

namespace App\Http\Controllers;

use App\Models\Kecamatan;
use App\Models\MapItem;

class PetaController extends Controller
{
    /**
     * Display the map page (public, no auth required).
     */
    public function index()
    {
        return view('peta');
    }

    /**
     * Display item detail page.
     */
    public function show(MapItem $mapItem)
    {
        $mapItem->load('mapLayer', 'kecamatans');
        return view('item-detail', compact('mapItem'));
    }

    /**
     * Return all kecamatan data as JSON.
     */
    public function apiKecamatan()
    {
        $kecamatans = Kecamatan::select('id', 'district_code', 'nama_kecamatan')
            ->orderBy('nama_kecamatan')
            ->get();

        return response()->json($kecamatans);
    }
}
