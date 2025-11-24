<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Addon;
use App\Models\Vendor;
use Illuminate\Support\Str;
use Carbon\Carbon; // Tambahkan ini untuk bekerja dengan tanggal kadaluarsa

class AddonSeeder extends Seeder
{
    public function run(): void
    {
        $vendor = Vendor::first();

        if (!$vendor) {
            $this->command->warn('Vendor belum ada! Pastikan VendorSeeder dijalankan lebih dulu.');
            return;
        }

        $addons = [
            [
                'addons' => 'Comfort Eye Mask',
                'desc' => 'Masker mata lembut untuk tidur yang lebih nyaman selama perjalanan.',
                // Perubahan Skema Harga
                'basic_price' => 20000.00, // Harga jual publik sebelum diskon
                'nta' => 15000.00,        // Harga Nett ke Agen/Vendor
                'tax_rate' => 0.00,       // Pajak 0%
                'pax' => 1,
                'status' => 'available',
                'publish' => true,
                'id_vendor' => $vendor->id,
                'images' => ['addons/placeholder-eyemask.jpg'], 
                // Tidak ada diskon
            ],
            [
                'addons' => 'Travel Neck Pillow',
                'desc' => 'Bantal leher ergonomis untuk menjaga kenyamanan saat bepergian jauh.',
                // Perubahan Skema Harga
                'basic_price' => 45000.00,
                'nta' => 35000.00,
                'tax_rate' => 11.00, // Contoh Pajak 11%
                'pax' => 1,
                'status' => 'available',
                'publish' => true,
                'id_vendor' => $vendor->id,
                'images' => ['addons/placeholder-neckpillow.jpg'], 
                // Diskon Persentase (Masih berlaku)
                'discount_type' => 'percentage',
                'discount_value' => 10.00, // Diskon 10%
                'discount_expires_at' => Carbon::now()->addMonths(2)->format('Y-m-d H:i:s'),
            ],
            [
                'addons' => 'Premium Seat Upgrade',
                'desc' => 'Naik kelas tempat duduk untuk pengalaman perjalanan yang lebih mewah.',
                // Perubahan Skema Harga
                'basic_price' => 150000.00,
                'nta' => 120000.00,
                'tax_rate' => 0.00,
                'pax' => 1,
                'status' => 'available',
                'publish' => true,
                'id_vendor' => $vendor->id,
                'images' => ['addons/placeholder-upgrade.jpg'], 
                // Diskon Tetap (Telah kadaluarsa)
                'discount_type' => 'fixed',
                'discount_value' => 25000.00, // Diskon Tetap Rp 25.000
                'discount_expires_at' => Carbon::yesterday()->format('Y-m-d H:i:s'), // Sudah Kadaluarsa
            ],
            [
                'addons' => 'Aromatherapy Kit',
                'desc' => 'Set aromaterapi dengan minyak esensial untuk relaksasi selama perjalanan.',
                // Perubahan Skema Harga
                'basic_price' => 60000.00,
                'nta' => 50000.00,
                'tax_rate' => 0.00,
                'pax' => 1,
                'status' => 'available',
                'publish' => true,
                'id_vendor' => $vendor->id,
                'images' => ['addons/placeholder-kit.jpg'], 
                // Diskon Tetap (Masih berlaku)
                'discount_type' => 'fixed',
                'discount_value' => 10000.00, // Diskon Tetap Rp 10.000
                'discount_expires_at' => null, // Diskon berlaku selamanya/tanpa batas waktu
            ],
            [
                'addons' => 'Guided Meditation Session',
                'desc' => 'Sesi meditasi terpandu untuk menenangkan pikiran sebelum perjalanan dimulai.',
                // Perubahan Skema Harga
                'basic_price' => 40000.00,
                'nta' => 30000.00,
                'tax_rate' => 0.00,
                'pax' => 1,
                'status' => 'available',
                'publish' => true,
                'id_vendor' => $vendor->id,
                'images' => ['addons/placeholder-meditation.jpg'], 
                // Tidak ada diskon
            ],
        ];

        
        foreach ($addons as $addon) {
            // Gunakan updateOrCreate agar seeder dapat dijalankan berulang kali tanpa duplikasi, 
            // atau gunakan create jika Anda yakin tabel kosong.
            // Di sini kita gunakan create untuk meniru seeder awal:
             Addon::create(array_merge(
                $addon,
                ['id' => Str::uuid()] // Pastikan UUID dihasilkan
            ));
        }
    }
}