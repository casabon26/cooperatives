<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // add profile-like columns to cooperatives if not present
        Schema::table('cooperatives', function (Blueprint $table) {
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
                $table->string('years')->nullable();
            }
            if (!Schema::hasColumn('cooperatives', 'members_count')) {
                $table->integer('members_count')->nullable();
            }
            if (!Schema::hasColumn('cooperatives', 'address')) {
                $table->string('address')->nullable();
            }
            if (!Schema::hasColumn('cooperatives', 'gallery')) {
                $table->json('gallery')->nullable();
            }
            if (!Schema::hasColumn('cooperatives', 'contact_phone')) {
                $table->string('contact_phone')->nullable();
            }
            if (!Schema::hasColumn('cooperatives', 'contact_email')) {
                $table->string('contact_email')->nullable();
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

        // copy data from cooperative_profiles into cooperatives
        if (Schema::hasTable('cooperative_profiles')) {
            $profiles = DB::table('cooperative_profiles')->get();
            foreach ($profiles as $p) {
                $update = [];
                foreach (['objectives','services','contact_info','mission','vision','achievements','years','members_count','address'] as $c) {
                    if (isset($p->$c)) { $update[$c] = $p->$c; }
                }
                // gallery handling: ensure json array
                if (isset($p->gallery)) {
                    try {
                        $g = is_string($p->gallery) ? json_decode($p->gallery, true) : $p->gallery;
                        if ($g === null) { $g = [$p->gallery]; }
                        $update['gallery'] = json_encode(array_values((array) $g));
                    } catch (\Throwable $e) {
                        $update['gallery'] = json_encode([$p->gallery]);
                    }
                }
                if (!empty($update)) {
                    DB::table('cooperatives')->where('id', $p->cooperative_id)->update($update);
                }
            }

            // drop the cooperative_profiles table now that data is copied
            Schema::dropIfExists('cooperative_profiles');
        }
    }

    public function down()
    {
        // recreate cooperative_profiles (best-effort) if missing
        if (!Schema::hasTable('cooperative_profiles')) {
            Schema::create('cooperative_profiles', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('cooperative_id')->index();
                $table->text('objectives')->nullable();
                $table->text('services')->nullable();
                $table->text('contact_info')->nullable();
                $table->text('mission')->nullable();
                $table->text('vision')->nullable();
                $table->text('achievements')->nullable();
                $table->string('years')->nullable();
                $table->integer('members_count')->nullable();
                $table->string('address')->nullable();
                $table->json('gallery')->nullable();
                $table->timestamps();
            });
        }

        // remove added columns from cooperatives
        Schema::table('cooperatives', function (Blueprint $table) {
            foreach (['objectives','services','contact_info','mission','vision','achievements','years','members_count','address','gallery','contact_phone','contact_email','facebook','twitter','instagram','linkedin','map_embed','operating_hours'] as $c) {
                if (Schema::hasColumn('cooperatives', $c)) {
                    $table->dropColumn($c);
                }
            }
        });
    }
};
