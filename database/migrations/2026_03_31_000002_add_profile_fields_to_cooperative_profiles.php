<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('cooperative_profiles', function (Blueprint $table) {
            if (!Schema::hasColumn('cooperative_profiles', 'mission')) {
                $table->text('mission')->nullable()->after('objectives');
            }
            if (!Schema::hasColumn('cooperative_profiles', 'vision')) {
                $table->text('vision')->nullable()->after('mission');
            }
            if (!Schema::hasColumn('cooperative_profiles', 'achievements')) {
                $table->text('achievements')->nullable()->after('vision');
            }
            if (!Schema::hasColumn('cooperative_profiles', 'years')) {
                $table->string('years')->nullable()->after('achievements');
            }
            if (!Schema::hasColumn('cooperative_profiles', 'members_count')) {
                $table->integer('members_count')->nullable()->after('years');
            }
            if (!Schema::hasColumn('cooperative_profiles', 'address')) {
                $table->string('address')->nullable()->after('members_count');
            }
            if (!Schema::hasColumn('cooperative_profiles', 'gallery')) {
                $table->json('gallery')->nullable()->after('address');
            }
        });
    }

    public function down()
    {
        Schema::table('cooperative_profiles', function (Blueprint $table) {
            if (Schema::hasColumn('cooperative_profiles', 'mission')) $table->dropColumn('mission');
            if (Schema::hasColumn('cooperative_profiles', 'vision')) $table->dropColumn('vision');
            if (Schema::hasColumn('cooperative_profiles', 'achievements')) $table->dropColumn('achievements');
            if (Schema::hasColumn('cooperative_profiles', 'years')) $table->dropColumn('years');
            if (Schema::hasColumn('cooperative_profiles', 'members_count')) $table->dropColumn('members_count');
            if (Schema::hasColumn('cooperative_profiles', 'address')) $table->dropColumn('address');
            if (Schema::hasColumn('cooperative_profiles', 'gallery')) $table->dropColumn('gallery');
        });
    }
};
