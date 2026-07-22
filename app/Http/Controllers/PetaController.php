<?php

namespace App\Http\Controllers;

use App\Models\Kecamatan;

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
