<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        // Add owner_name to whichever table name exists (cabstop_stores or store_locations)
        if (Schema::hasTable('cabstop_stores')) {
            Schema::table('cabstop_stores', function (Blueprint $table) {
                if (!Schema::hasColumn('cabstop_stores', 'owner_name')) {
                    $table->string('owner_name')->nullable()->after('name')->comment('Admin-only owner name');
                }
            });
        } elseif (Schema::hasTable('store_locations')) {
            Schema::table('store_locations', function (Blueprint $table) {
                if (!Schema::hasColumn('store_locations', 'owner_name')) {
                    $table->string('owner_name')->nullable()->after('name')->comment('Admin-only owner name');
                }
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('cabstop_stores') && Schema::hasColumn('cabstop_stores', 'owner_name')) {
            Schema::table('cabstop_stores', function (Blueprint $table) {
                $table->dropColumn('owner_name');
            });
        } elseif (Schema::hasTable('store_locations') && Schema::hasColumn('store_locations', 'owner_name')) {
            Schema::table('store_locations', function (Blueprint $table) {
                $table->dropColumn('owner_name');
            });
        }
    }
};
