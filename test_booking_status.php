<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Booking;
use App\Models\BookPackage;
use App\Models\BookProduct;
use App\Models\BookAddon;
use App\Models\Package;
use App\Models\Product;
use App\Models\Addon;
use App\Models\VendorInfo;
use App\Models\Admin;
use Illuminate\Support\Facades\DB;

echo "=== TESTING BOOKING STATUS FLOW ===\n\n";

$bookingCode = 'BK-20251208071027-207';
$booking = Booking::where('booking_code', $bookingCode)->first();

if (!$booking) {
    echo "Booking not found: {$bookingCode}\n";
    exit;
}

echo "Booking ID: {$booking->id}\n";
echo "Current Status: {$booking->status}\n";
echo "Total Price: Rp " . number_format($booking->total_price, 0, ',', '.') . "\n\n";

// Get all related bookings
$bookPackages = BookPackage::where('id_book', $booking->id)->get();
$bookProducts = BookProduct::where('id_book', $booking->id)->get();
$bookAddons = BookAddon::where('id_book', $booking->id)->get();

echo "--- CURRENT STATE ---\n";

// Check BookPackages
if ($bookPackages->count() > 0) {
    echo "\nBOOK PACKAGES:\n";
    foreach ($bookPackages as $bp) {
        $package = Package::find($bp->id_package);
        echo "  Package: " . ($package ? $package->name_package : 'N/A') . "\n";
        echo "  Quantity: {$bp->quantity}\n";
        echo "  Status: {$bp->status}\n";
        echo "  Total Price: Rp " . number_format($bp->total_price, 0, ',', '.') . "\n";
        echo "  Stock Applied: " . ($bp->stock_applied ? 'YES' : 'NO') . "\n";
        echo "  Revenue Applied: " . ($bp->revenue_applied ? 'YES' : 'NO') . "\n";
        
        // Get products from JSON products_data
        $productsData = is_array($package->products_data) ? $package->products_data : [];
        echo "  Products in package:\n";
        foreach ($productsData as $productData) {
            $productId = $productData['id'] ?? null;
            if ($productId) {
                $product = Product::find($productId);
                if ($product) {
                    $pax = $productData['pax'] ?? 1;
                    echo "    - {$product->name} (pax: {$pax}) | Stock: {$product->jumlah}\n";
                }
            }
        }
        
        // Get addons from JSON addons_data
        $addonsData = is_array($package->addons_data) ? $package->addons_data : [];
        if (count($addonsData) > 0) {
            echo "  Addons in package:\n";
            foreach ($addonsData as $addonData) {
                $addonId = $addonData['id'] ?? null;
                if ($addonId) {
                    $addon = Addon::find($addonId);
                    if ($addon) {
                        $pax = $addonData['pax'] ?? 1;
                        echo "    - {$addon->addons} (pax: {$pax}) | Stock: {$addon->jumlah}\n";
                    }
                }
            }
        }
    }
}

// Check BookProducts
if ($bookProducts->count() > 0) {
    echo "\nBOOK PRODUCTS:\n";
    foreach ($bookProducts as $bp) {
        $product = Product::find($bp->id_product);
        echo "  Product: " . ($product ? $product->name : 'N/A') . "\n";
        echo "  Amount: {$bp->amount}\n";
        echo "  Status: {$bp->status}\n";
        echo "  Total Price: Rp " . number_format($bp->total_price, 0, ',', '.') . "\n";
        echo "  Stock Applied: " . ($bp->stock_applied ? 'YES' : 'NO') . "\n";
        echo "  Revenue Applied: " . ($bp->revenue_applied ? 'YES' : 'NO') . "\n";
        if ($product) {
            echo "  Product Stock: {$product->jumlah}\n";
        }
    }
}

