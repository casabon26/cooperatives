<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('videos', function (Blueprint $table) {
            if (!Schema::hasColumn('videos', 'highlight_landing')) {
                $table->boolean('highlight_landing')->default(false)->after('file_path');
            }
            if (!Schema::hasColumn('videos', 'highlight_enterprise')) {
                $table->boolean('highlight_enterprise')->default(false)->after('highlight_landing');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('videos', function (Blueprint $table) {
            if (Schema::hasColumn('videos', 'highlight_enterprise')) {
                $table->dropColumn('highlight_enterprise');
            }
            if (Schema::hasColumn('videos', 'highlight_landing')) {
                $table->dropColumn('highlight_landing');
            }
        });
    }
};
