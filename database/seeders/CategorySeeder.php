<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Type; // Pastikan Anda mengimpor Model Type

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Ambil SEMUA data Type dan buat array map [type_name => id]
        // Asumsikan kolom nama di tabel 'types' adalah 'name'.
        $types = Type::pluck('id', 'type')->toArray(); 
        
        // Cek apakah data Type sudah ada
        if (empty($types)) {
            $this->command->warn('⚠️ Seeder dihentikan: Tabel types kosong. Pastikan TypeSeeder sudah dijalankan.');
            return;
        }

        $categoriesData = [
            // Ganti 'type_name' menjadi 'type_id' untuk memudahkan mapping
            ['categories' => 'Comfort Accessories',      'type_id_name' => 'Basic'],
            ['categories' => 'Relaxation Services',      'type_id_name' => 'Basic'],
            ['categories' => 'Comfort Transportation',   'type_id_name' => 'Premium'],
            ['categories' => 'Comfort Accommodation',    'type_id_name' => 'Premium'],
            ['categories' => 'Wellness Activities',      'type_id_name' => 'Wellness'],
        ];

        foreach ($categoriesData as $categoryData) {
            $typeName = $categoryData['type_id_name'];

            // 2. Ambil ID Type berdasarkan nama (key)
            $typeId = $types[$typeName] ?? null;

            // 3. Hanya buat kategori jika ID Type ditemukan
            if ($typeId) {
                // Hapus key type_id_name
                unset($categoryData['type_id_name']);
                
                // Tambahkan foreign key id_type
                $categoryData['id_type'] = $typeId; 

                // Lakukan proses seeding (misalnya menggunakan updateOrCreate atau create)
                Category::updateOrCreate(
                    ['categories' => $categoryData['categories']],
                    $categoryData
                );
            } else {
                 $this->command->warn("Nama Type '{$typeName}' tidak ditemukan di tabel types.");
            }
        }
    }
}