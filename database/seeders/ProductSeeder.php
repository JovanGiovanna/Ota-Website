<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use App\Models\Vendor;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil ID kategori berdasarkan nama (menggunakan pluck untuk efisiensi)
        $categories = Category::whereIn('categories', [
            'Comfort Accessories',
            'Relaxation Services',
            'Comfort Transportation',
            'Comfort Accommodation',
            'Wellness Activities',
        ])->pluck('id', 'categories')->toArray();

        // Ambil vendor pertama (pastikan VendorSeeder sudah jalan duluan)
        $vendor = Vendor::first();

        // Cek jika kategori atau vendor tidak ditemukan
        if (count($categories) < 5 || !$vendor) {
            $this->command->warn('⚠️ Category atau Vendor belum ada! Pastikan CategorySeeder dan VendorSeeder dijalankan lebih dulu dan memiliki data yang sesuai.');
            return;
        }

        $products = [
            [
                'name' => 'Comfort Travel Pillow',
                'description' => 'Bantal ergonomis untuk perjalanan yang nyaman dan dukungan leher maksimal.',
                'price' => 50000,
                'jumlah' => 10,
                'pax' => 1,                 // Kapasitas per unit
                'max_adults' => 1,          // Kapasitas dewasa
                'max_children' => 0,        // Kapasitas anak
                'id_category' => $categories['Comfort Accessories'],
                'id_vendor' => $vendor->id,
            ],
            [
                'name' => 'Luxury Spa Session',
                'description' => 'Perawatan spa relaksasi selama 90 menit untuk kenyamanan total, termasuk pijat aromaterapi.',
                'price' => 150000,
                'jumlah' => 5,
                'pax' => 1,
                'max_adults' => 1,
                'max_children' => 0,
                'id_category' => $categories['Relaxation Services'],
                'id_vendor' => $vendor->id,
            ],
            [
                'name' => 'Premium Bus Service',
                'description' => 'Layanan transportasi bus mewah dengan kursi yang dapat direbahkan dan Wi-Fi cepat.',
                'price' => 75000,
                'jumlah' => 15,
                'pax' => 1,
                'max_adults' => 1,
                'max_children' => 0,
                'id_category' => $categories['Comfort Transportation'],
                'id_vendor' => $vendor->id,
            ],
            [
                'name' => 'Comfortable Hotel Suite',
                'description' => 'Suite hotel yang luas dan nyaman dengan pemandangan kota, ideal untuk keluarga kecil.',
                'price' => 300000,
                'jumlah' => 3,
                'pax' => 3, // 2 dewasa + 1 anak
                'max_adults' => 2,
                'max_children' => 1,
                'id_category' => $categories['Comfort Accommodation'],
                'id_vendor' => $vendor->id,
            ],
            [
                'name' => 'Guided Relaxation Tour',
                'description' => 'Tur berpemandu yang berfokus pada meditasi dan relaksasi di alam terbuka.',
                'price' => 100000,
                'jumlah' => 7,
                'pax' => 1,
                'max_adults' => 1,
                'max_children' => 0,
                'id_category' => $categories['Wellness Activities'],
                'id_vendor' => $vendor->id,
            ],
        ];

        foreach ($products as $product) {
            // Karena Model Product menggunakan HasUuids, create() akan otomatis mengisi ID
            Product::create($product);
        }
    }
}