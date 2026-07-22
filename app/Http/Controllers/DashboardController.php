<?php

namespace App\Http\Controllers;

use App\Models\Kecamatan;
use App\Models\MapItem;
use App\Models\MapLayer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalItems = MapItem::count();
        $totalLayers = MapLayer::count();
        $totalKecamatan = Kecamatan::count();

        // 1. Data Tren (Berdasarkan Tanggal Input)
        // Ambil 14 hari terakhir yang ada datanya
        $trendData = MapItem::select(DB::raw('DATE(tanggal) as date'), DB::raw('count(*) as count'))
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->take(14)
            ->get()
            ->reverse()
            ->values();

        // 2. Data Sebaran per Layer (Doughnut Chart)
        $layerData = MapLayer::withCount('items')
            ->having('items_count', '>', 0)
            ->get(['id', 'nama', 'warna', 'items_count']);

        return view('dashboard', compact(
            'totalItems',
            'totalLayers',
            'totalKecamatan',
            'trendData',
            'layerData'
        ));
    }
}
