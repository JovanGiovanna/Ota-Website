<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Backfill revenue_applied for already-paid/completed bookings
        $paidBookings = DB::table('bookings')
            ->whereIn('status', ['paid', 'completed'])
            ->pluck('id')
            ->toArray();

        if (!empty($paidBookings)) {
            DB::table('book_products')
                ->whereIn('id_book', $paidBookings)
                ->update(['revenue_applied' => true]);
        }

        // For bookings currently in 'book' status, ensure stock_applied is set and product.jumlah adjusted
        $bookIds = DB::table('bookings')->where('status', 'book')->pluck('id')->toArray();
        if (!empty($bookIds)) {
            $bookProducts = DB::table('book_products')->whereIn('id_book', $bookIds)->get();
            foreach ($bookProducts as $bp) {
                if (!$bp->stock_applied) {
                    // decrement product jumlah
                    DB::table('products')->where('id', $bp->id_product)->decrement('jumlah', (int)($bp->amount ?? 1));
                    DB::table('book_products')->where('id', $bp->id)->update(['stock_applied' => true]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op: do not revert backfill automatically
    }
};
