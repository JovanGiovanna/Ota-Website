<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Addon;
use App\Models\Vendor;
use Illuminate\Support\Str;

class AddonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil vendor pertama yang sudah ada
        $vendor = Vendor::first();

        // Jika vendor tidak ditemukan, tampilkan warning
        if (!$vendor) {
            $this->command->warn('⚠️ Vendor belum ada! Pastikan VendorSeeder dijalankan lebih dulu.');
            return;
        }

        $addons = [
            [
                'addons' => 'Comfort Eye Mask',
                'desc' => 'Masker mata lembut untuk tidur yang lebih nyaman selama perjalanan.',
                'price' => 15000,
                'status' => 'available',
                'publish' => true,
                'id_vendor' => $vendor->id,
            ],
            [
                'addons' => 'Travel Neck Pillow',
                'desc' => 'Bantal leher ergonomis untuk menjaga kenyamanan saat bepergian jauh.',
                'price' => 25000,
                'status' => 'available',
                'publish' => true,
                'id_vendor' => $vendor->id,
            ],
            [
                'addons' => 'Premium Seat Upgrade',
                'desc' => 'Naik kelas tempat duduk untuk pengalaman perjalanan yang lebih mewah.',
                'price' => 50000,
                'status' => 'available',
                'publish' => true,
                'id_vendor' => $vendor->id,
            ],
            [
                'addons' => 'Aromatherapy Kit',
                'desc' => 'Set aromaterapi dengan minyak esensial untuk relaksasi selama perjalanan.',
                'price' => 30000,
                'status' => 'available',
                'publish' => true,
                'id_vendor' => $vendor->id,
            ],
            [
                'addons' => 'Guided Meditation Session',
                'desc' => 'Sesi meditasi terpandu untuk menenangkan pikiran sebelum perjalanan dimulai.',
                'price' => 40000,
                'status' => 'available',
                'publish' => true,
                'id_vendor' => $vendor->id,
            ],
        ];

        foreach ($addons as $addon) {
            Addon::create(array_merge($addon, [
                'id' => Str::uuid(),
            ]));
        }
    }
}
