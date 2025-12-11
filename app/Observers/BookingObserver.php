<?php

namespace App\Observers;

use App\Models\Booking;
use App\Models\BookProduct;
use App\Models\BookPackage;
use App\Models\Product;
use App\Models\EmailSetting;
use App\Mail\BookingNotificationToHeadbase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class BookingObserver
{
    /** Handle the Booking "created" event. */
    public function created(Booking $booking)
    {
        Log::info('BookingObserver created triggered for booking ID: ' . $booking->id . ', status: ' . $booking->status);
        
        // Send email notification to headbase
        $this->sendHeadbaseNotification($booking);
        
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

        // Update all child records to match booking status
        BookPackage::where('id_book', $booking->id)->update(['status' => $current]);
        BookProduct::where('id_book', $booking->id)->update(['status' => $current]);
        \App\Models\BookAddon::where('id_book', $booking->id)->update(['status' => $current]);

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

            // 2. Handle products inside packages (from products_data JSON)
            $bookPackages = BookPackage::where('id_book', $booking->id)->get();

            foreach ($bookPackages as $bp) {
                $package = $bp->package;
                if (!$package) continue;

                // Get quantity for this package booking (default to 1)
                $packageQuantity = $bp->quantity ?? 1;

                // Get products from JSON products_data
                $productsData = is_array($package->products_data) ? $package->products_data : [];

                // When booking becomes 'book' -> decrease stock for each product inside the package
                if ($booking->status === 'book' && !$bp->stock_applied) {
                    Log::info("Processing package products for stock decrease", ['package_id' => $package->id, 'products_count' => count($productsData)]);
                    foreach ($productsData as $productData) {
                        $productId = $productData['id'] ?? null;
                        if (!$productId) continue;
                        
                        $product = Product::find($productId);
                        if (!$product) continue;
                        
                        // Calculate stock needed based on total pax and capacity per stock
                        // pax in JSON = capacity per stock (e.g., 1 stock = 3 people)
                        // booking amount = total people
                        // stock needed = ceil(total people / capacity per stock)
                        $capacityPerStock = $productData['pax'] ?? 1;
                        $totalPeople = $booking->amount ?? $packageQuantity;
                        $stockNeeded = ceil($totalPeople / $capacityPerStock) * $packageQuantity;
                        
                        Log::info("Decreasing product stock", [
                            'product' => $product->name, 
                            'from' => $product->jumlah, 
                            'total_people' => $totalPeople,
                            'capacity_per_stock' => $capacityPerStock,
                            'package_quantity' => $packageQuantity,
                            'stock_needed' => $stockNeeded
                        ]);
                        
                        $product->jumlah = max(0, ($product->jumlah ?? 0) - $stockNeeded);
                        $product->save();
                    }
                    $bp->stock_applied = true;
                    $bp->save();
                    Log::info("Stock decrease completed");
                }

                // When booking becomes 'cancelled' -> restore stock for package products
                if ($booking->status === 'cancelled' && $bp->stock_applied) {
                    foreach ($productsData as $productData) {
                        $productId = $productData['id'] ?? null;
                        if (!$productId) continue;
                        
                        $product = Product::find($productId);
                        if (!$product) continue;
                        
                        // Restore stock using same calculation as decrease
                        $capacityPerStock = $productData['pax'] ?? 1;
                        $totalPeople = $booking->amount ?? $packageQuantity;
                        $stockNeeded = ceil($totalPeople / $capacityPerStock) * $packageQuantity;
                        
                        $product->jumlah = ($product->jumlah ?? 0) + $stockNeeded;
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

            // 3. Handle standalone addons booked (from book_addons)
            $bookAddons = \App\Models\BookAddon::where('id_book', $booking->id)->get();

            foreach ($bookAddons as $ba) {
                $addon = $ba->addon;
                if (!$addon) continue;

                // Get amount (quantity) for this addon booking
                $addonAmount = $ba->amount ?? 1;

                // When booking becomes 'book' -> decrease stock for addon, multiplied by amount
                if ($booking->status === 'book' && !$ba->stock_applied) {
                    if ($addon) {
                        $addon->jumlah = max(0, ($addon->jumlah ?? 0) - $addonAmount);
                        $addon->save();
                    }
                    $ba->stock_applied = true;
                    $ba->save();
                }

                // When booking becomes 'paid' -> mark revenue applied and update revenue
                if ($booking->status === 'paid' && !$ba->revenue_applied) {
                    $price = $ba->total_price; // use total_price from BookAddon
                    if ($addon && $addon->id_vendor) {
                        $vendorInfo = $addon->vendor->vendorInfo;
                        if ($vendorInfo) {
                            $vendorInfo->total_revenue = ($vendorInfo->total_revenue ?? 0) + $price;
                            $vendorInfo->save();
                        }
                    }
                    $ba->revenue_applied = true;
                    $ba->save();
                }

                // When booking becomes 'cancelled' -> restore stock if previously applied
                if ($booking->status === 'cancelled' && $ba->stock_applied) {
                    if ($addon) {
                        $addon->jumlah = ($addon->jumlah ?? 0) + $addonAmount;
                        $addon->save();
                    }
                    $ba->stock_applied = false;
                    $ba->save();
                }

                // When booking becomes 'payment return' -> remove revenue flag and update revenue
                if (($booking->status === 'payment return' || $booking->status === 'payment_return') && $ba->revenue_applied) {
                    $price = $ba->total_price; // use total_price from BookAddon
                    if ($addon && $addon->id_vendor) {
                        $vendorInfo = $addon->vendor->vendorInfo;
                        if ($vendorInfo) {
                            $vendorInfo->total_revenue = ($vendorInfo->total_revenue ?? 0) - $price;
                            $vendorInfo->save();
                        }
                    }
                    $ba->revenue_applied = false;
                    $ba->save();
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            // swallow to avoid breaking booking flow; log if necessary
            \Log::error('BookingObserver error: ' . $e->getMessage());
        }
    }

    /** Send booking notification to active headbase emails */
    private function sendHeadbaseNotification(Booking $booking)
    {
        try {
            // Get all active email settings
            $activeEmails = EmailSetting::where('is_active', true)->get();

            if ($activeEmails->isEmpty()) {
                Log::info('No active headbase emails configured. Skipping notification.');
                return;
            }

            // Load booking relationships for email
            $booking->load(['user', 'packages', 'products', 'addons']);

            // Send email to each active recipient
            foreach ($activeEmails as $emailSetting) {
                try {
                    Mail::to($emailSetting->email)->send(new BookingNotificationToHeadbase($booking));
                    Log::info("Booking notification sent to: {$emailSetting->email} for booking: {$booking->booking_code}");
                } catch (\Exception $e) {
                    Log::error("Failed to send booking notification to {$emailSetting->email}: " . $e->getMessage());
                }
            }
        } catch (\Exception $e) {
            Log::error('Error in sendHeadbaseNotification: ' . $e->getMessage());
        }
    }
}
