<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Gallery;
use Illuminate\Support\Facades\File;

class GallerySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Seed site-wide gallery images from public/galleries (livelihood/featured)
        $siteDir = public_path('galleries');
        if (File::exists($siteDir)) {
            $files = File::files($siteDir);
            foreach ($files as $f) {
                $path = 'galleries/' . $f->getFilename();
                if (Gallery::where('path', $path)->exists()) continue;
                Gallery::create([
                    'title' => pathinfo($f->getFilename(), PATHINFO_FILENAME),
                    'description' => null,
                    'path' => $path,
                    'alt_text' => null,
                    'published' => true,
                    'section' => 'livelihood',
                ]);
            }
        }

        // Seed cooperative-specific images (kept as cooperative section)
        $coopDir = public_path('cooperative_galleries');
        if (File::exists($coopDir)) {
            $files = File::files($coopDir);
            foreach ($files as $f) {
                $path = 'cooperative_galleries/' . $f->getFilename();
                if (Gallery::where('path', $path)->exists()) continue;
                Gallery::create([
                    'title' => pathinfo($f->getFilename(), PATHINFO_FILENAME),
                    'description' => null,
                    'path' => $path,
                    'alt_text' => null,
                    'published' => true,
                    'section' => 'cooperative',
                ]);
            }
        }
    }
}
