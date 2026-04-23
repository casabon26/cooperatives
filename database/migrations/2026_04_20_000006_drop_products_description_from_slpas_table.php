<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('slpas')) return;
        Schema::table('slpas', function (Blueprint $table) {
            if (Schema::hasColumn('slpas', 'products_description')) {
                $table->dropColumn('products_description');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (!Schema::hasTable('slpas')) return;
        Schema::table('slpas', function (Blueprint $table) {
            if (!Schema::hasColumn('slpas', 'products_description')) {
                $table->text('products_description')->nullable()->after('products');
            }
        });
    }
};
