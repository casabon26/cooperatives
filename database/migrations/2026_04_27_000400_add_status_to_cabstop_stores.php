<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        if (Schema::hasTable('cabstop_stores')) {
            Schema::table('cabstop_stores', function (Blueprint $table) {
                if (!Schema::hasColumn('cabstop_stores', 'status')) {
                    $table->string('status')->nullable()->after('owner_name')->comment('seasonal|regular|ongoing');
                }
            });
        } elseif (Schema::hasTable('store_locations')) {
            Schema::table('store_locations', function (Blueprint $table) {
                if (!Schema::hasColumn('store_locations', 'status')) {
                    $table->string('status')->nullable()->after('owner_name')->comment('seasonal|regular|ongoing');
                }
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('cabstop_stores') && Schema::hasColumn('cabstop_stores', 'status')) {
            Schema::table('cabstop_stores', function (Blueprint $table) {
                $table->dropColumn('status');
            });
        } elseif (Schema::hasTable('store_locations') && Schema::hasColumn('store_locations', 'status')) {
            Schema::table('store_locations', function (Blueprint $table) {
                $table->dropColumn('status');
            });
        }
    }
};
