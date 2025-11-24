<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use App\Models\Vendor;
use Carbon\Carbon; // Digunakan untuk menghitung tanggal kadaluarsa diskon

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $categories = Category::whereIn('categories', [
            'Comfort Accessories',
            'Relaxation Services',
            'Comfort Transportation',
            'Comfort Accommodation',
            'Wellness Activities',
            'Luxury Travel', // Kategori tambahan
            'Food & Beverages', // Kategori tambahan
        ])->pluck('id', 'categories')->toArray();

        // Ambil vendor pertama atau buat vendor jika belum ada (hanya sebagai fallback)
        $vendor = Vendor::where('email', 'vendor@test.com')->first();
        // Perluas cek untuk memastikan kategori yang akan digunakan ada
        if (count($categories) < 7 || !$vendor) {
            $this->command->warn('Category atau Vendor belum ada! Pastikan CategorySeeder dan VendorSeeder dijalankan lebih dulu dan memiliki data yang sesuai.');
            $this->command->warn('Pastikan kategori Luxury Travel dan Food & Beverages sudah dibuat.');
            return;
        }

        // Tanggal kadaluarsa diskon (misalnya, 30 hari dari sekarang)
        $discountExpiresAt = Carbon::now()->addDays(30);

        $products = [
            [
                'name' => 'Comfort Travel Pillow',
                'description' => 'Bantal ergonomis untuk perjalanan yang nyaman dan dukungan leher maksimal.',
                // Perubahan Harga
                'basic_price' => 50000, 
                'nta' => 45000, // Contoh NTA
                'tax_rate' => 11.00, // Contoh Tax Rate 11%
                
                // DISKON: TIDAK ADA
                'discount_type' => null,
                'discount_value' => null,
                'discount_expires_at' => null,
                // ---

                'jumlah' => 10,
                'pax' => 1,
                'max_adults' => 1,
                'max_children' => 0,
                'id_category' => $categories['Comfort Accessories'],
                'id_vendor' => $vendor->id,
                'images' => [
                    'products/placeholder-pillow-1.jpg',
                    'products/placeholder-pillow-2.jpg',
                ],
            ],
            [
                'name' => 'Luxury Spa Session',
                'description' => 'Perawatan spa relaksasi selama 90 menit untuk kenyamanan total, termasuk pijat aromaterapi.',
                // Perubahan Harga
                'basic_price' => 150000,
                'nta' => 135000,
                'tax_rate' => 10.00,
                
                // DISKON: Persentase 15%
                'discount_type' => 'percentage',
                'discount_value' => 15,
                'discount_expires_at' => $discountExpiresAt, // Diskon berlaku 30 hari
                // ---
                
                'jumlah' => 5,
                'pax' => 1,
                'max_adults' => 1,
                'max_children' => 0,
                'id_category' => $categories['Relaxation Services'],
                'id_vendor' => $vendor->id,
                'images' => [
                    'products/placeholder-spa-1.jpg',
                    'products/placeholder-spa-2.jpg',
                    'products/placeholder-spa-3.jpg',
                ],
            ],
            [
                'name' => 'Premium Bus Service',
                'description' => 'Layanan transportasi bus mewah dengan kursi yang dapat direbahkan dan Wi-Fi cepat.',
                // Perubahan Harga
                'basic_price' => 75000,
                'nta' => 70000,
                'tax_rate' => 0.00,
                
                // DISKON: Harga Tetap Rp 5.000
                'discount_type' => 'fixed',
                'discount_value' => 5000,
                'discount_expires_at' => $discountExpiresAt->copy()->addDays(60), // Diskon berlaku 60 hari
                // ---

                'jumlah' => 15,
                'pax' => 1,
                'max_adults' => 1,
                'max_children' => 0,
                'id_category' => $categories['Comfort Transportation'],
                'id_vendor' => $vendor->id,
                'images' => [
                    'products/placeholder-bus-1.jpg',
                ],
            ],
            [
                'name' => 'Comfortable Hotel Suite',
                'description' => 'Suite hotel yang luas dan nyaman dengan pemandangan kota, ideal untuk keluarga kecil.',
                // Perubahan Harga
                'basic_price' => 300000,
                'nta' => 280000,
                'tax_rate' => 10.00,
                
                // DISKON: TIDAK ADA
                'discount_type' => null,
                'discount_value' => null,
                'discount_expires_at' => null,
                // ---

                'jumlah' => 3,
                'pax' => 3, 
                'max_adults' => 2,
                'max_children' => 1,
                'id_category' => $categories['Comfort Accommodation'],
                'id_vendor' => $vendor->id,
                'images' => [
                    'products/placeholder-hotel-1.jpg',
                    'products/placeholder-hotel-2.jpg',
                    'products/placeholder-hotel-3.jpg',
                ],
            ],
            [
                'name' => 'Guided Relaxation Tour',
                'description' => 'Tur berpemandu yang berfokus pada meditasi dan relaksasi di alam terbuka.',
                // Perubahan Harga
                'basic_price' => 100000,
                'nta' => 90000,
                'tax_rate' => 11.00,
                
                // DISKON: TIDAK ADA
                'discount_type' => null,
                'discount_value' => null,
                'discount_expires_at' => null,
                // ---

                'jumlah' => 7,
                'pax' => 1,
                'max_adults' => 1,
                'max_children' => 0,
                'id_category' => $categories['Wellness Activities'],
                'id_vendor' => $vendor->id,
                'images' => [
                    'products/placeholder-tour-1.jpg',
                    'products/placeholder-tour-2.jpg',
                ],
            ],
            
            // --- PRODUK BARU DENGAN DISKON KHUSUS ---

            [
                'name' => 'All-Inclusive Luxury Package',
                'description' => 'Paket perjalanan mewah mencakup akomodasi bintang 5 dan transportasi pribadi.',
                'basic_price' => 5000000,
                'nta' => 4500000,
                'tax_rate' => 10.00,
                
                // DISKON: Persentase 10%
                'discount_type' => 'percentage',
                'discount_value' => 10,
                'discount_expires_at' => $discountExpiresAt,
                
                'jumlah' => 2,
                'pax' => 2,
                'max_adults' => 2,
                'max_children' => 2,
                'id_category' => $categories['Luxury Travel'],
                'id_vendor' => $vendor->id,
                'images' => [
                    'products/placeholder-luxury-1.jpg',
                ],
            ],
            
            [
                'name' => 'Gourmet Dinner Voucher',
                'description' => 'Voucher makan malam eksklusif di restoran mitra dengan menu 3-course.',
                'basic_price' => 250000,
                'nta' => 220000,
                'tax_rate' => 11.00,
                
                // DISKON: Harga Tetap Rp 25.000
                'discount_type' => 'fixed',
                'discount_value' => 25000,
                'discount_expires_at' => $discountExpiresAt->copy()->addDays(90),
                
                'jumlah' => 20,
                'pax' => 1,
                'max_adults' => 1,
                'max_children' => 0,
                'id_category' => $categories['Food & Beverages'],
                'id_vendor' => $vendor->id,
                'images' => [
                    'products/placeholder-food-1.jpg',
                ],
            ],

        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}