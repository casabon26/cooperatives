<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::table('videos', function (Blueprint $table) {
            if (!Schema::hasColumn('videos', 'length')) {
                $table->integer('length')->nullable()->after('file_path'); // length in seconds
            }
        });
    }
    public function down() {
        Schema::table('videos', function (Blueprint $table) {
            if (Schema::hasColumn('videos', 'length')) {
                $table->dropColumn('length');
            }
        });
    }
};
