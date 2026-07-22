<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MapLayer extends Model
{
    protected $fillable = [
        'nama',
        'deskripsi',
        'tipe',
        'warna',
        'ikon',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get all items in this layer.
     */
    public function items(): HasMany
    {
        return $this->hasMany(MapItem::class);
    }
}
