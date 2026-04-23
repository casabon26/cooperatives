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
        if (!Schema::hasTable('slpas')) return;

        // add new columns and remove 'link' column if present
        Schema::table('slpas', function (Blueprint $table) {
            if (!Schema::hasColumn('slpas', 'members_count')) {
                $table->integer('members_count')->nullable()->after('name');
            }
            if (!Schema::hasColumn('slpas', 'address')) {
                $table->text('address')->nullable()->after('members_count');
            }
            if (!Schema::hasColumn('slpas', 'products')) {
                $table->text('products')->nullable()->after('description');
            }
            if (!Schema::hasColumn('slpas', 'products_description')) {
                $table->text('products_description')->nullable()->after('products');
            }
            if (!Schema::hasColumn('slpas', 'business')) {
                $table->string('business')->nullable()->after('products_description');
            }

            if (Schema::hasColumn('slpas', 'link')) {
                $table->dropColumn('link');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('slpas')) return;

        Schema::table('slpas', function (Blueprint $table) {
            if (Schema::hasColumn('slpas','members_count')) $table->dropColumn('members_count');
            if (Schema::hasColumn('slpas','address')) $table->dropColumn('address');
            if (Schema::hasColumn('slpas','products')) $table->dropColumn('products');
            if (Schema::hasColumn('slpas','products_description')) $table->dropColumn('products_description');
            if (Schema::hasColumn('slpas','business')) $table->dropColumn('business');

            if (!Schema::hasColumn('slpas','link')) {
                $table->string('link')->nullable()->after('image');
            }
        });
    }
};
