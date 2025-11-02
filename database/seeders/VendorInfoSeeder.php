<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\VendorInfo;

class VendorInfoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $vendors = \App\Models\Vendor::all();
        $cities = \App\Models\City::all();
        $provinces = \App\Models\Province::all();

        $vendorInfos = [
            [
                'id_vendor' => $vendors->first()->id,
                'name_corporate' => 'Comfort Vendor Info 1',
                'description' => 'Comfort-focused services for Vendor 1',
                'address' => 'Comfort Address 1',
                'phone' => '081234567890',
                'id_city' => $cities->where('name', 'Jakarta')->first()->id,
                'coordinate_latitude' => -6.2088,
                'coordinate_longitude' => 106.8456,
                'landmark_description' => 'Near Comfort Central Park',
            ],
        ];

        foreach ($vendorInfos as $vendorInfo) {
            VendorInfo::create($vendorInfo);
        }
    }
}
