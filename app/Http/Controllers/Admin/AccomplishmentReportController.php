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
        return view('admin.accomplishment_reports.create');
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
                $path = $request->file('file')->store('accomplishment_reports', 'public');
            } catch (\Throwable $e) {
                // Fallback: store manually without relying on mime guessers
                $file = $request->file('file');
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
        }

        AccomplishmentReport::create($data);

        return redirect()->route('admin.accomplishment-reports.index')->with('success', 'Accomplishment Report created.');
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
            'file' => 'nullable|file|max:10240',
        ]);

        if($request->hasFile('file')){
            // delete old file if exists
            if($accomplishmentReport->file_path && Storage::disk('public')->exists($accomplishmentReport->file_path)){
                Storage::disk('public')->delete($accomplishmentReport->file_path);
            }
            try {
                $path = $request->file('file')->store('accomplishment_reports', 'public');
            } catch (\Throwable $e) {
                $file = $request->file('file');
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
        }

        $accomplishmentReport->update($data);

        return redirect()->route('admin.accomplishment-reports.index')->with('success', 'Accomplishment Report updated.');
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
