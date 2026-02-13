<?php

namespace App\Http\Controllers;

use App\Models\Cooperative;
use App\Models\CooperativeResource;
use App\Models\News;
use App\Models\Video;
use App\Models\Memorandum;
use App\Models\AccomplishmentReport;
use Illuminate\Http\Request;

class PublicController extends Controller
{
    public function home(Request $request)
    {
        // Exclude articles assigned to homepage cards from the general news list
        $news = News::whereNotNull('published_at')->whereNull('card_slot')->orderByDesc('published_at')->limit(5)->get();
        $cardNews = News::whereNotNull('published_at')->whereNotNull('card_slot')->get()->keyBy('card_slot');
        // Load a sample set of cooperatives for the landing page
        $cooperatives = Cooperative::orderBy('name')->limit(34)->get();
        // Load latest videos for landing page. Prefer videos explicitly flagged for landing highlights;
        // fall back to the latest videos if no flags are set.
        $landingQuery = Video::where('highlight_landing', true)->orderByDesc('created_at');
        if ($landingQuery->count() > 0) {
            $videos = $landingQuery->limit(6)->get();
        } else {
            // Exclude videos that are marked as enterprise-only highlights so enterprise-only
            // videos do not appear on the landing page.
            $videos = Video::where(function($q){
                        $q->whereNull('highlight_enterprise')->orWhere('highlight_enterprise', false);
                    })->orderByDesc('created_at')->limit(6)->get();
        }
        $hasKey = !empty(env('YOUTUBE_API_KEY'));
        foreach ($videos as $v) {
            $v->youtube_id = $v->youtubeId();
            $v->embed_allowed = null;
            if ($v->youtube_id) {
                if ($hasKey) {
                    $emb = $v->checkYouTubeEmbeddable();
                    $v->embed_allowed = ($emb === true);
                } else {
                    // No API key: optimistic embedding — we'll attempt to embed. If the owner disabled embedding,
                    // the iframe may still show "Video unavailable"; admin should enable embedding or upload file.
                    $v->embed_allowed = null; // null == unknown/optimistic
                }
            }
        }
        // Build years list + counts for filter
        $yearCounts = Memorandum::whereNotNull('published_at')
                    ->selectRaw('YEAR(published_at) as year, count(*) as cnt')
                    ->groupBy('year')
                    ->orderByDesc('year')
                    ->pluck('cnt','year')
                    ->toArray();

        $years = array_keys($yearCounts);

        $memoQuery = Memorandum::query();
        if ($request->filled('memo_year')) {
            $memoQuery->whereYear('published_at', $request->input('memo_year'));
        }
        $memorandums = $memoQuery->orderByDesc('published_at')->limit(10)->get();

        $totalCount = Memorandum::count();
        $selectedCount = $request->filled('memo_year') ? ($yearCounts[$request->input('memo_year')] ?? 0) : $totalCount;

        // Build accomplishment reports data
        $accomplishmentReports = AccomplishmentReport::whereNotNull('published_at')->orderByDesc('published_at')->limit(10)->get();

        return view('public.home', compact('news','cardNews','cooperatives','videos','memorandums','years','yearCounts','totalCount','selectedCount','accomplishmentReports'));
    }

    public function directory(Request $request)
    {
        $query = Cooperative::query()->where('status','active');
        if ($request->filled('q')) {
            $query->where('name','like','%'.$request->q.'%');
        }
        if ($request->filled('region')) {
            $query->where('region',$request->region);
        }
        $perPage = intval($request->input('per_page', 12));
        if ($perPage <= 0 || $perPage > 200) {
            $perPage = 12;
        }
        $cooperatives = $query->orderBy('name')->paginate($perPage)->withQueryString();
        // Prepare cooperative resources sidebar (years filter + recent resources)
        $resourceYearCounts = CooperativeResource::selectRaw('YEAR(created_at) as year, count(*) as cnt')
                    ->groupBy('year')
                    ->orderByDesc('year')
                    ->pluck('cnt','year')
                    ->toArray();

        $resourceYears = array_keys($resourceYearCounts);

        $resQuery = CooperativeResource::query();
        if ($request->filled('resource_year')) {
            $resQuery->whereYear('created_at', $request->input('resource_year'));
        }
        $coopResources = $resQuery->orderByDesc('created_at')->limit(10)->get();

        $resourceTotalCount = CooperativeResource::count();
        $resourceSelectedCount = $request->filled('resource_year') ? ($resourceYearCounts[$request->input('resource_year')] ?? 0) : $resourceTotalCount;

        return view('public.directory', compact('cooperatives','coopResources','resourceYears','resourceYearCounts','resourceTotalCount','resourceSelectedCount'));
    }

