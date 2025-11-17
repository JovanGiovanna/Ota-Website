<?php
// Temporary file to check addon relationships
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$addons = \App\Models\Addon::with('vendor.vendorInfo')->get();
$packages = \App\Models\Package::with('vendorInfo.vendor')->get();

echo "Addons with vendors:\n";
foreach ($addons as $addon) {
    $vendorName = isset($addon->vendor) ? $addon->vendor->name : 'No Vendor';
    echo "Addon: {$addon->addons} - Vendor: {$vendorName}\n";
}

echo "\nPackages with vendors:\n";
foreach ($packages as $package) {
    $vendorName = isset($package->vendorInfo->vendor) ? $package->vendorInfo->vendor->name : 'No Vendor';
    echo "Package: {$package->name_package} - Vendor: {$vendorName}\n";
}
