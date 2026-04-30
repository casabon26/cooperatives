<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Gallery;
use Illuminate\Support\Facades\Storage;

class GalleryController extends Controller
{
    public function index()
    {
        $galleries = Gallery::orderByDesc('created_at')->paginate(20);
        return view('admin.galleries.index', compact('galleries'));
    }

    public function create()
    {
        return view('admin.galleries.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            // use file validator to avoid relying on PHP fileinfo extension
            // max is in kilobytes: 50 MB = 50 * 1024 = 51200 KB
            'image' => 'required|file|max:51200',
            // title required for livelihood uploads (server-side enforced via required_if below)
            'title' => 'nullable|string|max:191',
            'description' => 'nullable|string',
            'alt_text' => 'nullable|string|max:191',
            'section' => 'nullable|string|max:60',
        ], [
            'image.max' => 'Maximum allowed image size is 50 MB.',
        ]);

        // Enforce title requirement when section is livelihood
        $section = $request->input('section','livelihood');
        if ($section === 'livelihood' && !trim((string)$request->input('title','')) ) {
            return redirect()->back()->withInput()->withErrors(['title' => 'Title is required for livelihood gallery items.']);
        }

        // Permissive image detection (does not rely on PHP fileinfo)
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $ext = strtolower(pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION));
            $permissiveExts = ['jpg','jpeg','png','gif','webp','bmp','tif','tiff','svg','svgz','ico','heic','avif','psd','raw','cr2','nef','orf','arw','jfif','jp2','jpx'];
            $isImage = false;

            // 1) Try getimagesize() for raster images (works without mime guessers)
            try {
                $realPath = $file->getRealPath();
                if ($realPath && @getimagesize($realPath) !== false) {
                    $isImage = true;
                }
            } catch (\Throwable $e) {
                // ignore and continue to other checks
            }

            // 2) Allow common image extensions (covers vector/SVG and uncommon formats)
            if (!$isImage && in_array($ext, $permissiveExts)) {
                $isImage = true;
            }

            // 3) Quick SVG content sniff (in case getimagesize isn't available)
            if (!$isImage && in_array($ext, ['svg','svgz'])) {
                $contents = @file_get_contents($file->getRealPath(), false, null, 0, 512);
                if ($contents !== false && stripos($contents, '<svg') !== false) {
                    $isImage = true;
                }
            }

            if (!$isImage) {
                return redirect()->back()->withInput()->withErrors(['image' => 'Uploaded file does not appear to be an image.']);
            }
        }

        $uploadWarning = false;
        $path = null;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            try {
                try {
                    $path = $file->store('cooperative_galleries', 'public');
                } catch (\Throwable $e) {
                    // Fallback if mime guessers not available (php_fileinfo missing)
                    $name = time().'_'.preg_replace('/[^A-Za-z0-9_\-\.]/', '_', $file->getClientOriginalName());
                    $destination = public_path('storage/cooperative_galleries');
                    if (!file_exists($destination)) {
                        @mkdir($destination, 0755, true);
                    }
                    $moved = $file->move($destination, $name);
                    if ($moved) {
                        $path = 'cooperative_galleries/'. $name;
                    } else {
                        throw $e;
                    }
                }
            } catch (\Throwable $e) {
                $uploadWarning = true;
            }
        }

        $gallery = Gallery::create([
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'path' => $path,
            'alt_text' => $request->input('alt_text'),
            'published' => true,
            'section' => $request->input('section','livelihood'),
        ]);

        $msg = 'Image added to gallery.' . ($uploadWarning ? ' (Upload fallback used; file stored directly.)' : '');
        return redirect()->back()->with('success', $msg);
    }

    public function destroy(Gallery $gallery)
    {
        if ($gallery->path && Storage::disk('public')->exists($gallery->path)) {
            Storage::disk('public')->delete($gallery->path);
        }
        $gallery->delete();
        return redirect()->back()->with('success','Image removed');
    }
}
