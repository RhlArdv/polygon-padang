<?php

use App\Http\Controllers\LandingController;
use App\Http\Controllers\MapItemController;
use App\Http\Controllers\MapLayerController;
use App\Http\Controllers\PetaController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

// Peta publik (Landing Page)
Route::get('/', [LandingController::class, 'index'])->name('landing');

// API publik (load data untuk Leaflet)
Route::get('/api/layers', [MapLayerController::class, 'apiIndex'])->name('api.layers.index');
Route::get('/api/kecamatan', [PetaController::class, 'apiKecamatan'])->name('api.kecamatan.index');

// Area Admin
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/admin/peta', [PetaController::class, 'index'])->name('peta.index');
    
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Map layers
    Route::post('/layers', [MapLayerController::class, 'store'])->name('layers.store');
    Route::put('/layers/{mapLayer}', [MapLayerController::class, 'update'])->name('layers.update');
    Route::delete('/layers/{mapLayer}', [MapLayerController::class, 'destroy'])->name('layers.destroy');

    // Map items
    Route::post('/items', [MapItemController::class, 'store'])->name('items.store');
    Route::put('/items/{mapItem}', [MapItemController::class, 'update'])->name('items.update');
    Route::delete('/items/{mapItem}', [MapItemController::class, 'destroy'])->name('items.destroy');
});

require __DIR__.'/auth.php';
