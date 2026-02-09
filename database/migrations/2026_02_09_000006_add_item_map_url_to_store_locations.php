<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('store_locations', function (Blueprint $table) {
            $table->string('item_map_url')->nullable()->after('map_url');
        });
    }

    public function down()
    {
        Schema::table('store_locations', function (Blueprint $table) {
            $table->dropColumn('item_map_url');
        });
    }
};
