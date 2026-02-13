<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Enterprise;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class EnterpriseController extends Controller
{
    public function index()
    {
        $enterprises = Enterprise::orderBy('created_at','desc')->paginate(15);
        return view('admin.enterprises.index', compact('enterprises'));
    }

    public function create()
    {
        return view('admin.enterprises.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|in:Micro,Small,Medium',
            'summary' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            // Use `file` instead of `image` to avoid requiring the fileinfo extension for MIME guessing
            'image' => 'nullable|file|max:5120',
        ]);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time().'_'.preg_replace('/[^A-Za-z0-9._-]/', '_', $file->getClientOriginalName());
            $dest = storage_path('app/public/enterprises');
            if (!is_dir($dest)) { mkdir($dest, 0755, true); }
            $file->move($dest, $filename);
            $data['image'] = 'enterprises/'.$filename;
            // copy to public/enterprises for direct serving if needed
            $publicDir = public_path('enterprises');
            if (!is_dir($publicDir)) { @mkdir($publicDir, 0755, true); }
            try { copy($dest.DIRECTORY_SEPARATOR.$filename, $publicDir.DIRECTORY_SEPARATOR.$filename); } catch (\Throwable $e) { /* non-fatal */ }

            // store base64/mime metadata similar to news controller to avoid relying on finfo
            try {
                $contents = file_get_contents($dest.DIRECTORY_SEPARATOR.$filename);
                if ($contents !== false) {
                    $data['image_data'] = base64_encode($contents);
                    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                    $map = ['jpg'=>'image/jpeg','jpeg'=>'image/jpeg','png'=>'image/png','gif'=>'image/gif','webp'=>'image/webp','svg'=>'image/svg+xml','bmp'=>'image/bmp'];
                    $data['image_mime'] = $map[$ext] ?? 'image/jpeg';
                    $data['image_filename'] = $file->getClientOriginalName();
                }
            } catch (\Throwable $e) { /* non-fatal */ }
        }

        Enterprise::create($data);
        return redirect()->route('admin.enterprises.index')->with('success', 'Enterprise created.');
    }

    public function edit($id)
    {
        $enterprise = Enterprise::findOrFail($id);
        return view('admin.enterprises.edit', compact('enterprise'));
    }

    public function update(Request $request, $id)
    {
        $enterprise = Enterprise::findOrFail($id);
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|in:Micro,Small,Medium',
            'summary' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|file|max:5120',
        ]);

        if ($request->hasFile('image')) {
            // delete old storage file and public copy if present
            if ($enterprise->image) {
                try {
                    $oldBase = basename($enterprise->image);
                    $oldStorage = storage_path('app/public/'. $oldBase);
                    if (file_exists($oldStorage)) { @unlink($oldStorage); }
                } catch (\Throwable $e) { Log::warning('Could not unlink old enterprise storage file', ['id'=>$enterprise->id, 'error'=>$e->getMessage()]); }
                try {
                    $oldPublic = public_path('enterprises/'. basename($enterprise->image));
                    if (file_exists($oldPublic)) { @unlink($oldPublic); }
                } catch (\Throwable $e) { /* ignore */ }
            }

            $file = $request->file('image');
            $filename = time().'_'.preg_replace('/[^A-Za-z0-9._-]/', '_', $file->getClientOriginalName());
            $dest = storage_path('app/public/enterprises');
            if (!is_dir($dest)) { mkdir($dest, 0755, true); }
            $file->move($dest, $filename);
            $data['image'] = 'enterprises/'.$filename;

            $publicDir = public_path('enterprises');
            if (!is_dir($publicDir)) { @mkdir($publicDir, 0755, true); }
            try { copy($dest.DIRECTORY_SEPARATOR.$filename, $publicDir.DIRECTORY_SEPARATOR.$filename); } catch (\Throwable $e) { /* non-fatal */ }

            try {
                $contents = file_get_contents($dest.DIRECTORY_SEPARATOR.$filename);
                if ($contents !== false) {
                    $data['image_data'] = base64_encode($contents);
                    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                    $map = ['jpg'=>'image/jpeg','jpeg'=>'image/jpeg','png'=>'image/png','gif'=>'image/gif','webp'=>'image/webp','svg'=>'image/svg+xml','bmp'=>'image/bmp'];
                    $data['image_mime'] = $map[$ext] ?? 'image/jpeg';
                    $data['image_filename'] = $file->getClientOriginalName();
                }
            } catch (\Throwable $e) { /* non-fatal */ }
        }

        $enterprise->update($data);
        return redirect()->route('admin.enterprises.index')->with('success', 'Enterprise updated.');
    }

    public function destroy($id)
    {
        $enterprise = Enterprise::findOrFail($id);
        if ($enterprise->image) {
            try {
                $base = basename($enterprise->image);
                $storageFile = storage_path('app/public/'. $base);
                if (file_exists($storageFile)) { @unlink($storageFile); }
            } catch (\Throwable $e) { Log::warning('Failed to unlink enterprise storage file', ['id'=>$enterprise->id, 'error'=>$e->getMessage()]); }
            try {
                $publicCopy = public_path('enterprises/'. basename($enterprise->image));
                if (file_exists($publicCopy)) { @unlink($publicCopy); }
            } catch (\Throwable $e) { /* ignore */ }
        }
        $enterprise->delete();
        return redirect()->route('admin.enterprises.index')->with('success', 'Enterprise deleted.');
    }

    /**
     * Return a JSON list of enterprises for the given category.
     * This is a simple placeholder endpoint; replace with real DB queries later.
     */
    public function list(\Illuminate\Http\Request $request)
    {
        $cat = $request->get('category', 'Micro');
        $cat = ucfirst(strtolower(trim($cat)));
        $allowed = ['Micro','Small','Medium'];
        if(!in_array($cat, $allowed)){
            return response()->json(['data' => []]);
        }

        // Return enterprises from DB for the requested category
        $enterprises = \App\Models\Enterprise::where('category', $cat)
            ->orderBy('created_at', 'desc')
            ->get(['id','name','summary','image']);

        // include public URL for each enterprise
        $enterprises = $enterprises->map(function($e){
            $imageUrl = null;
            if (!empty($e->image)) {
                $img = str_replace('\\', '/', $e->image);
                // Check for a public copy (public/enterprises/...)
                if (file_exists(public_path($img))) {
                    $imageUrl = asset($img);
                } elseif (file_exists(public_path('storage/'.$img))) {
                    $imageUrl = asset('storage/'.$img);
                } elseif (preg_match('/^https?:\/\//', $img)) {
                    $imageUrl = $img;
                }
            }
            return [
                'id' => $e->id,
                'name' => $e->name,
                'summary' => $e->summary,
                'image' => $e->image,
                'image_url' => $imageUrl,
                'url' => url('/enterprises/'.$e->id),
            ];
        });

        return response()->json(['data' => $enterprises]);
    }
}
