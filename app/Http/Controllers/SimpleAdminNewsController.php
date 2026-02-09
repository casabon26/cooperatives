<?php
namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class SimpleAdminNewsController extends Controller
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
        $news = News::orderByDesc('published_at')->paginate(20);
        return view('admin.news.index', compact('news'));
    }

    public function store(Request $request)
    {
        $this->ensureAdmin();
        $data = $request->validate([
            'title' => 'required|string|max:191',
            'content' => 'required|string',
            'published_at' => 'nullable|date',
            // Allow admin to pick a numeric card slot; keep reasonable bounds
            'card_slot' => 'nullable|integer|min:1|max:20',
            // Use `file` instead of `image` to avoid requiring the fileinfo extension for MIME guessing
            'image' => 'nullable|file|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time().'_'.preg_replace('/[^A-Za-z0-9._-]/', '_', $file->getClientOriginalName());
            $dest = storage_path('app/public/news');
            if (!is_dir($dest)) { mkdir($dest, 0755, true); }
            $file->move($dest, $filename);
            $data['image'] = 'news/'.$filename;
            // Also copy to public/news so images are served even if storage symlink is missing
            // Use `news_images` directory to avoid colliding with the /news route
            $publicDir = public_path('news_images');
            if (!is_dir($publicDir)) { @mkdir($publicDir, 0755, true); }
            try { copy($dest.DIRECTORY_SEPARATOR.$filename, $publicDir.DIRECTORY_SEPARATOR.$filename); } catch (\Throwable $e) { /* non-fatal */ }

            // Save binary/base64 into DB fields for direct DB serving
            try {
                $contents = file_get_contents($dest.DIRECTORY_SEPARATOR.$filename);
                if ($contents !== false) {
                    $data['image_data'] = base64_encode($contents);
                    // Avoid relying on PHP finfo (may be missing). Infer from extension with safe fallback.
                    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                    $map = ['jpg'=>'image/jpeg','jpeg'=>'image/jpeg','png'=>'image/png','gif'=>'image/gif','webp'=>'image/webp','svg'=>'image/svg+xml','bmp'=>'image/bmp'];
                    $data['image_mime'] = $map[$ext] ?? 'image/jpeg';
                    $data['image_filename'] = $file->getClientOriginalName();
                }
            } catch (\Throwable $e) {
                // non-fatal — keep file storage fallback
            }
        }

        // If no published_at provided, default to current date and time
        if (empty($data['published_at'])) {
            $data['published_at'] = now();
        }

        // If this article is assigned a homepage card slot, handle possible existing assignment.
        if (!empty($data['card_slot'])) {
            $existing = News::where('card_slot', $data['card_slot'])->first();
            if ($existing) {
                // If the request included a confirmation, clear the existing assignment and proceed.
                if ($request->boolean('confirm_overwrite')) {
                    News::where('card_slot', $data['card_slot'])->update(['card_slot' => null]);
                } else {
                    return redirect()->back()->withInput()->with('error', 'Card '.intval($data['card_slot']).' is already assigned to "'.($existing->title ?? 'Untitled').'". Confirm overwrite to proceed.');
                }
            }
        }

        $data['created_by'] = session('admin_email') ? null : null;
        $n = News::create($data);
        return redirect('/admin/manage-news')->with('success', 'Article created');
    }

    public function edit(News $news)
    {
        $this->ensureAdmin();
        return view('admin.news.edit', compact('news'));
    }

    public function update(Request $request, News $news)
    {
        $this->ensureAdmin();
        $data = $request->validate([
            'title' => 'required|string|max:191',
            'content' => 'required|string',
            'published_at' => 'nullable|date',
            'card_slot' => 'nullable|integer|min:1|max:20',
            'image' => 'nullable|file|max:2048',
        ]);

        if ($request->hasFile('image')) {
            // delete old
            if ($news->image) { Storage::disk('public')->delete($news->image); }
            $file = $request->file('image');
            $filename = time().'_'.preg_replace('/[^A-Za-z0-9._-]/', '_', $file->getClientOriginalName());
            $dest = storage_path('app/public/news');
            if (!is_dir($dest)) { mkdir($dest, 0755, true); }
            $file->move($dest, $filename);
            $data['image'] = 'news/'.$filename;
            // remove any public copy of the old file and copy new file to public/news
            try {
                $oldBase = basename($news->image ?? '');
                if ($oldBase) {
                    $oldPublic = public_path('news/'. $oldBase);
                    if (file_exists($oldPublic)) { @unlink($oldPublic); }
                }
            } catch (\Throwable $e) { /* ignore */ }
            // Use `news_images` directory to avoid colliding with the /news route
            $publicDir = public_path('news_images');
            if (!is_dir($publicDir)) { @mkdir($publicDir, 0755, true); }
            try { copy($dest.DIRECTORY_SEPARATOR.$filename, $publicDir.DIRECTORY_SEPARATOR.$filename); } catch (\Throwable $e) { /* non-fatal */ }

            // Save binary/base64 into DB fields for direct DB serving (overwrite old DB copy)
            try {
                $contents = file_get_contents($dest.DIRECTORY_SEPARATOR.$filename);
                if ($contents !== false) {
                    $data['image_data'] = base64_encode($contents);
                    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                    $map = ['jpg'=>'image/jpeg','jpeg'=>'image/jpeg','png'=>'image/png','gif'=>'image/gif','webp'=>'image/webp','svg'=>'image/svg+xml','bmp'=>'image/bmp'];
                    $data['image_mime'] = $map[$ext] ?? 'image/jpeg';
                    $data['image_filename'] = $file->getClientOriginalName();
                }
            } catch (\Throwable $e) {
                // non-fatal
            }
        }

        // If assigning a card_slot on update, handle existing assignment by other article.
        if (!empty($data['card_slot'])) {
            $existing = News::where('card_slot', $data['card_slot'])->where('id', '<>', $news->id)->first();
            if ($existing) {
                if ($request->boolean('confirm_overwrite')) {
                    News::where('card_slot', $data['card_slot'])->where('id', '<>', $news->id)->update(['card_slot' => null]);
                } else {
                    return redirect()->back()->withInput()->with('error', 'Card '.intval($data['card_slot']).' is already assigned to "'.($existing->title ?? 'Untitled').'". Confirm overwrite to proceed.');
                }
            }
        }

        $news->update($data);
        return redirect('/admin/manage-news')->with('success', 'Article updated');
    }

    public function destroy(News $news)
    {
        $this->ensureAdmin();
        // Try to remove associated file, but do not abort DB deletion on failure.
        if ($news->image) {
            try {
                $path = storage_path('app/public/'.ltrim($news->image, '/'));
                if (file_exists($path)) {
                    if (!@unlink($path)) {
                        Log::warning('Could not unlink news image file (permissions?)', ['news_id' => $news->id, 'path' => $path]);
                    }
                } else {
                    // Only attempt Storage facade delete when finfo is available
                    if (class_exists('finfo')) {
                        try {
                            if (Storage::disk('public')->exists($news->image)) {
                                Storage::disk('public')->delete($news->image);
                            }
                        } catch (\Throwable $e) {
                            Log::warning('Storage disk delete failed for news image', ['news_id' => $news->id, 'error' => $e->getMessage()]);
                        }
                    } else {
                        Log::info('Skipping Storage delete because finfo is not available', ['news_id' => $news->id, 'image' => $news->image]);
                    }
                }
                // also attempt to delete any public copy
                try {
                    $oldBase = basename($news->image);
                    $publicCopy = public_path('news_images/'. $oldBase);
                    if (file_exists($publicCopy)) { @unlink($publicCopy); }
                } catch (\Throwable $e) { /* ignore */ }
                // also clear DB image fields
                try { $news->update(['image_data' => null, 'image_mime' => null, 'image_filename' => null]); } catch (\Throwable $e) { }
            } catch (\Throwable $e) {
                Log::warning('Failed to delete news image file', ['news_id' => $news->id, 'error' => $e->getMessage()]);
            }
        }

        try {
            $news->delete();
            return redirect('/admin/manage-news')->with('success', 'Article deleted');
        } catch (\Throwable $e) {
            Log::error('Failed to delete news record', ['news_id' => $news->id, 'error' => $e->getMessage()]);
            return redirect('/admin/manage-news')->with('error', 'Could not delete article.');
        }
    }
}