// Check BookAddons
if ($bookAddons->count() > 0) {
    echo "\nBOOK ADDONS:\n";
    foreach ($bookAddons as $ba) {
        $addon = Addon::find($ba->id_addon);
        echo "  Addon: " . ($addon ? $addon->addons : 'N/A') . "\n";
        echo "  Amount: {$ba->amount}\n";
        echo "  Status: {$ba->status}\n";
        echo "  Total Price: Rp " . number_format($ba->total_price, 0, ',', '.') . "\n";
        echo "  Stock Applied: " . ($ba->stock_applied ? 'YES' : 'NO') . "\n";
        echo "  Revenue Applied: " . ($ba->revenue_applied ? 'YES' : 'NO') . "\n";
        if ($addon) {
            echo "  Addon Stock: {$addon->jumlah}\n";
        }
    }
}

echo "\n\n=== SCENARIO TESTING ===\n\n";

// Store initial state
$initialStocks = [];
$initialRevenues = [];

// Get initial stocks for package products
foreach ($bookPackages as $bp) {
    $packageProductIds = DB::table('package_products')->where('id_package', $bp->id_package)->pluck('id_product');
    foreach ($packageProductIds as $productId) {
        $product = Product::find($productId);
        if ($product) {
            $initialStocks['package_product_' . $productId] = $product->jumlah;
        }
    }
    
    // Get package vendor revenue
    $package = Package::find($bp->id_package);
    if ($package && $package->vendorInfo) {
        $initialRevenues['package_vendor_' . $package->id_vendor_info] = $package->vendorInfo->total_revenue;
    }
}

// Get initial stocks for direct products
foreach ($bookProducts as $bp) {
    $product = Product::find($bp->id_product);
    if ($product) {
        $initialStocks['product_' . $bp->id_product] = $product->jumlah;
        
        // Get product vendor/admin revenue
        if ($product->id_vendor) {
            $vendorInfo = $product->vendor->vendorInfo;
            if ($vendorInfo) {
                $initialRevenues['product_vendor_' . $vendorInfo->id] = $vendorInfo->total_revenue;
            }
        } elseif ($product->id_super_admin) {
            $admin = Admin::find($product->id_super_admin);
            if ($admin) {
                $initialRevenues['product_admin_' . $admin->id] = $admin->total_revenue;
            }
        }
    }
}

// Get initial stocks for addons
foreach ($bookAddons as $ba) {
    $addon = Addon::find($ba->id_addon);
    if ($addon) {
        $initialStocks['addon_' . $ba->id_addon] = $addon->jumlah;
        
        // Get addon vendor revenue
        if ($addon->id_vendor) {
            $vendorInfo = $addon->vendor->vendorInfo;
            if ($vendorInfo) {
                $initialRevenues['addon_vendor_' . $vendorInfo->id] = $vendorInfo->total_revenue;
            }
        }
    }
}

echo "Initial state captured.\n\n";

// TEST 1: Status = 'book' (should decrease stock)
if ($booking->status !== 'book') {
    echo "TEST 1: Changing status to 'book' (should decrease stock)...\n";
    $booking->update(['status' => 'book']);
    
    // Check stock changes
    foreach ($initialStocks as $key => $initialStock) {
        if (strpos($key, 'package_product_') === 0) {
            $productId = str_replace('package_product_', '', $key);
            $product = Product::find($productId);
            $newStock = $product->jumlah;
            $diff = $initialStock - $newStock;
            echo "  Product {$product->name}: {$initialStock} → {$newStock} (decreased by {$diff})\n";
        } elseif (strpos($key, 'product_') === 0) {
            $productId = str_replace('product_', '', $key);
            $product = Product::find($productId);
            $newStock = $product->jumlah;
            $diff = $initialStock - $newStock;
            echo "  Product {$product->name}: {$initialStock} → {$newStock} (decreased by {$diff})\n";
        } elseif (strpos($key, 'addon_') === 0) {
            $addonId = str_replace('addon_', '', $key);
            $addon = Addon::find($addonId);
            $newStock = $addon->jumlah;
            $diff = $initialStock - $newStock;
            echo "  Addon {$addon->addons}: {$initialStock} → {$newStock} (decreased by {$diff})\n";
        }
    }
    
    // Update initial stocks for next test
    foreach ($initialStocks as $key => &$stock) {
        if (strpos($key, 'package_product_') === 0) {
            $productId = str_replace('package_product_', '', $key);
            $stock = Product::find($productId)->jumlah;
        } elseif (strpos($key, 'product_') === 0) {
            $productId = str_replace('product_', '', $key);
            $stock = Product::find($productId)->jumlah;
        } elseif (strpos($key, 'addon_') === 0) {
            $addonId = str_replace('addon_', '', $key);
            $stock = Addon::find($addonId)->jumlah;
        }
    }
    echo "\n";
}

