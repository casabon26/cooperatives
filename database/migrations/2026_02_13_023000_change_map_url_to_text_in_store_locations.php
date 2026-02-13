<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Changes `map_url` column to TEXT to allow long Google Maps URLs.
     * Uses raw SQL to avoid requiring doctrine/dbal for column type changes.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE `store_locations` MODIFY `map_url` TEXT NULL;");
    }

    /**
     * Reverse the migrations.
     * Restores `map_url` to VARCHAR(255).
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE `store_locations` MODIFY `map_url` VARCHAR(255) NULL;");
    }
};
