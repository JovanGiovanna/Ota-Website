<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Type;
use Illuminate\Support\Str;

class TypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['id' => Str::uuid(), 'type' => 'Basic', 'status' => true],
            ['id' => Str::uuid(), 'type' => 'Premium', 'status' => true],
            ['id' => Str::uuid(), 'type' => 'Wellness', 'status' => true],
        ];

        foreach ($types as $type) {
            Type::create($type);
        }
    }
}
