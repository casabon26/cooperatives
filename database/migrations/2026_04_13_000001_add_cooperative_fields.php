<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        // Only add columns that do not already exist to avoid duplicate column errors
        Schema::table('cooperatives', function (Blueprint $table) {
            if (!Schema::hasColumn('cooperatives', 'address')) {
                $table->text('address')->nullable();
            }
            if (!Schema::hasColumn('cooperatives', 'contact_phone')) {
                $table->string('contact_phone')->nullable();
            }
            if (!Schema::hasColumn('cooperatives', 'contact_email')) {
                $table->string('contact_email')->nullable()->index();
            }

            if (!Schema::hasColumn('cooperatives', 'image')) {
                $table->string('image')->nullable();
            }
            if (!Schema::hasColumn('cooperatives', 'link')) {
                $table->string('link')->nullable();
            }
            if (!Schema::hasColumn('cooperatives', 'objectives')) {
                $table->text('objectives')->nullable();
            }
            if (!Schema::hasColumn('cooperatives', 'services')) {
                $table->text('services')->nullable();
            }
            if (!Schema::hasColumn('cooperatives', 'contact_info')) {
                $table->text('contact_info')->nullable();
            }
            if (!Schema::hasColumn('cooperatives', 'mission')) {
                $table->text('mission')->nullable();
            }
            if (!Schema::hasColumn('cooperatives', 'vision')) {
                $table->text('vision')->nullable();
            }
            if (!Schema::hasColumn('cooperatives', 'achievements')) {
                $table->text('achievements')->nullable();
            }
            if (!Schema::hasColumn('cooperatives', 'years')) {
                $table->integer('years')->nullable();
            }
            if (!Schema::hasColumn('cooperatives', 'members_count')) {
                $table->integer('members_count')->nullable();
            }
            if (!Schema::hasColumn('cooperatives', 'gallery')) {
                $table->json('gallery')->nullable();
            }
            if (!Schema::hasColumn('cooperatives', 'facebook')) {
                $table->string('facebook')->nullable();
            }
            if (!Schema::hasColumn('cooperatives', 'twitter')) {
                $table->string('twitter')->nullable();
            }
            if (!Schema::hasColumn('cooperatives', 'instagram')) {
                $table->string('instagram')->nullable();
            }
            if (!Schema::hasColumn('cooperatives', 'linkedin')) {
                $table->string('linkedin')->nullable();
            }
            if (!Schema::hasColumn('cooperatives', 'map_embed')) {
                $table->text('map_embed')->nullable();
            }
            if (!Schema::hasColumn('cooperatives', 'operating_hours')) {
                $table->string('operating_hours')->nullable();
            }
        });
    }

    public function down()
    {
        Schema::table('cooperatives', function (Blueprint $table) {
            $table->dropColumn([
                'address','contact_phone','contact_email','image','link','objectives','services','contact_info',
                'mission','vision','achievements','years','members_count','gallery','facebook','twitter','instagram','linkedin','map_embed','operating_hours'
            ]);
        });
    }
};
