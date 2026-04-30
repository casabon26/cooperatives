<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('galleries')) return;

        // Classify existing gallery rows: if path appears in any cooperative->gallery arrays,
        // mark section = 'cooperative'. Otherwise if section empty set to 'livelihood'.
        try {
            if (class_exists(\App\Models\Cooperative::class) && class_exists(\App\Models\Gallery::class)) {
                $coopPaths = [];
                foreach (\App\Models\Cooperative::all() as $c) {
                    $g = $c->gallery;
                    if (is_array($g)) {
                        foreach ($g as $p) {
                            if ($p) $coopPaths[$p] = true;
                        }
                    }
                }

                // Update galleries that match cooperative paths
                if (!empty($coopPaths)) {
                    $paths = array_keys($coopPaths);
                    \App\Models\Gallery::whereIn('path', $paths)->update(['section' => 'cooperative']);
                }

                // Ensure remaining rows have a section (set to livelihood if empty)
                \App\Models\Gallery::whereNull('section')->orWhere('section','')->update(['section' => 'livelihood']);
            }
        } catch (\Throwable $e) {
            // don't break migrations on data errors
        }
    }

    public function down()
    {
        // no-op data migration
    }
};
