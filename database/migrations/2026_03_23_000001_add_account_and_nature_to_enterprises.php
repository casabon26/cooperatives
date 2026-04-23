<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('enterprises', function (Blueprint $table) {
            if (!Schema::hasColumn('enterprises', 'account_no')) {
                $table->string('account_no')->nullable()->after('id');
            }
            if (!Schema::hasColumn('enterprises', 'nature_of_business')) {
                $table->string('nature_of_business')->nullable()->after('industry');
            }
        });
    }

    public function down()
    {
        Schema::table('enterprises', function (Blueprint $table) {
            if (Schema::hasColumn('enterprises', 'nature_of_business')) {
                $table->dropColumn('nature_of_business');
            }
            if (Schema::hasColumn('enterprises', 'account_no')) {
                $table->dropColumn('account_no');
            }
        });
    }
};
