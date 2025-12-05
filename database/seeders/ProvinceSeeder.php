<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Province;

class ProvinceSeeder extends Seeder
{
    public function run(): void
    {
        $provinces = [
            ['id' => 1, 'name' => 'Aceh'],
            ['id' => 2, 'name' => 'Sumatera Utara'],
            ['id' => 3, 'name' => 'Sumatera Barat'],
            ['id' => 4, 'name' => 'Riau'],
            ['id' => 5, 'name' => 'Kepulauan Riau'],
            ['id' => 6, 'name' => 'Jambi'],
            ['id' => 7, 'name' => 'Sumatera Selatan'],
            ['id' => 8, 'name' => 'Bangka Belitung'],
            ['id' => 9, 'name' => 'Bengkulu'],
            ['id' => 10, 'name' => 'Lampung'],
            ['id' => 11, 'name' => 'DKI Jakarta'],
            ['id' => 12, 'name' => 'Jawa Barat'],
            ['id' => 13, 'name' => 'Banten'],
            ['id' => 14, 'name' => 'Jawa Tengah'],
            ['id' => 15, 'name' => 'DI Yogyakarta'],
            ['id' => 16, 'name' => 'Jawa Timur'],
            ['id' => 17, 'name' => 'Bali'],
            ['id' => 18, 'name' => 'Nusa Tenggara Barat'],
            ['id' => 19, 'name' => 'Nusa Tenggara Timur'],
            ['id' => 20, 'name' => 'Kalimantan Barat'],
            ['id' => 21, 'name' => 'Kalimantan Tengah'],
            ['id' => 22, 'name' => 'Kalimantan Selatan'],
            ['id' => 23, 'name' => 'Kalimantan Timur'],
            ['id' => 24, 'name' => 'Kalimantan Utara'],
            ['id' => 25, 'name' => 'Sulawesi Utara'],
            ['id' => 26, 'name' => 'Sulawesi Tengah'],
            ['id' => 27, 'name' => 'Sulawesi Selatan'],
            ['id' => 28, 'name' => 'Sulawesi Tenggara'],
            ['id' => 29, 'name' => 'Gorontalo'],
            ['id' => 30, 'name' => 'Sulawesi Barat'],
            ['id' => 31, 'name' => 'Maluku'],
            ['id' => 32, 'name' => 'Maluku Utara'],
            ['id' => 33, 'name' => 'Papua Barat'],
            ['id' => 34, 'name' => 'Papua Barat Daya'],
            ['id' => 35, 'name' => 'Papua'],
            ['id' => 36, 'name' => 'Papua Pegunungan'],
            ['id' => 37, 'name' => 'Papua Tengah'],
            ['id' => 38, 'name' => 'Papua Selatan'],
        ];

        Province::insert($provinces);
    }
}
