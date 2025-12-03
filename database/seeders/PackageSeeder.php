<?php
namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Package;
use App\Models\Product;
use App\Models\Addon;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        
        $productPillow = Product::where('name', trim('Comfort Travel Pillow'))->first();
        $productSpa = Product::where('name', trim('Luxury Spa Session'))->first();
        $productBus = Product::where('name', trim('Premium Bus Service'))->first();
        
        $addonEyeMask = Addon::where('addons', trim('Comfort Eye Mask'))->first();
        $addonNeckPillow = Addon::where('addons', trim('Travel Neck Pillow'))->first();
        $addonSeatUpgrade = Addon::where('addons', trim('Premium Seat Upgrade'))->first(); 
        
        if (!$productPillow || !$addonEyeMask || !$productBus || !$productSpa || !$addonNeckPillow || !$addonSeatUpgrade) {
             $this->command->error('❌ Gagal menjalankan PackageSeeder!');
             $this->command->warn('⚠️ Sebagian data Product/Addon tidak ditemukan.');
             $this->command->info('Pastikan ProductSeeder dan AddonSeeder sudah dijalankan dengan nama item yang sesuai.');
             return;
        }

        
        $formatProduct = function($product, $pax = 1) {
            $unitPrice = $product->nta ?? ($product->basic_price ?? 0); 
            
            return [
                'id' => $product->id,
                'name' => $product->name,
                'description' => $product->description,
                'images' => $product->images ?? ['products/placeholder.jpg'],
                'nta' => $unitPrice, 
                'pax' => $pax,
                'sub_total' => $unitPrice * $pax,
            ];
        };

        $formatAddon = function($addon, $pax = 1) {
            $unitPrice = $addon->nta ?? ($addon->basic_price ?? 0);

            return [
                'id' => $addon->id,
                'name' => $addon->addons, // Mengubah 'addons' menjadi 'name' untuk konsistensi
                'desc' => $addon->desc,
                'images' => $addon->images ?? ['addons/placeholder.jpg'],
                'nta' => $unitPrice, 
                'pax' => $pax,
                'sub_total' => $unitPrice * $pax,
            ];
        };
        
        $defaultImages = ['packages/placeholder-package-1.jpg', 'packages/placeholder-package-2.jpg']; 

        
        $pax1 = 2;
        $productsData1 = [$formatProduct($productPillow, $pax1)];
        $addonsData1 = [$formatAddon($addonEyeMask, $pax1)];

        $nta1 = array_sum(array_column($productsData1, 'sub_total')) + 
                         array_sum(array_column($addonsData1, 'sub_total'));
        
        $priceTotalPublish1 = $nta1; // Total Harga Jual = NTA
        $paxPaid1 = round($priceTotalPublish1 / $pax1); // Harga per Pax

        Package::updateOrCreate(
            [
                'slug' => 'comfort-travel-kit-plus',
            ],
            [
                'name_package' => 'Comfort Travel Kit Plus',
                'description' => 'Kombinasi bantal dan masker mata untuk perjalanan jarak jauh yang super nyaman. Harga dihitung dari NTA.',
                'location' => 'Jakarta',
                'phone' => '021-1234567',
                'nta' => $nta1, 
                'pax_paid' => $paxPaid1, 
                'start_publish' => now()->subDay(),
                'end_publish' => now()->addMonths(6),
                'is_active' => true,
                'images' => $defaultImages, 
                'products_data' => $productsData1, 
                'addons_data' => $addonsData1,
            ]
        );

        // Paket 2: Ultimate Relaxation Bundle (Spa Session + Neck Pillow)
        $pax2 = 1;
        $productsData2 = [$formatProduct($productSpa, $pax2)];
        $addonsData2 = [$formatAddon($addonNeckPillow, $pax2)];
        
        $nta2 = array_sum(array_column($productsData2, 'sub_total')) + 
                         array_sum(array_column($addonsData2, 'sub_total'));
        
        $priceTotalPublish2 = $nta2;
        $paxPaid2 = round($priceTotalPublish2 / $pax2); 
        
        Package::updateOrCreate(
            [
                'slug' => 'ultimate-relaxation-bundle',
            ],
            [
                'name_package' => 'Ultimate Relaxation Bundle',
                'description' => 'Sesi spa mewah ditambah bantal leher untuk pengalaman relaksasi total. Harga dihitung dari NTA.',
                'location' => 'Surabaya',
                'phone' => '031-9876543',
                'nta' => $nta2, 
                'pax_paid' => $paxPaid2, 
                'start_publish' => now()->subWeek(),
                'end_publish' => now()->addYear(),
                'is_active' => true,
                'images' => $defaultImages, 
                'products_data' => $productsData2,
                'addons_data' => $addonsData2,
            ]
        );
        
        $pax3 = 3;
        $productsData3 = [$formatProduct($productBus, $pax3)];
        $addonsData3 = [$formatAddon($addonSeatUpgrade, $pax3)];

        $nta3 = array_sum(array_column($productsData3, 'sub_total')) + 
                         array_sum(array_column($addonsData3, 'sub_total'));
        
        $priceTotalPublish3 = $nta3; 
        $paxPaid3 = round($priceTotalPublish3 / $pax3);

        Package::updateOrCreate(
            [
                'slug' => 'vip-commute-experience',
            ],
            [
                'name_package' => 'VIP Commute Experience',
                'description' => 'Layanan bus premium dengan jaminan peningkatan kursi (seat upgrade) untuk kenyamanan maksimal. Harga dihitung dari NTA.',
                'location' => 'Bandung',
                'phone' => '022-5555555',
                'nta' => $nta3, 
                'pax_paid' => $paxPaid3, 
                'start_publish' => now(),
                'end_publish' => now()->addMonths(3),
                'is_active' => true,
                'images' => $defaultImages, 
                'products_data' => $productsData3,
                'addons_data' => $addonsData3,
            ]
        );
        
        $this->command->info('✅ PackageSeeder berhasil dijalankan. Kolom diskon telah dihapus, dan harga Pax Paid didasarkan pada NTA / Total Pax.');
    }
}