<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use App\Models\Vendor;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil kategori berdasarkan nama
        $comfortAccessories = Category::where('categories', 'Comfort Accessories')->first();
        $relaxationServices = Category::where('categories', 'Relaxation Services')->first();
        $comfortTransportation = Category::where('categories', 'Comfort Transportation')->first();
        $comfortAccommodation = Category::where('categories', 'Comfort Accommodation')->first();
        $wellnessActivities = Category::where('categories', 'Wellness Activities')->first();

        // Ambil vendor pertama (pastikan VendorSeeder sudah jalan duluan)
        $vendor = Vendor::first();

        // Kalau salah satu kategori tidak ditemukan, tampilkan warning
        if (!$comfortAccessories || !$vendor) {
            $this->command->warn('⚠️ Category atau Vendor belum ada! Pastikan CategorySeeder dan VendorSeeder dijalankan lebih dulu.');
            return;
        }

        $products = [
            [
                'name' => 'Comfort Travel Pillow',
                'description' => 'Ergonomic pillow for comfortable travel',
                'price' => 50000,
                'jumlah' => 10,
                'id_category' => $comfortAccessories->id,
                'id_vendor' => $vendor->id,
            ],
            [
                'name' => 'Luxury Spa Session',
                'description' => 'Relaxing spa treatment for ultimate comfort',
                'price' => 150000,
                'jumlah' => 5,
                'id_category' => $relaxationServices->id,
                'id_vendor' => $vendor->id,
            ],
            [
                'name' => 'Premium Bus Service',
                'description' => 'Comfortable and luxurious bus transportation',
                'price' => 75000,
                'jumlah' => 15,
                'id_category' => $comfortTransportation->id,
                'id_vendor' => $vendor->id,
            ],
            [
                'name' => 'Comfortable Hotel Suite',
                'description' => 'Spacious and comfortable hotel suite',
                'price' => 300000,
                'jumlah' => 3,
                'id_category' => $comfortAccommodation->id,
                'id_vendor' => $vendor->id,
            ],
            [
                'name' => 'Guided Relaxation Tour',
                'description' => 'Guided tour focused on comfort and relaxation',
                'price' => 100000,
                'jumlah' => 7,
                'id_category' => $wellnessActivities->id,
                'id_vendor' => $vendor->id,
            ],
        ];

        foreach ($products as $product) {
            Product::create(array_merge($product, ['id' => Str::uuid()]));
        }
    }
}
