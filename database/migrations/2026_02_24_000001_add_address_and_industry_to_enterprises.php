<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('enterprises', function (Blueprint $table) {
            if (!Schema::hasColumn('enterprises', 'address')) {
                $table->string('address')->nullable()->after('name');
            }
            if (!Schema::hasColumn('enterprises', 'industry')) {
                $table->string('industry')->nullable()->after('address');
            }
        });
    }

    public function down()
    {
        Schema::table('enterprises', function (Blueprint $table) {
            if (Schema::hasColumn('enterprises', 'industry')) {
                $table->dropColumn('industry');
            }
            if (Schema::hasColumn('enterprises', 'address')) {
                $table->dropColumn('address');
            }
        });
    }
};
