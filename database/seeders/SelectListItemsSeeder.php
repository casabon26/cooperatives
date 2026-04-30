<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SelectListItemsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (!Schema::hasTable('select_list_items')) {
            return;
        }

        $now = now();

        $items = [
            // CabStop places (label is used as unique identifier now)
            ['group' => 'cabstop', 'label' => 'CabStop Bayan', 'active' => 1],
            ['group' => 'cabstop', 'label' => 'CabStop CABS', 'active' => 1],
            ['group' => 'cabstop', 'label' => 'CabStop Municipal', 'active' => 1],

            // Programs
            ['group' => 'programs', 'label' => 'Livelihood Grants', 'active' => 1],
            ['group' => 'programs', 'label' => 'Enterprise Development', 'active' => 1],
            ['group' => 'programs', 'label' => 'Product Development', 'active' => 1],
            ['group' => 'programs', 'label' => 'Market Linkages', 'active' => 1],

            // Services
            ['group' => 'services', 'label' => 'Training & Capacity Building', 'active' => 1],
            ['group' => 'services', 'label' => 'Grants & Support', 'active' => 1],
            ['group' => 'services', 'label' => 'Market Linkages', 'active' => 1],
        ];

        foreach ($items as $it) {
            DB::table('select_list_items')->updateOrInsert(
                ['group' => $it['group'], 'label' => $it['label']],
                ['active' => $it['active'], 'updated_at' => $now, 'created_at' => $now]
            );
        }
    }
}
