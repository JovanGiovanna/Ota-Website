<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // products: upsale -> upsell
        if (Schema::hasColumn('products', 'upsale')) {
            Schema::table('products', function (Blueprint $table) {
                $table->renameColumn('upsale', 'upsell');
            });
        }

        // addons: upsale -> upsell
        if (Schema::hasColumn('addons', 'upsale')) {
            Schema::table('addons', function (Blueprint $table) {
                $table->renameColumn('upsale', 'upsell');
            });
        }

        // packages: upsale -> upsell
        if (Schema::hasColumn('packages', 'upsale')) {
            Schema::table('packages', function (Blueprint $table) {
                $table->renameColumn('upsale', 'upsell');
            });
        }
    }

    public function down(): void
    {
        // products: upsell -> upsale
        if (Schema::hasColumn('products', 'upsell')) {
            Schema::table('products', function (Blueprint $table) {
                $table->renameColumn('upsell', 'upsale');
            });
        }

        // addons: upsell -> upsale
        if (Schema::hasColumn('addons', 'upsell')) {
            Schema::table('addons', function (Blueprint $table) {
                $table->renameColumn('upsell', 'upsale');
            });
        }

        // packages: upsell -> upsale
        if (Schema::hasColumn('packages', 'upsell')) {
            Schema::table('packages', function (Blueprint $table) {
                $table->renameColumn('upsell', 'upsale');
            });
        }
    }
};
