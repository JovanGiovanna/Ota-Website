<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\City;

class CitySeeder extends Seeder
{
    public function run(): void
    {
        $cities = json_decode(
            file_get_contents(database_path('seeders/data/cities.json')),
            true
        );

        City::insert($cities);
    }
}
