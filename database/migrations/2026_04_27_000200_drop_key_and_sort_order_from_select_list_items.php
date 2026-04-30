<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropKeyAndSortOrderFromSelectListItems extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('select_list_items')) {
            return;
        }

        // If select_list_items still has a 'key' column, migrate any StoreLocation.place
        // values that reference the key to use the human-friendly label instead.
        if (Schema::hasColumn('select_list_items', 'key')) {
            try {
                // build mapping key => label
                $rows = \DB::table('select_list_items')->select('key','label')->get();
                $map = [];
                foreach ($rows as $r) {
                    if (!empty($r->key)) {
                        $map[$r->key] = $r->label;
                    }
                }

                // Update store locations that reference the old key to use the label
                if (!empty($map) && Schema::hasTable('cabstop_stores')) {
                    foreach ($map as $k => $lbl) {
                        \DB::table('cabstop_stores')->where('place', $k)->update(['place' => $lbl]);
                    }
                }
            } catch (\Throwable $e) {
                // if any step fails, continue - dropping the columns is non-destructive to other data
            }
        }

        Schema::table('select_list_items', function (Blueprint $table) {
            // check for columns before attempting to drop to avoid errors
            if (Schema::hasColumn('select_list_items', 'key')) {
                $table->dropColumn('key');
            }
            if (Schema::hasColumn('select_list_items', 'sort_order')) {
                $table->dropColumn('sort_order');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('select_list_items')) {
            return;
        }

        Schema::table('select_list_items', function (Blueprint $table) {
            if (!Schema::hasColumn('select_list_items', 'key')) {
                $table->string('key')->nullable()->index();
            }
            if (!Schema::hasColumn('select_list_items', 'sort_order')) {
                $table->integer('sort_order')->default(0);
            }
        });
    }
}
