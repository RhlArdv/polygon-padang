<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kecamatan extends Model
{
    protected $fillable = [
        'district_code',
        'nama_kecamatan',
    ];

    /**
     * Get all map items in this kecamatan.
     */
    public function mapItems(): HasMany
    {
        return $this->hasMany(MapItem::class);
    }
}