// TEST 2: Status = 'paid' (should increase revenue)
echo "TEST 2: Changing status to 'paid' (should increase revenue)...\n";
$booking->update(['status' => 'paid']);

foreach ($initialRevenues as $key => $initialRevenue) {
    if (strpos($key, 'package_vendor_') === 0) {
        $vendorInfoId = str_replace('package_vendor_', '', $key);
        $vendorInfo = VendorInfo::find($vendorInfoId);
        $newRevenue = $vendorInfo->total_revenue;
        $diff = $newRevenue - $initialRevenue;
        echo "  Package Vendor Revenue: Rp " . number_format($initialRevenue, 0, ',', '.') . " → Rp " . number_format($newRevenue, 0, ',', '.') . " (increased by Rp " . number_format($diff, 0, ',', '.') . ")\n";
    } elseif (strpos($key, 'product_vendor_') === 0) {
        $vendorInfoId = str_replace('product_vendor_', '', $key);
        $vendorInfo = VendorInfo::find($vendorInfoId);
        $newRevenue = $vendorInfo->total_revenue;
        $diff = $newRevenue - $initialRevenue;
        echo "  Product Vendor Revenue: Rp " . number_format($initialRevenue, 0, ',', '.') . " → Rp " . number_format($newRevenue, 0, ',', '.') . " (increased by Rp " . number_format($diff, 0, ',', '.') . ")\n";
    } elseif (strpos($key, 'addon_vendor_') === 0) {
        $vendorInfoId = str_replace('addon_vendor_', '', $key);
        $vendorInfo = VendorInfo::find($vendorInfoId);
        $newRevenue = $vendorInfo->total_revenue;
        $diff = $newRevenue - $initialRevenue;
        echo "  Addon Vendor Revenue: Rp " . number_format($initialRevenue, 0, ',', '.') . " → Rp " . number_format($newRevenue, 0, ',', '.') . " (increased by Rp " . number_format($diff, 0, ',', '.') . ")\n";
    } elseif (strpos($key, 'product_admin_') === 0) {
        $adminId = str_replace('product_admin_', '', $key);
        $admin = Admin::find($adminId);
        $newRevenue = $admin->total_revenue;
        $diff = $newRevenue - $initialRevenue;
        echo "  Product Admin Revenue: Rp " . number_format($initialRevenue, 0, ',', '.') . " → Rp " . number_format($newRevenue, 0, ',', '.') . " (increased by Rp " . number_format($diff, 0, ',', '.') . ")\n";
    }
}

// Update revenues for next test
foreach ($initialRevenues as $key => &$revenue) {
    if (strpos($key, 'package_vendor_') === 0) {
        $vendorInfoId = str_replace('package_vendor_', '', $key);
        $revenue = VendorInfo::find($vendorInfoId)->total_revenue;
    } elseif (strpos($key, 'product_vendor_') === 0) {
        $vendorInfoId = str_replace('product_vendor_', '', $key);
        $revenue = VendorInfo::find($vendorInfoId)->total_revenue;
    } elseif (strpos($key, 'addon_vendor_') === 0) {
        $vendorInfoId = str_replace('addon_vendor_', '', $key);
        $revenue = VendorInfo::find($vendorInfoId)->total_revenue;
    } elseif (strpos($key, 'product_admin_') === 0) {
        $adminId = str_replace('product_admin_', '', $key);
        $revenue = Admin::find($adminId)->total_revenue;
    }
}
echo "\n";

