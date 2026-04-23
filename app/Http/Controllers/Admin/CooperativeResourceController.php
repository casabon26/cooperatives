<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CooperativeResource;
use App\Models\Cooperative;
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
            'gdrive_link' => 'nullable|url',
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
            'gdrive_link' => 'nullable|url',
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
        $coopId = $cooperativeResource->cooperative_id ?? null;
        if ($coopId) {
            return redirect()->route('cooperatives.profile', $coopId)->with('success','Resource updated');
        }
        return redirect()->route('admin.cooperative-resources.index')->with('success','Resource updated');
    }

    /**
     * Store a new resource attached to a specific cooperative.
     */
    public function storeForCooperative(Request $request, Cooperative $cooperative)
    {
        $this->authorize('manage', $cooperative);

        // reuse same validation and upload logic as store()
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
            'gdrive_link' => 'nullable|url',
        ]);

        if ($request->hasFile('file')) {
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

        $data['cooperative_id'] = $cooperative->id;

        CooperativeResource::create($data);

        return redirect()->route('cooperatives.profile', $cooperative)->with('success','Resource added');
    }

    /**
     * Show edit form for a resource scoped to a cooperative.
     */
    public function editForCooperative(Cooperative $cooperative, CooperativeResource $resource)
    {
        $this->authorize('manage', $cooperative);
        if ($resource->cooperative_id !== $cooperative->id) abort(404);
        return view('admin.cooperative_resources.edit', compact('resource','cooperative'));
    }

    /**
     * Update a cooperative-scoped resource.
     */
    public function updateForCooperative(Request $request, Cooperative $cooperative, CooperativeResource $resource)
    {
        $this->authorize('manage', $cooperative);
        if ($resource->cooperative_id !== $cooperative->id) abort(404);

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
            'gdrive_link' => 'nullable|url',
        ]);

        if ($request->hasFile('file')) {
            if ($resource->file_path && Storage::disk('public')->exists($resource->file_path)) {
                Storage::disk('public')->delete($resource->file_path);
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

        $resource->update($data);
        return redirect()->route('cooperatives.profile', $cooperative)->with('success','Resource updated');
    }

    /**
     * Destroy a cooperative-scoped resource.
     */
    public function destroyForCooperative(Cooperative $cooperative, CooperativeResource $resource)
    {
        $this->authorize('manage', $cooperative);
        if ($resource->cooperative_id !== $cooperative->id) abort(404);
        if ($resource->file_path && Storage::disk('public')->exists($resource->file_path)) {
            Storage::disk('public')->delete($resource->file_path);
        }
        $resource->delete();
        return redirect()->route('cooperatives.profile', $cooperative)->with('success','Resource removed');
    }

    public function destroy(CooperativeResource $cooperativeResource)
    {
        if ($cooperativeResource->file_path && Storage::disk('public')->exists($cooperativeResource->file_path)) {
            Storage::disk('public')->delete($cooperativeResource->file_path);
        }
        $cooperativeResource->delete();
        $coopId = $cooperativeResource->cooperative_id ?? null;
        if ($coopId) {
            return redirect()->route('cooperatives.profile', $coopId)->with('success','Resource removed');
        }
        return redirect()->route('admin.cooperative-resources.index')->with('success','Resource removed');
    }
}
