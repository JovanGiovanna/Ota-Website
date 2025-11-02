<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Vendor;

class VendorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Vendor::updateOrCreate(
            ['email' => 'vendor@test.com'],
            [
                'name' => 'Comfort Vendor',
                'password' => Hash::make('password'),
                'is_active' => true,
            ]
        );
    }
}
