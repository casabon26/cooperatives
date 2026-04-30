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
        if (Schema::hasTable('select_list_items')) {
            // Table already exists, nothing to do
            return;
        }

        Schema::create('select_list_items', function (Blueprint $table) {
            $table->id();
            $table->string('group')->index();
            $table->string('key')->nullable();
            $table->string('label');
            $table->integer('sort_order')->default(0);
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('select_list_items')) {
            Schema::dropIfExists('select_list_items');
        }
    }
};
