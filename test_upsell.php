<?php

require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== UPSELL Field Testing ===\n\n";

// Test Packages
echo "Packages with upsell:\n";
$packages = \App\Models\Package::take(5)->get();
foreach($packages as $p) {
    echo "  " . $p->name . " (ID: " . substr($p->id, 0, 8) . "...)\n";
    echo "    - upsell: " . $p->upsell . "\n";
}

echo "\n\nProducts with upsell:\n";
$products = \App\Models\Product::take(5)->get();
foreach($products as $p) {
    echo "  " . $p->name . " (ID: " . substr($p->id, 0, 8) . "...)\n";
    echo "    - upsell: " . $p->upsell . "\n";
}

echo "\n\nAddons with upsell:\n";
$addons = \App\Models\addon::take(5)->get();
foreach($addons as $a) {
    echo "  " . $a->name . " (ID: " . substr($a->id, 0, 8) . "...)\n";
    echo "    - upsell: " . $a->upsell . "\n";
}

echo "\n\nCounts:\n";
echo "  Total Packages: " . \App\Models\Package::count() . "\n";
echo "  Total Products: " . \App\Models\Product::count() . "\n";
echo "  Total Addons: " . \App\Models\addon::count() . "\n";
?>
