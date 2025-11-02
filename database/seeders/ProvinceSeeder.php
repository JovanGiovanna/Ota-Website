<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Province;

class ProvinceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $provinces = [
            ['name' => 'Jakarta'],
            ['name' => 'West Java'],
            ['name' => 'East Java'],
            ['name' => 'Yogyakarta'],
            ['name' => 'Central Java'],
        ];

        foreach ($provinces as $province) {
            Province::create($province);
        }
    }
}
