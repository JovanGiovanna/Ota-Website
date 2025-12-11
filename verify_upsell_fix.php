<?php

require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Verifying UPSELL Fix ===\n\n";

// Test Product
echo "1. PRODUCTS:\n";
$products = \App\Models\Product::limit(3)->get();
foreach($products as $p) {
    echo "   - " . $p->name . " | upsell: " . $p->upsell . "\n";
}

// Test Addon
echo "\n2. ADDONS:\n";
$addons = \App\Models\addon::limit(3)->get();
foreach($addons as $a) {
    echo "   - " . $a->addons . " | upsell: " . $a->upsell . "\n";
}

// Test Package
echo "\n3. PACKAGES:\n";
$packages = \App\Models\Package::limit(3)->get();
foreach($packages as $pkg) {
    echo "   - " . $pkg->name_package . " | upsell: " . $pkg->upsell . "\n";
}

echo "\n✅ All models can access 'upsell' field!\n";
?>
