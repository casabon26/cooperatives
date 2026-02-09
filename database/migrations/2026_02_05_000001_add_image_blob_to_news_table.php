<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('news', function (Blueprint $table) {
            // Store raw binary image or base64 in longText if needed
            if (!Schema::hasColumn('news', 'image_data')) {
                $table->longText('image_data')->nullable()->after('image');
            }
            if (!Schema::hasColumn('news', 'image_mime')) {
                $table->string('image_mime')->nullable()->after('image_data');
            }
            if (!Schema::hasColumn('news', 'image_filename')) {
                $table->string('image_filename')->nullable()->after('image_mime');
            }
        });
    }

    public function down()
    {
        Schema::table('news', function (Blueprint $table) {
            if (Schema::hasColumn('news', 'image_data')) { $table->dropColumn('image_data'); }
            if (Schema::hasColumn('news', 'image_mime')) { $table->dropColumn('image_mime'); }
            if (Schema::hasColumn('news', 'image_filename')) { $table->dropColumn('image_filename'); }
        });
    }
};
