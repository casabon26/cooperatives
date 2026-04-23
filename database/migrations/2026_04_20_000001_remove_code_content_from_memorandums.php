<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (Schema::hasTable('memorandums')) {
            Schema::table('memorandums', function (Blueprint $table) {
                if (Schema::hasColumn('memorandums', 'code')) {
                    $table->dropColumn('code');
                }
                if (Schema::hasColumn('memorandums', 'content')) {
                    $table->dropColumn('content');
                }
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('memorandums')) {
            Schema::table('memorandums', function (Blueprint $table) {
                $table->string('code')->nullable();
                $table->text('content')->nullable();
            });
        }
    }
};
