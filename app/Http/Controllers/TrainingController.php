<?php
namespace App\Http\Controllers;

use App\Models\Video;
use App\Models\TrainingCompletion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class TrainingController extends Controller
{
    public function index()
    {
        $videos = Video::where('is_training', true)->orderByDesc('created_at')->paginate(12);

        $completedIds = [];
        if (auth()->check()) {
            $completedIds = TrainingCompletion::where('user_id', auth()->id())
                ->whereIn('video_id', $videos->pluck('id')->toArray())
                ->pluck('video_id')
                ->map(fn($v) => (int)$v)
                ->toArray();
        }

        return view('training.index', compact('videos','completedIds'));
    }

    public function show(Video $video)
    {
        if (!$video->is_training) abort(404);
        $completed = false;
        if (Auth::check()) {
            $completed = TrainingCompletion::where('user_id', Auth::id())->where('video_id', $video->id)->exists();
        }
        return view('training.show', compact('video','completed'));
    }

    public function complete(Request $request, Video $video)
    {
        if (!$video->is_training) abort(404);
        $user = Auth::user();
        if (!$user) return redirect('/user/login')->with('error','Please log in to complete training.');

        $tc = TrainingCompletion::firstOrNew(['user_id' => $user->id, 'video_id' => $video->id]);
        $tc->completed_at = now();
        if (empty($tc->certificate_token)) { $tc->certificate_token = Str::random(40); }
        $tc->save();

        return redirect()->route('training.show', $video)->with('success','Training marked complete — certificate available.');
    }

    public function certificate(Video $video)
    {
        $user = Auth::user();
        if (!$user) return redirect('/user/login')->with('error','Please log in to view certificate.');

        $tc = TrainingCompletion::where('user_id', $user->id)->where('video_id', $video->id)->first();
        if (!$tc) return redirect()->route('training.show', $video)->with('error','You have not completed this training yet.');

        return view('training.certificate', compact('video','tc','user'));
    }
}
