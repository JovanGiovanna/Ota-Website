<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Package;

class PackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Package::updateOrCreate(
            ['slug' => 'bali-adventure-package'],
            [
                'name_package' => 'Bali Adventure Package',
                'description' => 'Experience the beauty of Bali with this comprehensive adventure package including beach activities, cultural tours, and local cuisine.',
                'price_publish' => 2500000,
                'start_publish' => '2024-01-01',
                'end_publish' => '2024-12-31',
                'is_active' => true,
                'image' => null,
            ]
        );

        Package::updateOrCreate(
            ['slug' => 'jakarta-city-tour'],
            [
                'name_package' => 'Jakarta City Tour',
                'description' => 'Explore the vibrant capital city of Indonesia with guided tours to historical sites, modern attractions, and local markets.',
                'price_publish' => 1500000,
                'start_publish' => '2024-01-01',
                'end_publish' => '2024-12-31',
                'is_active' => true,
                'image' => null,
            ]
        );

        Package::updateOrCreate(
            ['slug' => 'yogyakarta-cultural-experience'],
            [
                'name_package' => 'Yogyakarta Cultural Experience',
                'description' => 'Immerse yourself in Javanese culture with visits to ancient temples, traditional arts performances, and local handicraft centers.',
                'price_publish' => 1800000,
                'start_publish' => '2024-01-01',
                'end_publish' => '2024-12-31',
                'is_active' => true,
                'image' => null,
            ]
        );
    }
}