    /**
     * AJAX search endpoint returning JSON list of cooperatives matching q and region.
     */
    public function search(Request $request)
    {
        $query = Cooperative::query()->where('status','active');
        if ($request->filled('q')) {
            $query->where('name','like','%'.$request->q.'%');
        }
        if ($request->filled('region')) {
            $query->where('region',$request->region);
        }
        $perPage = intval($request->input('per_page', 100));
        $perPage = $perPage > 0 ? min($perPage, 500) : 100;

        $cooperatives = $query->orderBy('name')->limit($perPage)->get(['id','name','sector','region','description','link']);

        return response()->json(['data' => $cooperatives]);
    }

    public function profile(Cooperative $cooperative)
    {
        $cooperative->load('profile','documents');
        return view('public.profile', compact('cooperative'));
    }

    /**
     * Return a small HTML fragment for cooperative details used in an AJAX modal.
     */
    public function profileModal(Cooperative $cooperative)
    {
        $cooperative->load('profile','documents');
        return view('public.partials.cooperative_modal', compact('cooperative'));
    }

    public function news()
    {
        $news = News::whereNotNull('published_at')->orderByDesc('published_at')->paginate(12);
        return view('public.news.index', compact('news'));
    }

    public function newsShow(News $news)
    {
        return view('public.news.show', compact('news'));
    }

    public function videos()
    {
        $videos = Video::orderByDesc('created_at')->paginate(12);
        return view('public.videos.index', compact('videos'));
    }

    public function videoShow(Video $video)
    {
        $video->youtube_id = $video->youtubeId();
        return view('public.videos.show', compact('video'));
    }

    /**
     * Serve a cooperative resource file (PDF/PPT) streaming through the app.
     */
    public function cooperativeResourceFile(CooperativeResource $resource)
    {
        $path = $resource->file_path;
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

        // public/storage/<path>
        $publicStoragePath = public_path('storage/' . ltrim($path, '/'));
        if (file_exists($publicStoragePath)) {
            return $this->streamFileResponse($publicStoragePath, request()->query('dl'));
        }

        return abort(404);
    }

    public function cooperativeResourceShow(CooperativeResource $resource)
    {
        // Determine where the file is and build view/download URLs similar to MemorandumController
        $path = $resource->file_path;
        if (empty($path)) {
            abort(404);
        }

        $assetUrl = null;
        // Check storage/app/public first
        $storageAppPath = storage_path('app/public/' . ltrim($path, '/'));
        if (file_exists($storageAppPath)) {
            // Use the public storage asset URL so we can detect the file extension,
            // but still stream the file through our route when viewing.
            $assetUrl = asset('storage/' . ltrim($path, '/'));
        } else {
            $publicPath = public_path($path);
            $publicStoragePath = public_path('storage/' . $path);
            if (file_exists($publicPath)) {
                $assetUrl = asset($path);
            } elseif (file_exists($publicStoragePath)) {
                $assetUrl = asset('storage/'.$path);
            } elseif (preg_match('/^https?:\/\//', $path)) {
                $assetUrl = $path;
            }
        }

        if (!$assetUrl) {
            return view('public.cooperative_resources.show', compact('resource'))->with('missing', true);
        }

        $isExternal = preg_match('/^https?:\/\//', $path);
        $viewUrl = $isExternal ? $assetUrl : route('cooperative-resources.file', $resource);
        $downloadUrl = $isExternal ? $assetUrl : (route('cooperative-resources.file', $resource) . '?dl=1');
        $ext = strtolower(pathinfo($assetUrl, PATHINFO_EXTENSION));

        // Also load recent memorandums and year filters for the right sidebar (match Memorandum listing behavior)
        $yearCounts = Memorandum::whereNotNull('published_at')
                    ->selectRaw('YEAR(published_at) as year, count(*) as cnt')
                    ->groupBy('year')
                    ->orderByDesc('year')
                    ->pluck('cnt','year')
                    ->toArray();

        $years = array_keys($yearCounts);

        $memoQuery = Memorandum::query();
        if (request()->filled('memo_year')) {
            $memoQuery->whereYear('published_at', request()->input('memo_year'));
        }
        $memorandums = $memoQuery->orderByDesc('published_at')->limit(10)->get();

        $totalCount = Memorandum::count();
        $selectedCount = request()->filled('memo_year') ? ($yearCounts[request()->input('memo_year')] ?? 0) : $totalCount;

        return view('public.cooperative_resources.show', compact('resource','viewUrl','downloadUrl','ext','isExternal','years','yearCounts','memorandums','totalCount','selectedCount'));
    }

    /**
     * Stream a local file to the client with a safe fallback mime-type.
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
            'ppt' => 'application/vnd.ms-powerpoint',
            'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        ];

        return $map[$ext] ?? 'application/octet-stream';
    }

}
