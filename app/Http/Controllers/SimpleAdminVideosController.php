<?php
namespace App\Http\Controllers;

use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class SimpleAdminVideosController extends Controller
{
    protected function ensureAdmin()
    {
        if (!session('admin_authenticated')) {
            abort(403, 'Forbidden');
        }
    }

    public function index()
    {
        $this->ensureAdmin();
        $videos = Video::orderByDesc('created_at')->paginate(20);
        return view('admin.videos.index', compact('videos'));
    }

    public function store(Request $request)
    {
        $this->ensureAdmin();
        try {
            // Use extension-based mime check to avoid relying on PHP's fileinfo extension
            $fileRule = 'nullable|file|mimes:mp4,webm,ogg|max:204800';

            $data = $request->validate([
                'title' => 'required|string|max:191',
                'description' => 'nullable|string',
                'url' => 'nullable|url',
                // increased max to 200MB (value is in KB)
                'file' => $fileRule,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect('/admin/manage-videos')
                ->withErrors($e->validator)
                ->withInput()
                ->with('error', 'Validation failed when uploading the video.');
        }

        try {
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $filename = time().'_'.preg_replace('/[^A-Za-z0-9._-]/', '_', $file->getClientOriginalName());
                $dest = storage_path('app/public/videos');
                if (!is_dir($dest)) { mkdir($dest, 0755, true); }
                $file->move($dest, $filename);
                $data['file_path'] = 'videos/'.$filename;
                // clear url if file uploaded
                $data['url'] = null;
            }

            Video::create($data);
        } catch (\Throwable $e) {
            Log::error('Video store failed', ['err' => $e->getMessage()]);
            return redirect('/admin/manage-videos')->withInput()->with('error', 'Failed to save video. Check logs for details.');
        }

        return redirect('/admin/manage-videos')->with('success', 'Video added');
    }

    public function edit(Video $video)
    {
        $this->ensureAdmin();
        return view('admin.videos.edit', compact('video'));
    }

    public function update(Request $request, Video $video)
    {
        $this->ensureAdmin();
        try {
            // Use extension-based mime check to avoid relying on PHP's fileinfo extension
            $fileRule = 'nullable|file|mimes:mp4,webm,ogg|max:204800';

            $data = $request->validate([
                'title' => 'required|string|max:191',
                'description' => 'nullable|string',
                'url' => 'nullable|url',
                'file' => $fileRule,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect('/admin/manage-videos')
                ->withErrors($e->validator)
                ->withInput()
                ->with('error', 'Validation failed when uploading the video.');
        }

        try {
            if ($request->hasFile('file')) {
                if ($video->file_path) {
                    try { Storage::disk('public')->delete($video->file_path); } catch (\Throwable $e) { Log::warning('Delete failed', ['err'=>$e->getMessage()]); }
                }
                $file = $request->file('file');
                $filename = time().'_'.preg_replace('/[^A-Za-z0-9._-]/', '_', $file->getClientOriginalName());
                $dest = storage_path('app/public/videos');
                if (!is_dir($dest)) { mkdir($dest, 0755, true); }
                $file->move($dest, $filename);
                $data['file_path'] = 'videos/'.$filename;
                $data['url'] = null;
            }

            $video->update($data);
        } catch (\Throwable $e) {
            Log::error('Video update failed', ['err' => $e->getMessage()]);
            return redirect('/admin/manage-videos')->withInput()->with('error', 'Failed to update video. Check logs for details.');
        }

        return redirect('/admin/manage-videos')->with('success', 'Video updated');
    }

    public function destroy(Video $video)
    {
        $this->ensureAdmin();
        if ($video->file_path) {
            try { Storage::disk('public')->delete($video->file_path); } catch (\Throwable $e) { Log::warning('Delete failed', ['err'=>$e->getMessage()]); }
        }
        $video->delete();
        return redirect('/admin/manage-videos')->with('success', 'Video deleted');
    }
}
