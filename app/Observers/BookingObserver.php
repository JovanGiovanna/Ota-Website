<?php

namespace App\Observers;

use App\Models\Booking;
use App\Models\BookProduct;
use App\Models\BookPackage;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BookingObserver
{
    /** Handle the Booking "created" event. */
    public function created(Booking $booking)
    {
        Log::info('BookingObserver created triggered for booking ID: ' . $booking->id . ', status: ' . $booking->status);
        // When a booking is first created with status 'book', apply stock immediately
        if ($booking->status === 'book') {
            $this->applyStockAndRevenue($booking);
        }
    }

    /** Handle the Booking "updated" event. */
    public function updated(Booking $booking)
    {
        // Only act when status changed
        $original = $booking->getOriginal('status');
        $current = $booking->status;

        if ($original === $current) {
            return;
        }

        $this->applyStockAndRevenue($booking);
    }

    /** Apply stock and revenue changes based on booking status */
    private function applyStockAndRevenue(Booking $booking)
    {
        Log::info('BookingObserver triggered for booking ID: ' . $booking->id . ', status: ' . $booking->status);
        try {
            // 1. Handle direct product bookings (from book_products)
            $bookProducts = BookProduct::where('id_book', $booking->id)->get();

            foreach ($bookProducts as $bp) {
                $product = Product::find($bp->id_product);
                $amount = (int) ($bp->amount ?? 1);

                // When booking becomes 'book' -> decrease stock (once)
                if ($booking->status === 'book' && !$bp->stock_applied) {
                    if ($product) {
                        $product->jumlah = max(0, ($product->jumlah ?? 0) - $amount);
                        $product->save();
                    }
                    $bp->stock_applied = true;
                    $bp->save();
                }

                // When booking becomes 'paid' -> mark revenue applied and update revenue
                if ($booking->status === 'paid' && !$bp->revenue_applied) {
                    $price = $bp->total_price; // use total_price from BookProduct
                    if ($product && $product->id_super_admin) {
                        $superAdmin = $product->superAdmin;
                        if ($superAdmin) {
                            $superAdmin->total_revenue = ($superAdmin->total_revenue ?? 0) + $price;
                            $superAdmin->save();
                        }
                    } elseif ($product && $product->id_vendor) {
                        $vendorInfo = $product->vendor->vendorInfo;
                        if ($vendorInfo) {
                            $vendorInfo->total_revenue = ($vendorInfo->total_revenue ?? 0) + $price;
                            $vendorInfo->save();
                        }
                    }
                    $bp->revenue_applied = true;
                    $bp->save();
                }

                // When booking becomes 'cancelled' -> restore stock if previously applied
                if ($booking->status === 'cancelled' && $bp->stock_applied) {
                    if ($product) {
                        $product->jumlah = ($product->jumlah ?? 0) + $amount;
                        $product->save();
                    }
                    $bp->stock_applied = false;
                    $bp->save();
                }

                // When booking becomes 'payment return' -> remove revenue flag and update revenue
                if (($booking->status === 'payment return' || $booking->status === 'payment_return') && $bp->revenue_applied) {
                    $price = $bp->total_price; // use total_price from BookProduct
                    if ($product && $product->id_super_admin) {
                        $superAdmin = $product->superAdmin;
                        if ($superAdmin) {
                            $superAdmin->total_revenue = ($superAdmin->total_revenue ?? 0) - $price;
                            $superAdmin->save();
                        }
                    } elseif ($product && $product->id_vendor) {
                        $vendorInfo = $product->vendor->vendorInfo;
                        if ($vendorInfo) {
                            $vendorInfo->total_revenue = ($vendorInfo->total_revenue ?? 0) - $price;
                            $vendorInfo->save();
                        }
                    }
                    $bp->revenue_applied = false;
                    $bp->save();
                }
            }

            // 2. Handle products inside packages (from book_packages -> package_products)
            $bookPackages = BookPackage::where('id_book', $booking->id)->get();

            foreach ($bookPackages as $bp) {
                $package = $bp->package;
                if (!$package) continue;

                // When booking becomes 'book' -> decrease stock for each product inside the package, once per booked package
                if ($booking->status === 'book' && !$bp->stock_applied) {
                    $packageProductIds = DB::table('package_products')->where('id_package', $package->id)->pluck('id_product');
                    foreach ($packageProductIds as $productId) {
                        $product = Product::find($productId);
                        if (!$product) continue;
                        $amount = 1; // packages currently imply 1 unit per package product
                        $product->jumlah = max(0, ($product->jumlah ?? 0) - $amount);
                        $product->save();
                    }
                    $bp->stock_applied = true;
                    $bp->save();
                }

                // When booking becomes 'cancelled' -> restore stock for package products if previously applied
                if ($booking->status === 'cancelled' && $bp->stock_applied) {
                    $packageProductIds = DB::table('package_products')->where('id_package', $package->id)->pluck('id_product');
                    foreach ($packageProductIds as $productId) {
                        $product = Product::find($productId);
                        if (!$product) continue;
                        $amount = 1;
                        $product->jumlah = ($product->jumlah ?? 0) + $amount;
                        $product->save();
                    }
                    $bp->stock_applied = false;
                    $bp->save();
                }

                // Handle revenue for packages - add to package vendor
                if ($booking->status === 'paid' && !$bp->revenue_applied) {
                    $price = $bp->total_price; // use total_price from BookPackage
                    $vendorInfo = $package->vendorInfo;
                    if ($vendorInfo) {
                        $vendorInfo->total_revenue = ($vendorInfo->total_revenue ?? 0) + $price;
                        $vendorInfo->save();
                    }
                    $bp->revenue_applied = true;
                    $bp->save();
                }

                if (($booking->status === 'payment return' || $booking->status === 'payment_return') && $bp->revenue_applied) {
                    $price = $bp->total_price; // use total_price from BookPackage
                    $vendorInfo = $package->vendorInfo;
                    if ($vendorInfo) {
                        $vendorInfo->total_revenue = ($vendorInfo->total_revenue ?? 0) - $price;
                        $vendorInfo->save();
                    }
                    $bp->revenue_applied = false;
                    $bp->save();
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            // swallow to avoid breaking booking flow; log if necessary
            \Log::error('BookingObserver error: ' . $e->getMessage());
        }
    }
}
