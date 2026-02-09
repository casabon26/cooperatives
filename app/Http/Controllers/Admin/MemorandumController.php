<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Memorandum;
use Illuminate\Support\Facades\Storage;

class MemorandumController extends Controller
{
    public function index()
    {
        $memorandums = Memorandum::orderBy('published_at','desc')->paginate(15);
        return view('admin.memorandums.index', compact('memorandums'));
    }

    public function create()
    {
        return view('admin.memorandums.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code' => 'nullable|string|max:191',
            'title' => 'nullable|string|max:191',
            'content' => 'nullable|string',
            'published_at' => 'nullable|date',
            'file' => 'nullable|file|max:10240',
        ]);

        if($request->hasFile('file')){
            try {
                $path = $request->file('file')->store('memorandums', 'public');
            } catch (\Throwable $e) {
                // Fallback: store manually without relying on mime guessers
                $file = $request->file('file');
                $name = time().'_'.preg_replace('/[^A-Za-z0-9_\-\.]/', '_', $file->getClientOriginalName());
                $destination = public_path('storage/memorandums');
                if (!file_exists($destination)) {
                    @mkdir($destination, 0755, true);
                }
                $moved = $file->move($destination, $name);
                if ($moved) {
                    $path = 'memorandums/'. $name;
                } else {
                    throw $e; // rethrow if move also fails
                }
            }
            $data['file_path'] = $path;
        }

        Memorandum::create($data);

        return redirect()->route('admin.memorandums.index')->with('success', 'Memorandum created.');
    }

    public function edit(Memorandum $memorandum)
    {
        return view('admin.memorandums.edit', compact('memorandum'));
    }

    public function update(Request $request, Memorandum $memorandum)
    {
        $data = $request->validate([
            'code' => 'nullable|string|max:191',
            'title' => 'nullable|string|max:191',
            'content' => 'nullable|string',
            'published_at' => 'nullable|date',
            'file' => 'nullable|file|max:10240',
        ]);

        if($request->hasFile('file')){
            // delete old file if exists
            if($memorandum->file_path && Storage::disk('public')->exists($memorandum->file_path)){
                Storage::disk('public')->delete($memorandum->file_path);
            }
            try {
                $path = $request->file('file')->store('memorandums', 'public');
            } catch (\Throwable $e) {
                $file = $request->file('file');
                $name = time().'_'.preg_replace('/[^A-Za-z0-9_\-\.]/', '_', $file->getClientOriginalName());
                $destination = public_path('storage/memorandums');
                if (!file_exists($destination)) {
                    @mkdir($destination, 0755, true);
                }
                $moved = $file->move($destination, $name);
                if ($moved) {
                    $path = 'memorandums/'. $name;
                } else {
                    throw $e;
                }
            }
            $data['file_path'] = $path;
        }

        $memorandum->update($data);

        return redirect()->route('admin.memorandums.index')->with('success', 'Memorandum updated.');
    }

    public function destroy(Memorandum $memorandum)
    {
        if($memorandum->file_path && Storage::disk('public')->exists($memorandum->file_path)){
            Storage::disk('public')->delete($memorandum->file_path);
        }
        $memorandum->delete();
        return redirect()->route('admin.memorandums.index')->with('success', 'Memorandum deleted.');
    }
}
