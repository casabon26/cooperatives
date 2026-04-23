<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('cooperative_resources', function (Blueprint $table) {
            $table->string('gdrive_link')->nullable()->after('file_path');
        });
    }

    public function down()
    {
        Schema::table('cooperative_resources', function (Blueprint $table) {
            $table->dropColumn('gdrive_link');
        });
    }
};
