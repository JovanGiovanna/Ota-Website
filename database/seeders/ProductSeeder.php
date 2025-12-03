<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use App\Models\Vendor;
use Carbon\Carbon; 

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
            'Luxury Travel', 
            'Food & Beverages',
        ])->pluck('id', 'categories')->toArray();

        $vendor = Vendor::where('email', 'vendor@test.com')->first();
        if (count($categories) < 7 || !$vendor) {
            $this->command->warn('Category atau Vendor belum ada! Pastikan CategorySeeder dan VendorSeeder dijalankan lebih dulu dan memiliki data yang sesuai.');
            $this->command->warn('Pastikan kategori Luxury Travel dan Food & Beverages sudah dibuat.');
            return;
        }

        $discountExpiresAt = Carbon::now()->addDays(30);

        $products = [
            [
                'name' => 'Comfort Travel Pillow',
                'description' => 'Bantal ergonomis untuk perjalanan yang nyaman dan dukungan leher maksimal.',
                'location' => 'Jakarta',
                'phone' => '021-1234567',
                'basic_price' => 50000, 
                'nta' => 45000,
                'tax_rate' => 11.00,
                'discount_type' => null,
                'discount_value' => null,
                'discount_expires_at' => null,
                'jumlah' => 10,
                'pax' => 1,
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
                'location' => 'Surabaya',
                'phone' => '031-9876543',
                'basic_price' => 150000,
                'nta' => 135000,
                'tax_rate' => 10.00,
                'discount_type' => 'percentage',
                'discount_value' => 15,
                'discount_expires_at' => $discountExpiresAt,
                'jumlah' => 5,
                'pax' => 1,
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
                'location' => 'Bandung',
                'phone' => '022-5555555',
                'basic_price' => 75000,
                'nta' => 70000,
                'tax_rate' => 0.00,
                'discount_type' => 'fixed',
                'discount_value' => 5000,
                'discount_expires_at' => $discountExpiresAt->copy()->addDays(60),
                'jumlah' => 15,
                'pax' => 1,
                'id_category' => $categories['Comfort Transportation'],
                'id_vendor' => $vendor->id,
                'images' => [
                    'products/placeholder-bus-1.jpg',
                ],
            ],
            [
                'name' => 'Comfortable Hotel Suite',
                'description' => 'Suite hotel yang luas dan nyaman dengan pemandangan kota, ideal untuk keluarga kecil.',
                'location' => 'Yogyakarta',
                'phone' => '0274-888888',
                'basic_price' => 300000,
                'nta' => 280000,
                'tax_rate' => 10.00,
                'discount_type' => null,
                'discount_value' => null,
                'discount_expires_at' => null,
                'jumlah' => 3,
                'pax' => 3, 
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
                'location' => 'Bali',
                'phone' => '0361-7777777',
                'basic_price' => 100000,
                'nta' => 90000,
                'tax_rate' => 11.00,
                'discount_type' => null,
                'discount_value' => null,
                'discount_expires_at' => null,
                'jumlah' => 7,
                'pax' => 1,
                'id_category' => $categories['Wellness Activities'],
                'id_vendor' => $vendor->id,
                'images' => [
                    'products/placeholder-tour-1.jpg',
                    'products/placeholder-tour-2.jpg',
                ],
            ],
            
            [
                'name' => 'All-Inclusive Luxury Package',
                'description' => 'Paket perjalanan mewah mencakup akomodasi bintang 5 dan transportasi pribadi.',
                'location' => 'Lombok',
                'phone' => '0370-6666666',
                'basic_price' => 5000000,
                'nta' => 4500000,
                'tax_rate' => 10.00,
                'discount_type' => 'percentage',
                'discount_value' => 10,
                'discount_expires_at' => $discountExpiresAt,
                'jumlah' => 2,
                'pax' => 2,
                'id_category' => $categories['Luxury Travel'],
                'id_vendor' => $vendor->id,
                'images' => [
                    'products/placeholder-luxury-1.jpg',
                ],
            ],
            
            [
                'name' => 'Gourmet Dinner Voucher',
                'description' => 'Voucher makan malam eksklusif di restoran mitra dengan menu 3-course.',
                'location' => 'Medan',
                'phone' => '061-4444444',
                'basic_price' => 250000,
                'nta' => 220000,
                'tax_rate' => 11.00,
                'discount_type' => 'fixed',
                'discount_value' => 25000,
                'discount_expires_at' => $discountExpiresAt->copy()->addDays(90),
                'jumlah' => 20,
                'pax' => 1,
                'id_category' => $categories['Food & Beverages'],
                'id_vendor' => $vendor->id,
                'images' => [
                    'products/placeholder-food-1.jpg',
                ],
            ],

        ];

        foreach ($products as $product) {
            // Hitung discount_amount
            $discountAmount = $this->calculateDiscountAmount(
                $product['basic_price'],
                $product['tax_rate'] ?? 0,
                $product['discount_type'] ?? null,
                $product['discount_value'] ?? null,
                $product['discount_expires_at'] ?? null
            );
            $product['discount_amount'] = $discountAmount;
            
            Product::create($product);
        }
    }

    /**
     * Hitung jumlah diskon berdasarkan tipe diskon.
     * Perhitungan: basic_price + (basic_price * tax_rate / 100) * discount_type
     */
    private function calculateDiscountAmount($basicPrice, $taxRate, $discountType, $discountValue, $expiresAt)
    {
        // Cek kadaluarsa diskon
        if ($expiresAt && Carbon::parse($expiresAt)->isPast()) {
            return 0.00;
        }

        // Hitung harga sebelum diskon: basic_price + (basic_price * tax_rate / 100)
        $taxAmount = $basicPrice * ($taxRate / 100);
        $priceBeforeDiscount = $basicPrice + $taxAmount;

        $discountAmount = 0.00;

        if ($discountType === 'percentage' && $discountValue > 0) {
            // Diskon persentase dari harga sebelum diskon
            $discountAmount = $priceBeforeDiscount * ($discountValue / 100);
        } elseif ($discountType === 'fixed' && $discountValue > 0) {
            // Diskon tetap
            $discountAmount = $discountValue;
        }

        // Pastikan jumlah diskon tidak menjadi negatif
        return max(0, round($discountAmount, 2));
    }
}