<?php
namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Package;
use App\Models\Product;
use App\Models\Addon;
use Illuminate\Support\Str;

class PackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // --- 1. Ambil Data Product & Addon ---
        $productPillow = Product::where('name', trim('Comfort Travel Pillow'))->first();
        $productSpa = Product::where('name', trim('Luxury Spa Session'))->first();
        $productBus = Product::where('name', trim('Premium Bus Service'))->first();
        
        $addonEyeMask = Addon::where('addons', trim('Comfort Eye Mask'))->first();
        $addonNeckPillow = Addon::where('addons', trim('Travel Neck Pillow'))->first();
        $addonSeatUpgrade = Addon::where('addons', trim('Premium Seat Upgrade'))->first(); 
        
        // Cek jika data yang dibutuhkan belum ada
        if (!$productPillow || !$addonEyeMask || !$productBus || !$productSpa || !$addonNeckPillow || !$addonSeatUpgrade) {
             $this->command->error('❌ Gagal menjalankan PackageSeeder!');
             $this->command->warn('⚠️ Sebagian data Product/Addon tidak ditemukan.');
             $this->command->info('Pastikan ProductSeeder dan AddonSeeder sudah dijalankan dengan nama item yang sesuai.');
             return;
        }

        // --- 2. Fungsi Pembantu untuk Format Data JSON ---
        
        $formatProduct = function($product, $pax = 1) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'pax' => $pax,
                'sub_total' => $product->price * $pax,
            ];
        };

        $formatAddon = function($addon, $pax = 1) {
            return [
                'id' => $addon->id,
                'name' => $addon->addons, 
                'price' => $addon->price,
                'pax' => $pax,
                'sub_total' => $addon->price * $pax,
            ];
        };
        
        $defaultImages = []; // Ganti dengan array of strings jika Anda memiliki path gambar dummy: ['path/to/img1.jpg', 'path/to/img2.jpg']

        // --- 3. Isi Tabel Packages (Menggunakan JSON Data) ---

        // Paket 1: Comfort Travel Kit Plus (Pillow + Eye Mask) - Diskon 0%
        $pax1 = 2;
        $discount1 = 0; 
        $productsData1 = [$formatProduct($productPillow, $pax1)];
        $addonsData1 = [$formatAddon($addonEyeMask, $pax1)];
        $priceReal1 = array_sum(array_column($productsData1, 'sub_total')) + 
                      array_sum(array_column($addonsData1, 'sub_total'));
        $pricePublish1 = round($priceReal1 * (1 - $discount1 / 100)); // Pembulatan

        Package::updateOrCreate(
            [
                'slug' => 'comfort-travel-kit-plus',
            ],
            [
                'name_package' => 'Comfort Travel Kit Plus',
                'description' => 'Kombinasi bantal dan masker mata untuk perjalanan jarak jauh yang super nyaman.',
                'price_publish' => $pricePublish1, 
                'price_real' => $priceReal1,
                'discount_percentage' => $discount1, 
                'start_publish' => now()->subDay(),
                'end_publish' => now()->addMonths(6),
                'is_active' => true,
                'images' => $defaultImages, 
                'products_data' => $productsData1,
                'addons_data' => $addonsData1,
            ]
        );

        // Paket 2: Ultimate Relaxation Bundle (Spa Session + Neck Pillow) - Diskon 15%
        $pax2 = 1;
        $discount2 = 15; 
        $productsData2 = [$formatProduct($productSpa, $pax2)];
        $addonsData2 = [$formatAddon($addonNeckPillow, $pax2)];
        $priceReal2 = array_sum(array_column($productsData2, 'sub_total')) + 
                      array_sum(array_column($addonsData2, 'sub_total'));
        $pricePublish2 = round($priceReal2 * (1 - $discount2 / 100)); 
        
        Package::updateOrCreate(
            [
                'slug' => 'ultimate-relaxation-bundle',
            ],
            [
                'name_package' => 'Ultimate Relaxation Bundle',
                'description' => 'Sesi spa mewah ditambah bantal leher untuk pengalaman relaksasi total.',
                'price_publish' => $pricePublish2, 
                'price_real' => $priceReal2,
                'discount_percentage' => $discount2, 
                'start_publish' => now()->subWeek(),
                'end_publish' => now()->addYear(),
                'is_active' => true,
                'images' => $defaultImages, 
                'products_data' => $productsData2,
                'addons_data' => $addonsData2,
            ]
        );
        
        // Paket 3: VIP Commute Experience (Bus Service + Seat Upgrade) - Diskon 10%
        $pax3 = 3;
        $discount3 = 10; 
        $productsData3 = [$formatProduct($productBus, $pax3)];
        $addonsData3 = [$formatAddon($addonSeatUpgrade, $pax3)];
        $priceReal3 = array_sum(array_column($productsData3, 'sub_total')) + 
                      array_sum(array_column($addonsData3, 'sub_total'));
        $pricePublish3 = round($priceReal3 * (1 - $discount3 / 100));

        Package::updateOrCreate(
            [
                'slug' => 'vip-commute-experience',
            ],
            [
                'name_package' => 'VIP Commute Experience',
                'description' => 'Layanan bus premium dengan jaminan peningkatan kursi (seat upgrade) untuk kenyamanan maksimal.',
                'price_publish' => $pricePublish3, 
                'price_real' => $priceReal3,
                'discount_percentage' => $discount3, 
                'start_publish' => now(),
                'end_publish' => now()->addMonths(3),
                'is_active' => true,
                // MODIFIKASI: Mengganti 'image' menjadi 'images'
                'images' => $defaultImages, 
                'products_data' => $productsData3,
                'addons_data' => $addonsData3,
            ]
        );
        
        $this->command->info('✅ PackageSeeder berhasil dijalankan menggunakan kolom JSON dan Discount Percentage.');
    }
}