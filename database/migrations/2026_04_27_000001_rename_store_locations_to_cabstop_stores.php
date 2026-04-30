<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        // Rename the table from store_locations to cabstop_stores
        Schema::rename('store_locations', 'cabstop_stores');
    }

    public function down()
    {
        // Rename back to store_locations
        Schema::rename('cabstop_stores', 'store_locations');
    }
};
