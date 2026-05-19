<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Video;
use App\Models\TrainingCompletion;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CertificatesSeeder extends Seeder
{
    public function run()
    {
        // Ensure the test user exists
        $user = User::firstOrCreate([
            'email' => 'berto@gmail.com'
        ], [
            'first_name' => 'berto',
            'last_name' => 'batumbakal',
            'name' => 'berto batumbakal',
            'email' => 'berto@gmail.com',
            'role' => 'user',
            'cp_number' => '09123456789',
            'address' => 'cabuyao',
            'sex' => 'Male',
            'age' => 25,
            'birthday' => '2000-06-22',
            'password' => Hash::make('password')
        ]);

        // Create or find a training video
        $video = Video::firstOrCreate([
            'title' => 'Sample Certificate Training'
        ], [
            'title' => 'Sample Certificate Training',
            'description' => 'A sample training used for certificate testing',
            'is_training' => true,
        ]);

        // Create training completion (certificate)
        $tc = TrainingCompletion::firstOrNew(['user_id' => $user->id, 'video_id' => $video->id]);
        $tc->completed_at = now();
        if (empty($tc->certificate_token)) $tc->certificate_token = Str::random(40);
        $tc->save();
    }
}
