<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Cooperative;
use App\Models\CooperativeProfile;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin users (use DB-stored credentials)
        User::factory()->create([ 
            'name'=>'Gov Admin', 
            'email'=>'admin@gov.test', 
            'role'=>'gov_admin', 
            'password' => \Illuminate\Support\Facades\Hash::make(\Illuminate\Support\Str::random(16))
        ]);
        User::factory()->create([ 
            'name'=>'Coop Admin', 
            'email'=>'coopadmin@gov.test', 
            'role'=>'cooperative_admin', 
            'password' => \Illuminate\Support\Facades\Hash::make(\Illuminate\Support\Str::random(16))
        ]);

        // Seed 34 sample cooperatives
        for ($i=1;$i<=34;$i++) {
            $c = Cooperative::create([
                'name' => 'Cooperative '.$i,
                'sector' => ['Agriculture','Finance','Service'][array_rand(['Agriculture','Finance','Service'])],
                'region' => 'Region '.(($i%5)+1),
                'description' => 'Sample cooperative '.$i,
                'status' => 'active'
            ]);

            CooperativeProfile::create([
                'cooperative_id' => $c->id,
                'objectives' => 'Improve member welfare',
                'services' => 'Savings, Loans',
                'contact_info' => 'info@coop'.$i.'.test'
            ]);
        }

        // Attach the cooperative admin user to all cooperatives as admin
        $coopAdmin = User::where('role','cooperative_admin')->first();
        if ($coopAdmin) {
            $all = Cooperative::pluck('id')->toArray();
            foreach ($all as $cid) {
                $coopAdmin->cooperatives()->attach($cid, ['role' => 'admin']);
            }
        }

        // SLPA sample data
        if (class_exists(\Database\Seeders\SlpaSeeder::class)) {
            $this->call(\Database\Seeders\SlpaSeeder::class);
        }
    }
}
