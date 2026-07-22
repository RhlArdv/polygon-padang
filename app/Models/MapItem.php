<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class MapItem extends Model
{
    protected $fillable = [
        'map_layer_id',
        'kecamatan_id',
        'judul',
        'deskripsi',
        'tipe',
        'latitude',
        'longitude',
        'polygon_coords',
        'gambar',
        'tanggal',
    ];

    protected function casts(): array
    {
        return [
            'polygon_coords' => 'array',
            'tanggal' => 'date',
            'latitude' => 'float',
            'longitude' => 'float',
        ];
    }

    /**
     * Get the layer this item belongs to.
     */
    public function mapLayer(): BelongsTo
    {
        return $this->belongsTo(MapLayer::class);
    }

    /**
     * Get the kecamatan this item belongs to.
     */
    public function kecamatans(): BelongsToMany
    {
        return $this->belongsToMany(Kecamatan::class, 'map_item_kecamatan');
    }
}
