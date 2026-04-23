<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Drop the old `card_content` column now that we store directory data separately.
        if (Schema::hasTable('cooperatives') && Schema::hasColumn('cooperatives', 'card_content')) {
            Schema::table('cooperatives', function (Blueprint $table) {
                $table->dropColumn('card_content');
            });
        }
    }

    public function down()
    {
        Schema::table('cooperatives', function (Blueprint $table) {
            $table->text('card_content')->nullable()->after('description');
        });
    }
};
