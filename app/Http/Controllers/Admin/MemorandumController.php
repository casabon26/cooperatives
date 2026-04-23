<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Memorandum;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

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
            'title' => 'nullable|string|max:191',
            'published_year' => 'nullable|digits:4|integer|min:1900|max:' . (date('Y') + 5),
            'file' => 'nullable|file|max:10240',
        ]);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            // Prevent adding a duplicate memorandum (same uploaded file/name)
            if ($this->isDuplicateMemorandumFile($file)) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['file' => 'Memorandum already exists.'])
                    ->with('error', 'Memorandum already exists.');
            }

            $path = $this->storeMemorandumFile($file);
            $data['file_path'] = $path;
        }

        // Convert published_year to published_at timestamp (start of year)
        if (!empty($data['published_year'])) {
            try {
                $data['published_at'] = Carbon::createFromFormat('Y', $data['published_year'])->startOfYear();
            } catch (\Throwable $e) {
                $data['published_at'] = null;
            }
            unset($data['published_year']);
        }

        // Prevent creating a memorandum that duplicates an existing one by title (and year when provided)
        if (!empty($data['title'])) {
            $query = Memorandum::where('title', $data['title']);
            if (!empty($data['published_at'])) {
                $query->whereYear('published_at', Carbon::parse($data['published_at'])->year);
            }
            if ($query->exists()) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['title' => 'A memorandum with the same title (and year) already exists.'])
                    ->with('error', 'A memorandum with the same title (and year) already exists.');
            }
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
            'title' => 'nullable|string|max:191',
            'published_year' => 'nullable|digits:4|integer|min:1900|max:' . (date('Y') + 5),
            'file' => 'nullable|file|max:10240',
        ]);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            // Prevent updating to a file that duplicates another memorandum
            if ($this->isDuplicateMemorandumFile($file, $memorandum->id)) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['file' => 'Memorandum already exists.'])
                    ->with('error', 'Memorandum already exists.');
            }

            $existing = $memorandum->file_path ?? null;
            $path = $this->storeMemorandumFile($file, $existing);

            // Delete old file only if it exists and is different from the new path
            if (!empty($existing) && $existing !== $path) {
                if (Storage::disk('public')->exists($existing)) {
                    Storage::disk('public')->delete($existing);
                } else {
                    $publicPath = public_path(ltrim($existing, '/'));
                    if (file_exists($publicPath)) {
                        @unlink($publicPath);
                    }
                }
            }

            $data['file_path'] = $path;
        }

        // Convert published_year to published_at timestamp (start of year)
        if (!empty($data['published_year'])) {
            try {
                $data['published_at'] = Carbon::createFromFormat('Y', $data['published_year'])->startOfYear();
            } catch (\Throwable $e) {
                $data['published_at'] = null;
            }
            unset($data['published_year']);
        }

        // Prevent updating to values that would duplicate another memorandum
        if (!empty($data['title'])) {
            $query = Memorandum::where('title', $data['title'])->where('id', '<>', $memorandum->id);
            if (!empty($data['published_at'])) {
                $query->whereYear('published_at', Carbon::parse($data['published_at'])->year);
            }
            if ($query->exists()) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['title' => 'A memorandum with the same title (and year) already exists.'])
                    ->with('error', 'A memorandum with the same title (and year) already exists.');
            }
        }

        $memorandum->update($data);

        return redirect()->route('admin.memorandums.index')->with('success', 'Memorandum updated.');
    }

    /**
     * Store an uploaded memorandum file ensuring filename uniqueness.
     * If $existingPath is provided and matches a candidate name, that name is allowed (for overwriting the same record).
     * Returns the stored relative path (e.g. "memorandums/filename.pdf").
     */
    private function storeMemorandumFile($file, $existingPath = null)
    {
        $disk = Storage::disk('public');
        $dir = 'memorandums';

        $orig = preg_replace('/[^A-Za-z0-9_\-\.]/', '_', $file->getClientOriginalName());
        $nameWithoutExt = pathinfo($orig, PATHINFO_FILENAME);
        $extension = pathinfo($orig, PATHINFO_EXTENSION);
        $baseCandidate = $nameWithoutExt . ($extension ? '.' . $extension : '');
        $candidate = $baseCandidate;

        // Inspect existing files in the memorandums dir to detect duplicates.
        $existingFiles = [];
        try {
            $existingFiles = $disk->files($dir) ?: [];
        } catch (\Throwable $e) {
            $existingFiles = [];
        }

        $maxNum = 0;
        $found = false;
        foreach ($existingFiles as $f) {
            // Normalize to relative path like "memorandums/filename.pdf"
            $rel = ltrim($f, '/');
            // Skip the file that belongs to the current record (allow overwrite on update)
            if ($existingPath !== null && (ltrim($existingPath, '/') === $rel || $existingPath === $rel)) {
                continue;
            }
            $basename = basename($f);
            $bnNoExt = pathinfo($basename, PATHINFO_FILENAME);

            // Match numeric suffix patterns: name-1, name-2, ...
            if (preg_match('/^' . preg_quote($nameWithoutExt, '/') . '-(\d+)$/i', $bnNoExt, $m)) {
                $found = true;
                $n = intval($m[1]);
                if ($n > $maxNum) $maxNum = $n;
                continue;
            }

            // If the existing basename contains the original name anywhere (handles timestamp prefixes and copy suffixes),
            // mark as found so we will append an incremental suffix. Use case-insensitive check.
            if (stripos($bnNoExt, $nameWithoutExt) !== false) {
                $found = true;
                continue;
            }
        }

        if ($found) {
            $candidate = $nameWithoutExt . '-' . ($maxNum + 1) . ($extension ? '.' . $extension : '');
        }

        Log::info('Memorandum upload: chosen candidate', ['orig' => $orig, 'candidate' => $candidate, 'existing_count' => count($existingFiles)]);

        try {
            $stored = $file->storeAs($dir, $candidate, 'public');
            Log::info('Memorandum upload: stored file', ['path' => $stored]);
            return $stored;
        } catch (\Throwable $e) {
            // Fallback: move the uploaded file into public/storage/memorandums and ensure uniqueness there as well
            $destination = public_path('storage/' . $dir);
            if (!file_exists($destination)) {
                @mkdir($destination, 0755, true);
            }

            $targetPath = $destination . DIRECTORY_SEPARATOR . $candidate;
            if (file_exists($targetPath) && ($existingPath === null || $existingPath !== $dir . '/' . $candidate)) {
                $j = $maxNum;
                do {
                    $j++;
                    $candidate = $nameWithoutExt . '-' . $j . ($extension ? '.' . $extension : '');
                    $targetPath = $destination . DIRECTORY_SEPARATOR . $candidate;
                } while (file_exists($targetPath));
            }

            $moved = $file->move($destination, $candidate);
            if ($moved) {
                Log::info('Memorandum upload: moved fallback', ['path' => $dir . '/' . $candidate]);
                return $dir . '/' . $candidate;
            }

            $fallbackName = time() . '_' . $candidate;
            $moved2 = $file->move($destination, $fallbackName);
            if ($moved2) {
                return $dir . '/' . $fallbackName;
            }

            throw $e;
        }
    }

    /**
     * Check whether an uploaded file would duplicate an existing memorandum.
     * Uses filename-based heuristics to detect duplicates (handles timestamped and "_copy" variants).
     * If $excludeId is provided, that memorandum record will be ignored (useful for updates).
     */
    private function isDuplicateMemorandumFile($file, $excludeId = null)
    {
        $orig = preg_replace('/[^A-Za-z0-9_\-\.]/', '_', $file->getClientOriginalName());
        $nameWithoutExt = pathinfo($orig, PATHINFO_FILENAME);

        // Quick DB check: any memorandum.file_path containing the base name?
        $query = Memorandum::query();
        if ($excludeId) {
            $query->where('id', '<>', $excludeId);
        }
        try {
            if ($query->where('file_path', 'like', '%' . $nameWithoutExt . '%')->exists()) {
                return true;
            }
        } catch (\Throwable $e) {
            // ignore DB errors and fall back to storage check
        }

        // Fallback: inspect storage files directly
        try {
            $disk = Storage::disk('public');
            $files = $disk->files('memorandums') ?: [];
            foreach ($files as $f) {
                $bnNoExt = pathinfo($f, PATHINFO_FILENAME);
                if (stripos($bnNoExt, $nameWithoutExt) !== false) {
                    // If excluding a record, skip its current file path
                    if ($excludeId) {
                        $excluded = Memorandum::find($excludeId);
                        if ($excluded && ltrim($excluded->file_path, '/') === ltrim($f, '/')) {
                            continue;
                        }
                    }
                    return true;
                }
            }
        } catch (\Throwable $e) {
            // ignore storage errors
        }

        return false;
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
