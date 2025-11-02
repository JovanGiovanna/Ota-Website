<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Type; // pastikan ada model Type
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil semua type yang sudah ada
        $types = Type::all()->keyBy('type'); // pastikan 'type' ada di tabel types

        // Kalau kamu punya data types seperti: Basic, Premium, Wellness, dll.
        // Sesuaikan di bawah ini dengan nama type sebenarnya.
        $categories = [
            ['categories' => 'Comfort Accessories', 'type_name' => 'Basic'],
            ['categories' => 'Relaxation Services', 'type_name' => 'Basic'],
            ['categories' => 'Comfort Transportation', 'type_name' => 'Premium'],
            ['categories' => 'Comfort Accommodation', 'type_name' => 'Premium'],
            ['categories' => 'Wellness Activities', 'type_name' => 'Wellness'],
        ];

        foreach ($categories as $data) {
            $type = $types->get($data['type_name']);

            if ($type) {
                Category::updateOrCreate(
                    ['categories' => $data['categories']],
                    [
                        'id' => Str::uuid(),
                        'id_type' => $type->id, // ambil UUID dari tabel types
                    ]
                );
            } else {
                echo "⚠️ Type '{$data['type_name']}' tidak ditemukan. Pastikan tabel 'types' sudah di-seed.\n";
            }
        }
    }
}
