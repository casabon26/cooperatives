<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CooperativeResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CooperativeResourceController extends Controller
{
    public function index()
    {
        $resources = CooperativeResource::orderByDesc('created_at')->paginate(20);
        return view('admin.cooperative_resources.index', compact('resources'));
    }

    public function create()
    {
        $resource = new CooperativeResource();
        return view('admin.cooperative_resources.create', compact('resource'));
    }

    public function store(Request $request)
    {
        // Ensure Symfony MimeTypes has a safe default to avoid php_fileinfo dependency exceptions
        if (!\Symfony\Component\Mime\MimeTypes::getDefault()->isGuesserSupported()) {
            $mt = new \Symfony\Component\Mime\MimeTypes();
            $mt->registerGuesser(new class implements \Symfony\Component\Mime\MimeTypeGuesserInterface {
                public function isGuesserSupported(): bool { return true; }
                public function guessMimeType(string $path): ?string { return null; }
            });
            \Symfony\Component\Mime\MimeTypes::setDefault($mt);
        }

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'file' => 'nullable|file|max:51200',
            'description' => 'nullable|string',
        ]);

        if ($request->hasFile('file')) {
            // Validate by extension and store using storeAs to avoid Symfony MimeTypes guessers
            $uploaded = $request->file('file');
            $ext = strtolower($uploaded->getClientOriginalExtension() ?? '');
            $allowed = ['pdf','ppt','pptx'];
            if (!in_array($ext, $allowed)) {
                return back()->withErrors(['file' => 'Only PDF and PPT files are allowed.'])->withInput();
            }
            $filename = time() . '_' . uniqid() . '.' . ($ext ?: 'bin');
            $path = $uploaded->storeAs('cooperative_resources', $filename, 'public');
            $data['file_path'] = $path;
        }

        CooperativeResource::create($data);
        return redirect()->route('admin.cooperative-resources.index')->with('success','Resource added');
    }

    public function edit(CooperativeResource $cooperativeResource)
    {
        $resource = $cooperativeResource;
        return view('admin.cooperative_resources.edit', compact('resource'));
    }

    public function update(Request $request, CooperativeResource $cooperativeResource)
    {
        // Ensure Symfony MimeTypes has a safe default to avoid php_fileinfo dependency exceptions
        if (!\Symfony\Component\Mime\MimeTypes::getDefault()->isGuesserSupported()) {
            $mt = new \Symfony\Component\Mime\MimeTypes();
            $mt->registerGuesser(new class implements \Symfony\Component\Mime\MimeTypeGuesserInterface {
                public function isGuesserSupported(): bool { return true; }
                public function guessMimeType(string $path): ?string { return null; }
            });
            \Symfony\Component\Mime\MimeTypes::setDefault($mt);
        }

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'file' => 'nullable|file|max:51200',
            'description' => 'nullable|string',
        ]);

        if ($request->hasFile('file')) {
            // delete old
            if ($cooperativeResource->file_path && Storage::disk('public')->exists($cooperativeResource->file_path)) {
                Storage::disk('public')->delete($cooperativeResource->file_path);
            }
            $uploaded = $request->file('file');
            $ext = strtolower($uploaded->getClientOriginalExtension() ?? '');
            $allowed = ['pdf','ppt','pptx'];
            if (!in_array($ext, $allowed)) {
                return back()->withErrors(['file' => 'Only PDF and PPT files are allowed.'])->withInput();
            }
            $filename = time() . '_' . uniqid() . '.' . ($ext ?: 'bin');
            $path = $uploaded->storeAs('cooperative_resources', $filename, 'public');
            $data['file_path'] = $path;
        }

        $cooperativeResource->update($data);
        return redirect()->route('admin.cooperative-resources.index')->with('success','Resource updated');
    }

    public function destroy(CooperativeResource $cooperativeResource)
    {
        if ($cooperativeResource->file_path && Storage::disk('public')->exists($cooperativeResource->file_path)) {
            Storage::disk('public')->delete($cooperativeResource->file_path);
        }
        $cooperativeResource->delete();
        return redirect()->route('admin.cooperative-resources.index')->with('success','Resource removed');
    }
}
