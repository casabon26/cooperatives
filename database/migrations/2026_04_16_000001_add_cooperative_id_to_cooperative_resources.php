<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('cooperative_resources', function (Blueprint $table) {
            $table->unsignedBigInteger('cooperative_id')->nullable()->index()->after('id');
            // Add foreign key if using InnoDB; wrap in try/catch to avoid migration failure on non-InnoDB setups
            try {
                $table->foreign('cooperative_id')->references('id')->on('cooperatives')->onDelete('cascade');
            } catch (\Throwable $e) {
                // ignore foreign key creation errors
            }
        });
    }

    public function down()
    {
        Schema::table('cooperative_resources', function (Blueprint $table) {
            try {
                $table->dropForeign(['cooperative_id']);
            } catch (\Throwable $e) { }
            $table->dropColumn('cooperative_id');
        });
    }
};
