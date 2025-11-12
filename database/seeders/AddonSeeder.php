<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Addon;
use App\Models\Vendor;
use Illuminate\Support\Str;

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
                'price' => 15000,
                'pax' => 1,
                'status' => 'available',
                'publish' => true,
                'id_vendor' => $vendor->id,
                'images' => ['addons/placeholder-eyemask.jpg'], 
            ],
            [
                'addons' => 'Travel Neck Pillow',
                'desc' => 'Bantal leher ergonomis untuk menjaga kenyamanan saat bepergian jauh.',
                'price' => 25000,
                'pax' => 1,
                'status' => 'available',
                'publish' => true,
                'id_vendor' => $vendor->id,
                'images' => ['addons/placeholder-neckpillow.jpg'], 
            ],
            [
                'addons' => 'Premium Seat Upgrade',
                'desc' => 'Naik kelas tempat duduk untuk pengalaman perjalanan yang lebih mewah.',
                'price' => 50000,
                'pax' => 1,
                'status' => 'available',
                'publish' => true,
                'id_vendor' => $vendor->id,
                'images' => ['addons/placeholder-upgrade.jpg'], 
            ],
            [
                'addons' => 'Aromatherapy Kit',
                'desc' => 'Set aromaterapi dengan minyak esensial untuk relaksasi selama perjalanan.',
                'price' => 30000,
                'pax' => 1,
                'status' => 'available',
                'publish' => true,
                'id_vendor' => $vendor->id,
                'images' => ['addons/placeholder-kit.jpg'], 
            ],
            [
                'addons' => 'Guided Meditation Session',
                'desc' => 'Sesi meditasi terpandu untuk menenangkan pikiran sebelum perjalanan dimulai.',
                'price' => 40000,
                'pax' => 1,
                'status' => 'available',
                'publish' => true,
                'id_vendor' => $vendor->id,
                'images' => ['addons/placeholder-meditation.jpg'], 
            ],
        ];

        foreach ($addons as $addon) {
            Addon::create($addon);
        }
    }
}