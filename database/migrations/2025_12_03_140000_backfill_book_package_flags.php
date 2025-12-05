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
            DB::table('book_packages')
                ->whereIn('id_book', $paidBookings)
                ->update(['revenue_applied' => true]);
        }

        // For bookings currently in 'book' status, set stock_applied to true (packages may not have stock to decrement)
        $bookIds = DB::table('bookings')->where('status', 'book')->pluck('id')->toArray();
        if (!empty($bookIds)) {
            DB::table('book_packages')
                ->whereIn('id_book', $bookIds)
                ->update(['stock_applied' => true]);
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
