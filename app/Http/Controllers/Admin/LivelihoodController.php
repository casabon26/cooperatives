<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Slpa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LivelihoodController extends Controller
{
    public function index()
    {
        $slpas = Slpa::orderByDesc('created_at')->paginate(20);
        return view('admin.livelihood.index', compact('slpas'));
    }

    public function create()
    {
        return view('admin.livelihood.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            // use 'file' validator and handle mime/extension checks manually to avoid fileinfo dependency
            'image' => 'nullable|file|max:5120',
            'gallery' => 'nullable|array',
            'gallery.*' => 'nullable|file|max:5120',
            'members_count' => 'nullable|integer',
            'address' => 'nullable|string',
            // products sent as array of objects: products[index][name], products[index][description]
            'products' => 'nullable|array',
            'products.*.name' => 'nullable|string|max:255',
            'products.*.description' => 'nullable|string|max:2000',
            'business' => 'nullable|array',
            'business.*' => 'nullable|string|max:255',
        ]);

        // Handle single image upload (store to storage/app/public/slpas and copy a public fallback)
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time().'_'.preg_replace('/[^A-Za-z0-9._-]/', '_', $file->getClientOriginalName());
            $dest = storage_path('app/public/slpas');
            if (!is_dir($dest)) { @mkdir($dest, 0755, true); }
            $file->move($dest, $filename);
            $data['image'] = 'slpas/'.$filename;
            // copy to public/slpa_images for direct serving (fallback if storage symlink missing)
            $publicDir = public_path('slpa_images');
            if (!is_dir($publicDir)) { @mkdir($publicDir, 0755, true); }
            try { copy($dest.DIRECTORY_SEPARATOR.$filename, $publicDir.DIRECTORY_SEPARATOR.$filename); } catch (\Throwable $e) { /* non-fatal */ }
        }

        // Handle gallery uploads into storage/app/public/slpa_galleries
        if ($request->hasFile('gallery')) {
            $galleryFiles = [];
            $dest = storage_path('app/public/slpa_galleries');
            if (!is_dir($dest)) { @mkdir($dest, 0755, true); }
            $publicDir = public_path('slpa_galleries');
            if (!is_dir($publicDir)) { @mkdir($publicDir, 0755, true); }
            foreach ($request->file('gallery') as $file) {
                if (!$file->isValid()) continue;
                $ext = strtolower(pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION));
                if (!in_array($ext, ['jpg','jpeg','png','gif','webp'])) continue;
                $filename = time().'_'.preg_replace('/[^A-Za-z0-9._-]/', '_', $file->getClientOriginalName());
                $file->move($dest, $filename);
                $galleryFiles[] = 'slpa_galleries/'.$filename;
                try { copy($dest.DIRECTORY_SEPARATOR.$filename, $publicDir.DIRECTORY_SEPARATOR.$filename); } catch (\Throwable $e) { /* non-fatal */ }
            }
            if (!empty($galleryFiles)) $data['gallery'] = $galleryFiles;
        }

        // Normalize products: accept array of strings or array of arrays/objects,
        // store as array of objects with 'name' and optional 'description'.
        if (!empty($data['products']) && is_array($data['products'])) {
            $prods = [];
            foreach ($data['products'] as $p) {
                if (is_string($p)) {
                    $name = trim($p);
                    $desc = '';
                } elseif (is_array($p) || is_object($p)) {
                    $name = trim((string) (data_get($p, 'name') ?? ''));
                    $desc = trim((string) (data_get($p, 'description') ?? ''));
                } else {
                    continue;
                }
                if ($name !== '') {
                    $prods[] = ['name' => $name, 'description' => $desc];
                }
            }
            $data['products'] = $prods;
        }

        // Normalize business: accept array of strings or comma/newline separated string
        if (isset($data['business'])) {
            if (is_array($data['business'])) {
                $b = array_filter(array_map('trim', $data['business']), function($v){ return $v !== ''; });
                $data['business'] = !empty($b) ? implode(', ', $b) : null;
            } else {
                $data['business'] = trim((string)$data['business']);
            }
        }

        Slpa::create($data);
        return redirect()->route('admin.livelihood.index')->with('success', 'SLPA entry added');
    }

    public function edit(Slpa $livelihood)
    {
        return view('admin.livelihood.edit', ['livelihood' => $livelihood]);
    }

    public function update(Request $request, Slpa $livelihood)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            // use 'file' validator and handle mime/extension checks manually to avoid fileinfo dependency
            'image' => 'nullable|file|max:5120',
            'gallery' => 'nullable|array',
            'gallery.*' => 'nullable|file|max:5120',
            'members_count' => 'nullable|integer',
            'address' => 'nullable|string',
            'products' => 'nullable|array',
            'products.*.name' => 'nullable|string|max:255',
            'products.*.description' => 'nullable|string|max:2000',
            'business' => 'nullable|array',
            'business.*' => 'nullable|string|max:255',
        ]);

        // Handle single image upload (mirror cooperative approach: move into storage/app/public/slpas and copy public fallback)
        if ($request->hasFile('image')) {
            // remove old storage file if present
            if ($livelihood->image && Storage::disk('public')->exists($livelihood->image)) {
                Storage::disk('public')->delete($livelihood->image);
            }
            // remove old public fallback copy if present
            if ($livelihood->image) {
                $oldPublic = public_path(str_replace('slpas/', 'slpa_images/', $livelihood->image));
                if (file_exists($oldPublic)) { @unlink($oldPublic); }
            }
            $file = $request->file('image');
            $filename = time().'_'.preg_replace('/[^A-Za-z0-9._-]/', '_', $file->getClientOriginalName());
            $dest = storage_path('app/public/slpas');
            if (!is_dir($dest)) { @mkdir($dest, 0755, true); }
            $file->move($dest, $filename);
            $data['image'] = 'slpas/'.$filename;
            $publicDir = public_path('slpa_images');
            if (!is_dir($publicDir)) { @mkdir($publicDir, 0755, true); }
            try { copy($dest.DIRECTORY_SEPARATOR.$filename, $publicDir.DIRECTORY_SEPARATOR.$filename); } catch (\Throwable $e) { /* non-fatal */ }
        }

        // Handle gallery uploads into storage/app/public/slpa_galleries (whitelist extensions)
        if ($request->hasFile('gallery')) {
            $existing = $livelihood->gallery ?? [];
            $dest = storage_path('app/public/slpa_galleries');
            if (!is_dir($dest)) { @mkdir($dest, 0755, true); }
            $publicDir = public_path('slpa_galleries');
            if (!is_dir($publicDir)) { @mkdir($publicDir, 0755, true); }
            foreach ($request->file('gallery') as $file) {
                if (!$file->isValid()) continue;
                $ext = strtolower(pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION));
                if (!in_array($ext, ['jpg','jpeg','png','gif','webp'])) continue;
                $filename = time().'_'.preg_replace('/[^A-Za-z0-9._-]/', '_', $file->getClientOriginalName());
                $file->move($dest, $filename);
                $existing[] = 'slpa_galleries/'.$filename;
                try { copy($dest.DIRECTORY_SEPARATOR.$filename, $publicDir.DIRECTORY_SEPARATOR.$filename); } catch (\Throwable $e) { /* non-fatal */ }
            }
            if (!empty($existing)) $data['gallery'] = $existing;
        }

        // Normalize products: accept array of strings or array of arrays/objects,
        // store as array of objects with 'name' and optional 'description'.
        if (!empty($data['products']) && is_array($data['products'])) {
            $prods = [];
            foreach ($data['products'] as $p) {
                if (is_string($p)) {
                    $name = trim($p);
                    $desc = '';
                } elseif (is_array($p) || is_object($p)) {
                    $name = trim((string) (data_get($p, 'name') ?? ''));
                    $desc = trim((string) (data_get($p, 'description') ?? ''));
                } else {
                    continue;
                }
                if ($name !== '') {
                    $prods[] = ['name' => $name, 'description' => $desc];
                }
            }
            $data['products'] = $prods;
        }

        // Normalize business: accept array of strings or comma/newline separated string
        if (isset($data['business'])) {
            if (is_array($data['business'])) {
                $b = array_filter(array_map('trim', $data['business']), function($v){ return $v !== ''; });
                $data['business'] = !empty($b) ? implode(', ', $b) : null;
            } else {
                $data['business'] = trim((string)$data['business']);
            }
        }

        $livelihood->update($data);
        return redirect()->route('admin.livelihood.index')->with('success', 'SLPA entry updated');
    }

    public function destroy(Slpa $livelihood)
    {
        if ($livelihood->image && Storage::disk('public')->exists($livelihood->image)) {
            Storage::disk('public')->delete($livelihood->image);
        }
        // delete gallery images if any
        if (!empty($livelihood->gallery) && is_array($livelihood->gallery)) {
            foreach ($livelihood->gallery as $g) {
                if ($g && Storage::disk('public')->exists($g)) {
                    Storage::disk('public')->delete($g);
                }
            }
        }
        $livelihood->delete();
        return redirect()->route('admin.livelihood.index')->with('success', 'SLPA entry removed');
    }
}
