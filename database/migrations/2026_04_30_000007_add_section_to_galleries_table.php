<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('galleries')) return;
        Schema::table('galleries', function (Blueprint $table) {
            if (!Schema::hasColumn('galleries', 'section')) {
                $table->string('section')->default('livelihood')->after('alt_text');
            }
        });
    }

    public function down()
    {
        if (!Schema::hasTable('galleries')) return;
        Schema::table('galleries', function (Blueprint $table) {
            if (Schema::hasColumn('galleries', 'section')) {
                $table->dropColumn('section');
            }
        });
    }
};
