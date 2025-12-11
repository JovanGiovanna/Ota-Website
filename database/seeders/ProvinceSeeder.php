<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Province;
use Illuminate\Support\Str;

class ProvinceSeeder extends Seeder
{
    public function run(): void
    {
        $provinceNames = [
            1 => 'Aceh',
            2 => 'Sumatera Utara',
            3 => 'Sumatera Barat',
            4 => 'Riau',
            5 => 'Kepulauan Riau',
            6 => 'Jambi',
            7 => 'Sumatera Selatan',
            8 => 'Bangka Belitung',
            9 => 'Bengkulu',
            10 => 'Lampung',
            11 => 'DKI Jakarta',
            12 => 'Jawa Barat',
            13 => 'Banten',
            14 => 'Jawa Tengah',
            15 => 'DI Yogyakarta',
            16 => 'Jawa Timur',
            17 => 'Bali',
            18 => 'Nusa Tenggara Barat',
            19 => 'Nusa Tenggara Timur',
            20 => 'Kalimantan Barat',
            21 => 'Kalimantan Tengah',
            22 => 'Kalimantan Selatan',
            23 => 'Kalimantan Timur',
            24 => 'Kalimantan Utara',
            25 => 'Sulawesi Utara',
            26 => 'Sulawesi Tengah',
            27 => 'Sulawesi Selatan',
            28 => 'Sulawesi Tenggara',
            29 => 'Gorontalo',
            30 => 'Sulawesi Barat',
            31 => 'Maluku',
            32 => 'Maluku Utara',
            33 => 'Papua Barat',
            34 => 'Papua Barat Daya',
            35 => 'Papua',
            36 => 'Papua Pegunungan',
            37 => 'Papua Tengah',
            38 => 'Papua Selatan',
        ];

        // Generate UUID mapping untuk setiap province ID
        $provinceUuids = [];
        foreach ($provinceNames as $id => $name) {
            $provinceUuids[$id] = Str::uuid()->toString();
            Province::create([
                'id' => $provinceUuids[$id],
                'name' => $name,
            ]);
        }

        // Simpan mapping ke cache untuk digunakan di CitySeeder
        cache()->put('province_uuids', $provinceUuids, now()->addHours(1));
    }
}
