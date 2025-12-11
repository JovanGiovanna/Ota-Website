<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Booking;
use App\Models\BookPackage;
use App\Models\BookAddon;
use App\Models\Package;
use App\Models\Addon;
use Illuminate\Support\Facades\DB;

echo "=== Testing BookingObserver ===\n\n";

// Get latest booking
$booking = Booking::latest()->first();
if (!$booking) {
    echo "No booking found.\n";
    exit;
}

echo "Latest Booking ID: {$booking->id}\n";
echo "Status: {$booking->status}\n";
echo "Total Price: Rp " . number_format($booking->total_price, 0, ',', '.') . "\n\n";

// Check BookPackage
$bookPackages = BookPackage::where('id_book', $booking->id)->get();
if ($bookPackages->count() > 0) {
    echo "--- BOOK PACKAGES ---\n";
    foreach ($bookPackages as $bp) {
        $package = Package::find($bp->id_package);
        echo "Package: " . ($package ? $package->name_package : 'N/A') . "\n";
        echo "Quantity: {$bp->quantity}\n";
        echo "Total Price: Rp " . number_format($bp->total_price, 0, ',', '.') . "\n";
        echo "Stock Applied: " . ($bp->stock_applied ? 'YES' : 'NO') . "\n";
        echo "Revenue Applied: " . ($bp->revenue_applied ? 'YES' : 'NO') . "\n";
        
        // Check products in this package
        $packageProductIds = DB::table('package_products')->where('id_package', $bp->id_package)->pluck('id_product');
        echo "Products in package: " . $packageProductIds->count() . "\n";
        foreach ($packageProductIds as $productId) {
            $product = \App\Models\Product::find($productId);
            if ($product) {
                echo "  - {$product->name} | Stock: {$product->jumlah}\n";
            }
        }
        echo "\n";
    }
}

// Check BookAddon
$bookAddons = BookAddon::where('id_book', $booking->id)->get();
if ($bookAddons->count() > 0) {
    echo "--- BOOK ADDONS ---\n";
    foreach ($bookAddons as $ba) {
        $addon = Addon::find($ba->id_addon);
        echo "Addon: " . ($addon ? $addon->addons : 'N/A') . "\n";
        echo "Amount: {$ba->amount}\n";
        echo "Total Price: Rp " . number_format($ba->total_price, 0, ',', '.') . "\n";
        echo "Stock Applied: " . ($ba->stock_applied ? 'YES' : 'NO') . "\n";
        echo "Revenue Applied: " . ($ba->revenue_applied ? 'YES' : 'NO') . "\n";
        if ($addon) {
            echo "Addon Stock: {$addon->jumlah}\n";
        }
        echo "\n";
    }
}

echo "\n=== Now testing status changes ===\n\n";

// Get current status
$currentStatus = $booking->status;
echo "Current status: {$currentStatus}\n\n";

// If status is 'book', change to 'paid' to test revenue
if ($currentStatus === 'book') {
    echo "Changing status from 'book' to 'paid'...\n";
    $booking->update(['status' => 'paid']);
    echo "Status changed. Check revenue_applied flags.\n\n";
    
    // Refresh and check again
    $bookPackages = BookPackage::where('id_book', $booking->id)->get();
    foreach ($bookPackages as $bp) {
        echo "BookPackage Revenue Applied: " . ($bp->fresh()->revenue_applied ? 'YES' : 'NO') . "\n";
    }
    
    $bookAddons = BookAddon::where('id_book', $booking->id)->get();
    foreach ($bookAddons as $ba) {
        echo "BookAddon Revenue Applied: " . ($ba->fresh()->revenue_applied ? 'YES' : 'NO') . "\n";
    }
}

// If status is 'paid', test cancellation
elseif ($currentStatus === 'paid') {
    echo "Changing status from 'paid' to 'cancelled'...\n";
    
    // Get stock before
    $stockBefore = [];
    $bookPackages = BookPackage::where('id_book', $booking->id)->get();
    foreach ($bookPackages as $bp) {
        $packageProductIds = DB::table('package_products')->where('id_package', $bp->id_package)->pluck('id_product');
        foreach ($packageProductIds as $productId) {
            $product = \App\Models\Product::find($productId);
            if ($product) {
                $stockBefore[$productId] = $product->jumlah;
            }
        }
    }
    
    $booking->update(['status' => 'cancelled']);
    echo "Status changed. Checking if stock restored...\n\n";
    
    // Check stock after
    foreach ($stockBefore as $productId => $stockBeforeValue) {
        $product = \App\Models\Product::find($productId);
        $stockAfter = $product->jumlah;
        $diff = $stockAfter - $stockBeforeValue;
        echo "Product {$product->name}: Stock before={$stockBeforeValue}, after={$stockAfter}, diff={$diff}\n";
    }
}

echo "\n=== Test Complete ===\n";
