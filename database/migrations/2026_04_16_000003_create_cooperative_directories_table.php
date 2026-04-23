<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::create('cooperative_directories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cooperative_id')->unique();
            $table->string('name')->nullable();
            $table->string('sector')->nullable()->index();
            $table->string('region')->nullable()->index();
            $table->text('description')->nullable();
            $table->timestamps();

            $table->foreign('cooperative_id')->references('id')->on('cooperatives')->onDelete('cascade');
        });

        // Migrate existing JSON `card_content` values (if present) into the new table
        if (Schema::hasTable('cooperatives')) {
            $coops = DB::table('cooperatives')->select('id','card_content')->get();
            foreach ($coops as $c) {
                if (empty($c->card_content)) continue;
                $decoded = json_decode($c->card_content, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    DB::table('cooperative_directories')->insert([
                        'cooperative_id' => $c->id,
                        'name' => $decoded['name'] ?? null,
                        'sector' => $decoded['sector'] ?? null,
                        'region' => $decoded['region'] ?? null,
                        'description' => $decoded['description'] ?? null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }

    public function down()
    {
        Schema::dropIfExists('cooperative_directories');
    }
};
