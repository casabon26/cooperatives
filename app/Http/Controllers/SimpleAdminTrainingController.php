<?php
namespace App\Http\Controllers;

use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class SimpleAdminTrainingController extends Controller
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
        $videos = Video::where('is_training', true)->orderByDesc('created_at')->paginate(20);
        return view('admin.training.index', compact('videos'));
    }

    public function create()
    {
        $this->ensureAdmin();
        return view('admin.training.create');
    }

    public function store(Request $request)
    {
        $this->ensureAdmin();
        $fileRule = 'nullable|file|mimes:mp4,webm,ogg|max:204800';
        $data = $request->validate([
            'title' => 'required|string|max:191',
            'description' => 'nullable|string',
            'url' => 'nullable|url',
            'file' => $fileRule,
            'length' => 'nullable|integer|min:1',
        ]);

        try {
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $filename = time().'_'.preg_replace('/[^A-Za-z0-9._-]/', '_', $file->getClientOriginalName());
                $dest = storage_path('app/public/videos');
                if (!is_dir($dest)) { mkdir($dest, 0755, true); }
                $file->move($dest, $filename);
                $data['file_path'] = 'videos/'.$filename;
                $data['url'] = null;
            }
            $data['is_training'] = true;
            Video::create($data);
        } catch (\Throwable $e) {
            Log::error('Training store failed', ['err' => $e->getMessage()]);
            return redirect('/admin/manage-training')->with('error', 'Failed to save training video.');
        }

        return redirect('/admin/manage-training')->with('success', 'Training video added');
    }

    public function edit(Video $video)
    {
        $this->ensureAdmin();
        return view('admin.training.edit', compact('video'));
    }

    public function update(Request $request, Video $video)
    {
        $this->ensureAdmin();
        $fileRule = 'nullable|file|mimes:mp4,webm,ogg|max:204800';
        $data = $request->validate([
            'title' => 'required|string|max:191',
            'description' => 'nullable|string',
            'url' => 'nullable|url',
            'file' => $fileRule,
            'length' => 'nullable|integer|min:1',
        ]);

        try {
            if ($request->hasFile('file')) {
                if ($video->file_path) { try { Storage::disk('public')->delete($video->file_path); } catch (\Throwable $e) {}}
                $file = $request->file('file');
                $filename = time().'_'.preg_replace('/[^A-Za-z0-9._-]/', '_', $file->getClientOriginalName());
                $dest = storage_path('app/public/videos');
                if (!is_dir($dest)) { mkdir($dest, 0755, true); }
                $file->move($dest, $filename);
                $data['file_path'] = 'videos/'.$filename;
                $data['url'] = null;
            }
            $data['is_training'] = true;
            $video->update($data);
        } catch (\Throwable $e) {
            Log::error('Training update failed', ['err' => $e->getMessage()]);
            return redirect('/admin/manage-training')->with('error', 'Failed to update training video.');
        }

        return redirect('/admin/manage-training')->with('success', 'Training video updated');
    }

    public function destroy(Video $video)
    {
        $this->ensureAdmin();
        if ($video->file_path) { try { Storage::disk('public')->delete($video->file_path); } catch (\Throwable $e) {}}
        $video->delete();
        return redirect('/admin/manage-training')->with('success', 'Training video deleted');
    }
}
