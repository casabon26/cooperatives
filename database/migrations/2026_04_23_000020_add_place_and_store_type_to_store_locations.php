<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('store_locations', function (Blueprint $table) {
            if (!Schema::hasColumn('store_locations', 'place')) {
                $table->string('place')->nullable()->after('tags')->index();
            }
            if (!Schema::hasColumn('store_locations', 'store_type')) {
                $table->string('store_type')->nullable()->after('place')->comment('e.g. food, non_food');
            }
        });
    }

    public function down()
    {
        Schema::table('store_locations', function (Blueprint $table) {
            if (Schema::hasColumn('store_locations', 'store_type')) {
                $table->dropColumn('store_type');
            }
            if (Schema::hasColumn('store_locations', 'place')) {
                $table->dropColumn('place');
            }
        });
    }
};
