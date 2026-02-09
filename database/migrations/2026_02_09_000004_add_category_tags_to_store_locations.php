<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('store_locations', function (Blueprint $table) {
            $table->string('category')->nullable()->after('address');
            $table->text('tags')->nullable()->after('category');
        });
    }

    public function down()
    {
        Schema::table('store_locations', function (Blueprint $table) {
            $table->dropColumn(['category','tags']);
        });
    }
};
