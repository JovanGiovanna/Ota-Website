<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\City;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $provinces = \App\Models\Province::all();

        $cities = [
            ['name' => 'Jakarta', 'id_province' => $provinces->where('name', 'Jakarta')->first()->id],
            ['name' => 'Bandung', 'id_province' => $provinces->where('name', 'West Java')->first()->id],
            ['name' => 'Surabaya', 'id_province' => $provinces->where('name', 'East Java')->first()->id],
            ['name' => 'Yogyakarta', 'id_province' => $provinces->where('name', 'Yogyakarta')->first()->id],
            ['name' => 'Semarang', 'id_province' => $provinces->where('name', 'Central Java')->first()->id],
        ];

        foreach ($cities as $city) {
            City::create($city);
        }
    }
}
