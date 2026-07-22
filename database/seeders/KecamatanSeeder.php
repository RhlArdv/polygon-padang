<?php

namespace Database\Seeders;

use App\Models\Kecamatan;
use Illuminate\Database\Seeder;

class KecamatanSeeder extends Seeder
{
    /**
     * Seed the kecamatans table with 11 districts of Kota Padang.
     */
    public function run(): void
    {
        $kecamatans = [
            ['district_code' => 'id1371010', 'nama_kecamatan' => 'Bungus Teluk Kabung'],
            ['district_code' => 'id1371020', 'nama_kecamatan' => 'Lubuk Kilangan'],
            ['district_code' => 'id1371030', 'nama_kecamatan' => 'Lubuk Begalung'],
            ['district_code' => 'id1371040', 'nama_kecamatan' => 'Padang Selatan'],
            ['district_code' => 'id1371050', 'nama_kecamatan' => 'Padang Timur'],
            ['district_code' => 'id1371060', 'nama_kecamatan' => 'Padang Barat'],
            ['district_code' => 'id1371070', 'nama_kecamatan' => 'Padang Utara'],
            ['district_code' => 'id1371080', 'nama_kecamatan' => 'Nanggalo'],
            ['district_code' => 'id1371090', 'nama_kecamatan' => 'Kuranji'],
            ['district_code' => 'id1371100', 'nama_kecamatan' => 'Pauh'],
            ['district_code' => 'id1371110', 'nama_kecamatan' => 'Koto Tangah'],
        ];

        foreach ($kecamatans as $kecamatan) {
            Kecamatan::updateOrCreate(
                ['district_code' => $kecamatan['district_code']],
                $kecamatan
            );
        }
    }
}
