<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // For existing book_packages, ensure flags are set according to booking status
        $bookPackages = DB::table('book_packages')
            ->join('bookings', 'book_packages.id_book', '=', 'bookings.id')
            ->select('book_packages.id as bp_id', 'bookings.status as booking_status', 'book_packages.id_package')
            ->get();

        foreach ($bookPackages as $bp) {
            // If booking is 'book' then apply stock for package products (if not already applied)
            if ($bp->booking_status === 'book') {
                // decrement each product in the package by 1
                $packageProducts = DB::table('package_products')->where('id_package', $bp->id_package)->pluck('id_product');
                foreach ($packageProducts as $productId) {
                    DB::table('products')->where('id', $productId)->decrement('jumlah', 1);
                }
                DB::table('book_packages')->where('id', $bp->bp_id)->update(['stock_applied' => true]);
            }

            // If booking is 'paid' then mark revenue_applied for package
            if ($bp->booking_status === 'paid') {
                DB::table('book_packages')->where('id', $bp->bp_id)->update(['revenue_applied' => true]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Do not attempt to revert product.quantity changes automatically.
        DB::table('book_packages')->update(['stock_applied' => false, 'revenue_applied' => false]);
    }
};
