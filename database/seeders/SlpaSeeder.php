<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Slpa;

class SlpaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $samples = [
            [
                'name' => 'SLPA Community Training',
                'description' => 'Community-based training program for small enterprises and cooperatives.',
                'members_count' => 120,
                'address' => 'Brgy. Centro, Sample Town',
                'products' => [
                    ['name' => 'Handicrafts', 'description' => 'Handmade woven and crafted items produced by community members.'],
                    ['name' => 'Dried Fruits', 'description' => 'Locally dried mangoes and bananas packaged for retail.'],
                ],
                'business' => 'Small Enterprise Support',
            ],
            [
                'name' => 'SLPA Market Linkage Support',
                'description' => 'Assistance for market access and trade linkages.',
                'members_count' => 45,
                'address' => 'Brgy. North, Sample Town',
                'products' => [
                    ['name' => 'Agricultural Produce', 'description' => 'Fresh vegetables and fruits supplied by members.'],
                ],
                'business' => 'Market Facilitation',
            ],
            [
                'name' => 'SLPA Product Development',
                'description' => 'Support for product improvement, packaging, and value-adding.',
                'members_count' => 30,
                'address' => 'Brgy. East, Sample Town',
                'products' => [
                    ['name' => 'Processed Food Items', 'description' => 'Value-added local food products such as jams and pickles.'],
                ],
                'business' => 'Product Development',
            ],
        ];

        foreach ($samples as $s) {
            Slpa::create($s);
        }
    }
}
