<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\VideoProgress;
use App\Models\TrainingCompletion;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class VideoProgressController extends Controller
{
    public function get(Request $request)
    {
        $videoId = $request->query('videoId');
        $userId = $request->query('userId') ?? Auth::id();
        if (!$videoId) return response()->json(null, 404);
        $prog = VideoProgress::where('video_id', $videoId)->where('user_id', $userId)->first();
        if (!$prog) return response()->json(null, 204);
        return response()->json([ 'currentTime' => (float)$prog->current_time, 'totalDuration' => (float)$prog->total_duration, 'progressPercent' => (int)$prog->progress_percent ]);
    }

    public function save(Request $request)
    {
        $data = $request->validate([
            'userId' => 'nullable',
            'videoId' => 'required|string',
            'progressPercent' => 'required|integer|min:0|max:100',
            'currentTime' => 'required|numeric|min:0',
            'totalDuration' => 'required|numeric|min:0'
        ]);

        $userId = $data['userId'] ?? Auth::id();
        $vp = VideoProgress::updateOrCreate(
            ['user_id' => $userId, 'video_id' => $data['videoId']],
            ['current_time' => $data['currentTime'], 'total_duration' => $data['totalDuration'], 'progress_percent' => $data['progressPercent']]
        );

        return response()->json(['success' => true]);
    }

    public function complete(Request $request)
    {
        $data = $request->validate([
            'userId' => 'nullable',
            'videoId' => 'required|string'
        ]);
        $userId = $data['userId'] ?? Auth::id();
        // create or update training completion
        $tc = TrainingCompletion::firstOrCreate([
            'user_id' => $userId,
            'video_id' => $data['videoId']
        ], [ 'completed_at' => now() ]);

        return response()->json(['success' => true]);
    }

    /**
     * Send a lightweight completion notification email to the logged-in user.
     * Used for quick-dev testing only (sends a short "hi" message).
     */
    public function sendCompletionEmail(Request $request)
    {
        $user = Auth::user();
        if (!$user || !$user->email) {
            return response()->json(['error' => 'Unauthenticated or missing email'], 401);
        }

        try {
            Mail::raw('hi', function($m) use ($user) {
                $m->to($user->email)->subject('Training progress notification');
            });
        } catch (\Exception $e) {
            return response()->json(['error' => 'Mail failed', 'message' => $e->getMessage()], 500);
        }

        return response()->json(['sent' => true]);
    }
}
