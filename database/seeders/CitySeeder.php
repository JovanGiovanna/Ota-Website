<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\City;
use Illuminate\Support\Str;

class CitySeeder extends Seeder
{
    public function run(): void
    {
        $cities = json_decode(
            file_get_contents(database_path('seeders/data/cities.json')),
            true
        );

        // Ambil mapping UUID province
        $provinceUuids = cache('province_uuids', []);

        // Generate UUID untuk setiap city dan update id_province ke UUID
        foreach ($cities as $city) {
            $provinceId = $city['id_province'];
            
            // Jika mapping ada, gunakan UUID; jika tidak, generate baru
            $provinceUuid = $provinceUuids[$provinceId] ?? Str::uuid()->toString();

            City::create([
                'id' => Str::uuid()->toString(),
                'name' => $city['name'],
                'id_province' => $provinceUuid,
            ]);
        }
    }
}
