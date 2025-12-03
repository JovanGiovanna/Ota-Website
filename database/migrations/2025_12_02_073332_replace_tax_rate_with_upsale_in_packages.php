<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            // Drop tax_rate if it exists
            if (Schema::hasColumn('packages', 'tax_rate')) {
                $table->dropColumn('tax_rate');
            }
            // Add upsale column
            $table->decimal('upsale', 15, 2)->nullable()->default(0)->after('pax_paid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            // Drop upsale
            if (Schema::hasColumn('packages', 'upsale')) {
                $table->dropColumn('upsale');
            }
            // Re-add tax_rate if needed
            $table->decimal('tax_rate', 5, 2)->nullable()->default(0)->after('pax_paid');
        });
    }
};
