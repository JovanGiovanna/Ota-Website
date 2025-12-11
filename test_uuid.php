<?php

require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== UUID Testing ===\n\n";

// Test Province
$province = \App\Models\Province::first();
echo "Province:\n";
echo "  ID: " . $province->id . "\n";
echo "  Name: " . $province->name . "\n";
echo "  Is UUID: " . (preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i', $province->id) ? "✓ YES" : "✗ NO") . "\n\n";

// Test City
$city = \App\Models\City::first();
echo "City:\n";
echo "  ID: " . $city->id . "\n";
echo "  Name: " . $city->name . "\n";
echo "  Province ID: " . $city->id_province . "\n";
echo "  ID is UUID: " . (preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i', $city->id) ? "✓ YES" : "✗ NO") . "\n";
echo "  Province ID is UUID: " . (preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i', $city->id_province) ? "✓ YES" : "✗ NO") . "\n\n";

// Count
echo "Counts:\n";
echo "  Total Provinces: " . \App\Models\Province::count() . "\n";
echo "  Total Cities: " . \App\Models\City::count() . "\n";
