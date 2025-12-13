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
        Schema::table('addons', function (Blueprint $table) {
            if (!Schema::hasColumn('addons', 'location')) {
                $table->text('location')->default('-')->after('pax');
            }
            if (!Schema::hasColumn('addons', 'phone')) {
                $table->string('phone', 50)->default('-')->after('location');
            }
            if (!Schema::hasColumn('addons', 'address')) {
                $table->text('address')->nullable()->after('location');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('addons', function (Blueprint $table) {
            if (Schema::hasColumn('addons', 'address')) {
                $table->dropColumn('address');
            }
            if (Schema::hasColumn('addons', 'phone')) {
                $table->dropColumn('phone');
            }
            if (Schema::hasColumn('addons', 'location')) {
                $table->dropColumn('location');
            }
        });
    }
};
