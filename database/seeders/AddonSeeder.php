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
                'basic_price' => 20000.00,
                'nta' => 15000.00,
                'tax_rate' => 0.00,
                'pax' => 1,
                'status' => 'available',
                'location' => 'Jakarta',
                'phone' => '021-1234567',
                'id_vendor' => $vendor->id,
                'images' => ['addons/placeholder-eyemask.jpg'], 
            ],
            [
                'addons' => 'Travel Neck Pillow',
                'desc' => 'Bantal leher ergonomis untuk menjaga kenyamanan saat bepergian jauh.',
                'basic_price' => 45000.00,
                'nta' => 35000.00,
                'tax_rate' => 11.00,
                'pax' => 1,
                'status' => 'available',
                'location' => 'Surabaya',
                'phone' => '031-9876543',
                'id_vendor' => $vendor->id,
                'images' => ['addons/placeholder-neckpillow.jpg'], 
                'discount_type' => 'percentage',
                'discount_value' => 10.00,
                'discount_expires_at' => Carbon::now()->addMonths(2)->format('Y-m-d H:i:s'),
            ],
            [
                'addons' => 'Premium Seat Upgrade',
                'desc' => 'Naik kelas tempat duduk untuk pengalaman perjalanan yang lebih mewah.',
                'basic_price' => 150000.00,
                'nta' => 120000.00,
                'tax_rate' => 0.00,
                'pax' => 1,
                'status' => 'available',
                'location' => 'Bandung',
                'phone' => '022-5555555',
                'id_vendor' => $vendor->id,
                'images' => ['addons/placeholder-upgrade.jpg'], 
                'discount_type' => 'fixed',
                'discount_value' => 25000.00,
                'discount_expires_at' => Carbon::yesterday()->format('Y-m-d H:i:s'),
            ],
            [
                'addons' => 'Aromatherapy Kit',
                'desc' => 'Set aromaterapi dengan minyak esensial untuk relaksasi selama perjalanan.',
                'basic_price' => 60000.00,
                'nta' => 50000.00,
                'tax_rate' => 0.00,
                'pax' => 1,
                'status' => 'available',
                'location' => 'Yogyakarta',
                'phone' => '0274-888888',
                'id_vendor' => $vendor->id,
                'images' => ['addons/placeholder-kit.jpg'], 
                'discount_type' => 'fixed',
                'discount_value' => 10000.00,
                'discount_expires_at' => null,
            ],
            [
                'addons' => 'Guided Meditation Session',
                'desc' => 'Sesi meditasi terpandu untuk menenangkan pikiran sebelum perjalanan dimulai.',
                'basic_price' => 40000.00,
                'nta' => 30000.00,
                'tax_rate' => 0.00,
                'pax' => 1,
                'status' => 'available',
                'location' => 'Bali',
                'phone' => '0361-7777777',
                'id_vendor' => $vendor->id,
                'images' => ['addons/placeholder-meditation.jpg'], 
            ],
        ];

        
        foreach ($addons as $addon) {
            // Hitung discount_amount
            $discountAmount = $this->calculateDiscountAmount(
                $addon['basic_price'],
                $addon['tax_rate'] ?? 0,
                $addon['discount_type'] ?? null,
                $addon['discount_value'] ?? null,
                $addon['discount_expires_at'] ?? null
            );
            $addon['discount_amount'] = $discountAmount;
            
            Addon::create(array_merge(
                $addon,
                ['id' => Str::uuid()]
            ));
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