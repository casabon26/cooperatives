<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AccomplishmentReport;
use Illuminate\Support\Facades\Storage;

class AccomplishmentReportController extends Controller
{
    public function index()
    {
        $reports = AccomplishmentReport::orderBy('published_at','desc')->paginate(15);
        return view('admin.accomplishment_reports.index', compact('reports'));
    }

    public function create()
    {
        // compute next incremental code (numeric, zero-padded to 3 digits)
        $max = AccomplishmentReport::whereNotNull('code')
            ->get()
            ->pluck('code')
            ->map(function($c){
                if(is_null($c)) return 0;
                // extract digits and convert to int
                $num = preg_replace('/[^0-9]/', '', $c);
                return (int) ($num === '' ? 0 : $num);
            })->max() ?? 0;
        $next = $max + 1;
        $code = str_pad($next, 3, '0', STR_PAD_LEFT);

        return view('admin.accomplishment_reports.create', compact('code'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code' => 'nullable|string|max:191',
            'title' => 'nullable|string|max:191',
            'content' => 'nullable|string',
            'published_at' => 'nullable|date',
            // max is in kilobytes — 200MB = 200 * 1024 = 204800 KB
            'file' => 'nullable|file|max:204800',
        ]);
        $uploadWarning = false;
        if($request->hasFile('file')){
            $file = $request->file('file');
            $size = $file->getSize(); // bytes
            $maxBytes = 200 * 1024 * 1024; // 200MB
            if($size !== null && $size > $maxBytes){
                return redirect()->back()->with('error', 'File exceeds maximum allowed size of 200MB.')->withInput();
            }

            try {
                try {
                    $path = $file->store('accomplishment_reports', 'public');
                } catch (\Throwable $e) {
                    // Fallback: store manually without relying on mime guessers
                    $name = time().'_'.preg_replace('/[^A-Za-z0-9_\-\.]/', '_', $file->getClientOriginalName());
                    $destination = public_path('storage/accomplishment_reports');
                    if (!file_exists($destination)) {
                        @mkdir($destination, 0755, true);
                    }
                    $moved = $file->move($destination, $name);
                    if ($moved) {
                        $path = 'accomplishment_reports/'. $name;
                    } else {
                        throw $e; // rethrow if move also fails
                    }
                }
                $data['file_path'] = $path;
            } catch (\Throwable $e) {
                // If file is not over the 200MB limit, allow creating the report without the file
                if(isset($size) && $size <= $maxBytes){
                    $uploadWarning = true;
                } else {
                    return redirect()->back()->with('error', 'Failed to upload file. Please try again.')->withInput();
                }
            }
        }

        AccomplishmentReport::create($data);

        $msg = 'Accomplishment Report created.' . ($uploadWarning ? ' (File upload failed — record created without file.)' : '');
        return redirect()->route('admin.accomplishment-reports.index')->with('success', $msg);
    }

    public function edit(AccomplishmentReport $accomplishmentReport)
    {
        return view('admin.accomplishment_reports.edit', compact('accomplishmentReport'));
    }

    public function update(Request $request, AccomplishmentReport $accomplishmentReport)
    {
        $data = $request->validate([
            'code' => 'nullable|string|max:191',
            'title' => 'nullable|string|max:191',
            'content' => 'nullable|string',
            'published_at' => 'nullable|date',
            'file' => 'nullable|file|max:204800',
        ]);

        $uploadWarning = false;
        if($request->hasFile('file')){
            // delete old file if exists
            if($accomplishmentReport->file_path && Storage::disk('public')->exists($accomplishmentReport->file_path)){
                Storage::disk('public')->delete($accomplishmentReport->file_path);
            }

            $file = $request->file('file');
            $size = $file->getSize(); // bytes
            $maxBytes = 200 * 1024 * 1024; // 200MB
            if($size !== null && $size > $maxBytes){
                return redirect()->back()->with('error', 'File exceeds maximum allowed size of 200MB.')->withInput();
            }

            try {
                try {
                    $path = $file->store('accomplishment_reports', 'public');
                } catch (\Throwable $e) {
                    $name = time().'_'.preg_replace('/[^A-Za-z0-9_\-\.]/', '_', $file->getClientOriginalName());
                    $destination = public_path('storage/accomplishment_reports');
                    if (!file_exists($destination)) {
                        @mkdir($destination, 0755, true);
                    }
                    $moved = $file->move($destination, $name);
                    if ($moved) {
                        $path = 'accomplishment_reports/'. $name;
                    } else {
                        throw $e;
                    }
                }
                $data['file_path'] = $path;
            } catch (\Throwable $e) {
                if(isset($size) && $size <= $maxBytes){
                    $uploadWarning = true;
                } else {
                    return redirect()->back()->with('error', 'Failed to upload file. Please try again.')->withInput();
                }
            }
        }

        $accomplishmentReport->update($data);

        $msg = 'Accomplishment Report updated.' . ($uploadWarning ? ' (File upload failed — record updated without new file.)' : '');
        return redirect()->route('admin.accomplishment-reports.index')->with('success', $msg);
    }

    public function destroy(AccomplishmentReport $accomplishmentReport)
    {
        if($accomplishmentReport->file_path && Storage::disk('public')->exists($accomplishmentReport->file_path)){
            Storage::disk('public')->delete($accomplishmentReport->file_path);
        }
        $accomplishmentReport->delete();
        return redirect()->route('admin.accomplishment-reports.index')->with('success', 'Accomplishment Report deleted.');
    }
}
