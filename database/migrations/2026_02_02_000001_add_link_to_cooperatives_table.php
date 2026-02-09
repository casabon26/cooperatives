<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('cooperatives', function (Blueprint $table) {
            $table->string('link')->nullable()->after('description')->index();
        });
    }

    public function down()
    {
        Schema::table('cooperatives', function (Blueprint $table) {
            $table->dropColumn('link');
        });
    }
};
