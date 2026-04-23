<?php

namespace App\Http\Controllers;

use App\Http\Requests\DocumentUploadRequest;
use App\Models\Document;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function store(DocumentUploadRequest $request)
    {
        $path = $request->file('file')->store('documents','public');

        $doc = Document::create([
            'cooperative_id' => $request->cooperative_id,
            'file_path' => $path,
            'document_type' => $request->document_type,
            'visibility' => $request->visibility,
            'uploaded_by' => $request->user()->id,
        ]);

        // Audit log
        try {
            AuditLog::create([
                'user_id' => $request->user()->id,
                'action' => 'upload_document',
                'ip_address' => $request->ip(),
                'meta' => ['document_id' => $doc->id, 'cooperative_id' => $doc->cooperative_id],
            ]);
        } catch (\Throwable $e) {}

        return back()->with('success','Document uploaded');
    }

    public function download(Document $document)
    {
        // Enforce visibility: public allowed to all, private only to coop members and gov_admin
        if ($document->visibility === 'private') {
            $user = request()->user();
            if (!$user) abort(403);
            if ($user->role !== 'gov_admin') {
                // check membership
                if (!$user->cooperatives()->where('cooperative_id',$document->cooperative_id)->exists()) abort(403);
            }
        }

        try {
            AuditLog::create([
                'user_id' => optional(request()->user())->id,
                'action' => 'download_document',
                'ip_address' => request()->ip(),
                'meta' => ['document_id' => $document->id, 'cooperative_id' => $document->cooperative_id],
            ]);
        } catch (\Throwable $e) {}

        return Storage::disk('public')->download($document->file_path);
    }

    /**
     * Delete a single document (file + DB record).
     */
    public function destroy(Request $request, Document $document)
    {
        $user = $request->user();
        if (!$user) abort(403);

        // Only gov_admin or cooperative_admin members may delete
        if (!in_array($user->role, ['gov_admin','cooperative_admin'])) {
            abort(403);
        }
        if ($user->role === 'cooperative_admin') {
            if (!$user->cooperatives()->where('cooperative_id', $document->cooperative_id)->exists()) {
                abort(403);
            }
        }

        try {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($document->file_path);
        } catch (\Throwable $e) {}

        try {
            $document->delete();
        } catch (\Throwable $e) { }

        try {
            AuditLog::create([
                'user_id' => $user->id,
                'action' => 'delete_document',
                'ip_address' => $request->ip(),
                'meta' => ['document_id' => $document->id, 'cooperative_id' => $document->cooperative_id],
            ]);
        } catch (\Throwable $e) {}

        return back()->with('success','Document deleted');
    }
}
