<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$package = \App\Models\Package::first();
if ($package) {
    echo "Package ID: " . $package->id . "\n";
    echo "Images: ";
    var_dump($package->images);
    echo "Images type: " . gettype($package->images) . "\n";
    if (is_array($package->images)) {
        echo "Images count: " . count($package->images) . "\n";
        foreach ($package->images as $index => $img) {
            echo "  [$index] => $img\n";
        }
    }
} else {
    echo "No packages found\n";
}
