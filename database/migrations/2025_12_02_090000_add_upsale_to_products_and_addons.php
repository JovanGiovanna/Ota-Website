<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('products', 'upsale')) {
            Schema::table('products', function (Blueprint $table) {
                $table->decimal('upsale', 15, 2)->nullable()->default(0)->after('nta');
            });
        }

        if (!Schema::hasColumn('addons', 'upsale')) {
            Schema::table('addons', function (Blueprint $table) {
                $table->decimal('upsale', 15, 2)->nullable()->default(0)->after('nta');
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('products', 'upsale')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn('upsale');
            });
        }

        if (Schema::hasColumn('addons', 'upsale')) {
            Schema::table('addons', function (Blueprint $table) {
                $table->dropColumn('upsale');
            });
        }
    }
};