// TEST 3: Status = 'cancelled' (should restore stock)
echo "TEST 3: Changing status to 'cancelled' (should restore stock)...\n";
$booking->update(['status' => 'cancelled']);

foreach ($initialStocks as $key => $currentStock) {
    if (strpos($key, 'package_product_') === 0) {
        $productId = str_replace('package_product_', '', $key);
        $product = Product::find($productId);
        $newStock = $product->jumlah;
        $diff = $newStock - $currentStock;
        echo "  Product {$product->name}: {$currentStock} → {$newStock} (increased by {$diff})\n";
    } elseif (strpos($key, 'product_') === 0) {
        $productId = str_replace('product_', '', $key);
        $product = Product::find($productId);
        $newStock = $product->jumlah;
        $diff = $newStock - $currentStock;
        echo "  Product {$product->name}: {$currentStock} → {$newStock} (increased by {$diff})\n";
    } elseif (strpos($key, 'addon_') === 0) {
        $addonId = str_replace('addon_', '', $key);
        $addon = Addon::find($addonId);
        $newStock = $addon->jumlah;
        $diff = $newStock - $currentStock;
        echo "  Addon {$addon->addons}: {$currentStock} → {$newStock} (increased by {$diff})\n";
    }
}
echo "\n";

// TEST 4: Back to 'paid' then 'payment_return' (should decrease revenue)
echo "TEST 4a: Changing back to 'paid' first...\n";
$booking->update(['status' => 'paid']);
echo "Done.\n\n";

echo "TEST 4b: Changing status to 'payment_return' (should decrease revenue)...\n";
$booking->update(['status' => 'payment_return']);

foreach ($initialRevenues as $key => $currentRevenue) {
    if (strpos($key, 'package_vendor_') === 0) {
        $vendorInfoId = str_replace('package_vendor_', '', $key);
        $vendorInfo = VendorInfo::find($vendorInfoId);
        $newRevenue = $vendorInfo->total_revenue;
        $diff = $currentRevenue - $newRevenue;
        echo "  Package Vendor Revenue: Rp " . number_format($currentRevenue, 0, ',', '.') . " → Rp " . number_format($newRevenue, 0, ',', '.') . " (decreased by Rp " . number_format($diff, 0, ',', '.') . ")\n";
    } elseif (strpos($key, 'product_vendor_') === 0) {
        $vendorInfoId = str_replace('product_vendor_', '', $key);
        $vendorInfo = VendorInfo::find($vendorInfoId);
        $newRevenue = $vendorInfo->total_revenue;
        $diff = $currentRevenue - $newRevenue;
        echo "  Product Vendor Revenue: Rp " . number_format($currentRevenue, 0, ',', '.') . " → Rp " . number_format($newRevenue, 0, ',', '.') . " (decreased by Rp " . number_format($diff, 0, ',', '.') . ")\n";
    } elseif (strpos($key, 'addon_vendor_') === 0) {
        $vendorInfoId = str_replace('addon_vendor_', '', $key);
        $vendorInfo = VendorInfo::find($vendorInfoId);
        $newRevenue = $vendorInfo->total_revenue;
        $diff = $currentRevenue - $newRevenue;
        echo "  Addon Vendor Revenue: Rp " . number_format($currentRevenue, 0, ',', '.') . " → Rp " . number_format($newRevenue, 0, ',', '.') . " (decreased by Rp " . number_format($diff, 0, ',', '.') . ")\n";
    } elseif (strpos($key, 'product_admin_') === 0) {
        $adminId = str_replace('product_admin_', '', $key);
        $admin = Admin::find($adminId);
        $newRevenue = $admin->total_revenue;
        $diff = $currentRevenue - $newRevenue;
        echo "  Product Admin Revenue: Rp " . number_format($currentRevenue, 0, ',', '.') . " → Rp " . number_format($newRevenue, 0, ',', '.') . " (decreased by Rp " . number_format($diff, 0, ',', '.') . ")\n";
    }
}

echo "\n=== ALL TESTS COMPLETE ===\n";
