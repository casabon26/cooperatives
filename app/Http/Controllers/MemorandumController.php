<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Memorandum;

class MemorandumController extends Controller
{
    public function index(Request $request)
    {
        $memorandums = Memorandum::orderBy('published_at', 'desc')->paginate(10);
        return view('public.memorandums.index', compact('memorandums'));
    }

    public function show(Memorandum $memorandum)
    {
        return view('public.memorandums.show', ['memorandum' => $memorandum]);
    }

    /**
     * Serve the memorandum file inline when possible.
     */
    public function file(Memorandum $memorandum)
    {
        $path = $memorandum->file_path;
        if (empty($path)) {
            abort(404);
        }

        // External URL -> redirect
        if (preg_match('/^https?:\/\//', $path)) {
            return redirect()->away($path);
        }

        // storage/app/public/<path>
        $storagePath = storage_path('app/public/' . ltrim($path, '/'));
        if (file_exists($storagePath)) {
            return $this->streamFileResponse($storagePath, request()->query('dl'));
        }

        // public/<path>
        $publicPath = public_path(ltrim($path, '/'));
        if (file_exists($publicPath)) {
            return $this->streamFileResponse($publicPath, request()->query('dl'));
        }

        // public/storage/<path> (when files were moved to public/storage/memorandums)
        $publicStoragePath = public_path('storage/' . ltrim($path, '/'));
        if (file_exists($publicStoragePath)) {
            return $this->streamFileResponse($publicStoragePath, request()->query('dl'));
        }

        return abort(404);
    }

    /**
     * Stream a local file to the client with a safe fallback mime-type.
     *
     * @param string $filePath
     * @param mixed $forceDownload
     * @return \Illuminate\Http\Response
     */
    protected function streamFileResponse(string $filePath, $forceDownload = false)
    {
        $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        $mime = $this->mimeFromExtension($ext);

        $disposition = $forceDownload ? 'attachment' : 'inline';
        $filename = basename($filePath);

        $headers = [
            'Content-Type' => $mime,
            'Content-Length' => (string) filesize($filePath),
            'Content-Disposition' => $disposition . '; filename="' . $filename . '"',
            'Content-Transfer-Encoding' => 'binary',
        ];

        return response()->stream(function() use ($filePath) {
            $fp = fopen($filePath, 'rb');
            if ($fp) {
                while (!feof($fp)) {
                    echo fread($fp, 8192);
                    @ob_flush();
                    @flush();
                }
                fclose($fp);
            }
        }, 200, $headers);
    }

    /**
     * Minimal mime type map by extension to avoid relying on php_fileinfo.
     */
    protected function mimeFromExtension(string $ext): string
    {
        static $map = [
            'pdf' => 'application/pdf',
            'png' => 'image/png',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'gif' => 'image/gif',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'xls' => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'csv' => 'text/csv',
            'txt' => 'text/plain',
            'html' => 'text/html',
            'zip' => 'application/zip',
            'mp4' => 'video/mp4',
            'mov' => 'video/quicktime',
        ];

        return $map[$ext] ?? 'application/octet-stream';
    }
}
