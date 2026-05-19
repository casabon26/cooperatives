<?php

namespace App\Http\Controllers;

use App\Models\Cooperative;
use App\Models\Slpa;
use App\Models\Gallery;
use App\Models\SelectListItem;
use Illuminate\Pagination\LengthAwarePaginator;
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
        $memorandums = $memoQuery->orderByDesc('published_at')->limit(5)->get();

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

        // If cooperative profiles table exists, eager-load profile to check for profile images
        if (\Schema::hasTable('cooperative_profiles')) {
            $query->with('profile');
        }

        $cooperatives = $query->orderBy('name')->limit($perPage)->get();

        $rows = $cooperatives->map(function($c){
            $imgUrl = null;
            // prefer cooperative image if present
            if (!empty($c->image ?? null)) {
                $p = ltrim($c->image, '/');
                $storagePath = storage_path('app/public/' . $p);
                $directPath = public_path($p);
                $publicCopy = public_path('cooperative_images/' . basename($p));
                if (file_exists($storagePath)) {
                    $imgUrl = asset('storage/' . $p);
                } elseif (file_exists($directPath)) {
                    $imgUrl = asset($p);
                } elseif (file_exists($publicCopy)) {
                    $imgUrl = asset('cooperative_images/' . basename($p));
                }
            }

            // fallback to profile image if available
            if (!$imgUrl && !empty($c->profile->image ?? null)) {
                $p = ltrim($c->profile->image, '/');
                $storagePath = storage_path('app/public/' . $p);
                $directPath = public_path($p);
                $publicCopy = public_path('cooperative_images/' . basename($p));
                if (file_exists($storagePath)) {
                    $imgUrl = asset('storage/' . $p);
                } elseif (file_exists($directPath)) {
                    $imgUrl = asset($p);
                } elseif (file_exists($publicCopy)) {
                    $imgUrl = asset('cooperative_images/' . basename($p));
                }
            }

            return [
                'id' => $c->id,
                'name' => $c->name,
                'sector' => $c->sector,
                'region' => $c->region,
                'description' => $c->description,
                'link' => $c->link,
                'image_url' => $imgUrl,
            ];
        })->values();

        return response()->json(['data' => $rows]);
    }

    public function profile(Cooperative $cooperative)
    {
        if (\Schema::hasTable('cooperative_profiles')) {
            $cooperative->load('profile');
        }
        // also load members (users) when available
        if (\Schema::hasTable('cooperative_user')) {
            $cooperative->load('users');
        }
        $cooperative->load('documents');
        return view('public.profile', compact('cooperative'));
    }

    /**
     * Return a small HTML fragment for cooperative details used in an AJAX modal.
     */
    public function profileModal(Cooperative $cooperative)
    {
        if (\Schema::hasTable('cooperative_profiles')) {
            $cooperative->load('profile');
        }
        $cooperative->load('documents');
        return view('public.partials.cooperative_modal', compact('cooperative'));
    }

    public function news()
    {
        $news = News::whereNotNull('published_at')->orderByDesc('published_at')->paginate(12);
        if (request()->ajax()) {
            if (view()->exists('public.news._list')) {
                return view('public.news._list', compact('news'));
            }
            // Partial view removed — return empty 204 for AJAX callers
            return response('', 204);
        }

        // If the full news view was removed, redirect to home with an informational message
        if (!view()->exists('public.news.index')) {
            return redirect('/')->with('error', 'News is currently unavailable.');
        }

        return view('public.news.index', compact('news'));
    }

    public function newsShow(News $news)
    {
        if (!view()->exists('public.news.show')) {
            return redirect('/')->with('error', 'News is currently unavailable.');
        }
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

        // External URL in file_path -> redirect
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
        $assetUrl = null;
        $gdriveLink = $resource->gdrive_link ?? null;

        // Prefer local or direct file_path for preview (storage/app/public, public, or http in file_path).
        if (!empty($path)) {
            $storageAppPath = storage_path('app/public/' . ltrim($path, '/'));
            if (file_exists($storageAppPath)) {
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
        }

        // If no usable assetUrl from file_path, fall back to gdrive link if present
        if (empty($assetUrl) && !empty($gdriveLink)) {
            $assetUrl = $gdriveLink;
        }

        if (!$assetUrl) {
            return view('public.cooperative_resources.show', compact('resource'))->with('missing', true);
        }

        // Determine externalness based on the original file path when possible (match Memorandum logic).
        if (!empty($path)) {
            $isExternal = preg_match('/^https?:\/\//', $path);
        } else {
            // If no file_path and we fell back to a gdrive link, it's external.
            $isExternal = !empty($gdriveLink) && ($assetUrl === $gdriveLink);
        }
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

        return view('public.cooperative_resources.show', compact('resource','viewUrl','downloadUrl','ext','isExternal','gdriveLink','years','yearCounts','memorandums','totalCount','selectedCount'));
    }

    /**
     * Public Livelihood page showing SLPA entries.
     */
    public function livelihood(Request $request)
    {
        // Only query the table if it exists to avoid migration ordering issues
        $slpas = [];
        if (\Schema::hasTable('slpas')) {
            $slpas = Slpa::orderBy('name')->get();
        }

        $programs = [];
        $services = [];
        $cabstops = [];
        if (\Schema::hasTable('select_list_items')) {
            $programs = SelectListItem::where('group','programs')->where('active', true)->orderBy('label')->get();
            $services = SelectListItem::where('group','services')->where('active', true)->orderBy('label')->get();
            $cabstops = SelectListItem::where('group','cabstop')->where('active', true)->orderBy('label')->get();
        }

        $galleries = [];
        if (\Schema::hasTable('galleries')) {
            $galleries = Gallery::where('section','livelihood')->orderByDesc('created_at')->limit(10)->get();
        }

        return view('pages.livelihood', compact('slpas','programs','services','cabstops','galleries'));
    }

    /**
     * Return a small HTML fragment for SLPA details used in an AJAX modal.
     */
    public function slpaModal(Slpa $slpa)
    {
        return view('public.partials.slpa_modal', compact('slpa'));
    }

    /**
     * Gallery listing (all photos)
     */
    public function galleryIndex(Request $request)
    {
        $galleries = [];
        if (\Schema::hasTable('galleries')) {
            $section = $request->input('section','livelihood');
            $galleries = Gallery::where('section',$section)->orderByDesc('created_at')->paginate(12);
        }
        return view('public.gallery.index', compact('galleries'));
    }

    /**
     * Return small HTML fragment for gallery item used in AJAX modal.
     */
    public function galleryModal(Gallery $gallery)
    {
        return view('public.partials.gallery_modal', compact('gallery'));
    }

    /**
     * Show a full SLPA profile page.
     */
    public function slpaShow(Slpa $slpa)
    {
        $raw = data_get($slpa, 'products');
        // If model cast returned null (legacy string stored), try raw attribute
        if ((is_null($raw) || $raw === '') && is_array($slpa->getAttributes()) && array_key_exists('products', $slpa->getAttributes())) {
            $rawAttr = $slpa->getAttributes()['products'];
            if (is_string($rawAttr) && trim($rawAttr) !== '') {
                $raw = $rawAttr;
            }
        }
        $products = [];
        if (is_array($raw)) {
            foreach ($raw as $p) {
                if (is_string($p)) {
                    $name = trim($p);
                    $desc = '';
                } elseif (is_array($p) || is_object($p)) {
                    $name = trim(data_get($p, 'name', ''));
                    $desc = trim(data_get($p, 'description', ''));
                } else {
                    continue;
                }
                if ($name !== '') $products[] = ['name' => $name, 'description' => $desc];
            }
        } elseif (is_string($raw) && trim($raw) !== '') {
            $parts = preg_split('/\r?\n|,/', $raw);
            foreach ($parts as $p) {
                $p = trim($p);
                if ($p !== '') $products[] = ['name' => $p, 'description' => ''];
            }
        }

        $perPage = 10;
        $page = max(1, (int) request()->input('prod_page', 1));
        $total = count($products);
        $offset = ($page - 1) * $perPage;
        $items = array_slice($products, $offset, $perPage);

        $paginator = new LengthAwarePaginator($items, $total, $perPage, $page, [
            'path' => route('slpas.show', $slpa),
            'pageName' => 'prod_page',
        ]);

        return view('public.slpas.show', compact('slpa', 'paginator'));
    }

    /**
     * Public listing for accomplishment reports
     */
    public function accomplishmentReports(Request $request)
    {
        $reports = AccomplishmentReport::whereNotNull('published_at')->orderByDesc('published_at')->paginate(12);
        return view('public.accomplishment_reports.index', compact('reports'));
    }

    /**
     * Public listing for cooperative resources
     */
    public function cooperativeResources(Request $request)
    {
        $query = CooperativeResource::query();
        if ($request->filled('year')) {
            $query->whereYear('created_at', $request->input('year'));
        }
        $resources = $query->orderByDesc('created_at')->paginate(12)->withQueryString();
        return view('public.cooperative_resources.index', compact('resources'));
    }

    /** Stream an accomplishment report file (download or inline) */
    public function accomplishmentReportFile(\App\Models\AccomplishmentReport $report)
    {
        $path = $report->file_path ?? '';
        if (empty($path)) abort(404);

        if (preg_match('/^https?:\/\//', $path)) {
            return redirect()->away($path);
        }

        $storagePath = storage_path('app/public/' . ltrim($path, '/'));
        if (file_exists($storagePath)) {
            return $this->streamFileResponse($storagePath, request()->query('dl'));
        }
        $publicPath = public_path(ltrim($path, '/'));
        if (file_exists($publicPath)) {
            return $this->streamFileResponse($publicPath, request()->query('dl'));
        }
        $publicStoragePath = public_path('storage/' . ltrim($path, '/'));
        if (file_exists($publicStoragePath)) {
            return $this->streamFileResponse($publicStoragePath, request()->query('dl'));
        }

        abort(404);
    }

    /** Show an accomplishment report page with preview when possible */
    public function accomplishmentReportShow(\App\Models\AccomplishmentReport $report)
    {
        $path = $report->file_path ?? '';
        if (empty($path)) abort(404);

        $assetUrl = null;
        $storageAppPath = storage_path('app/public/' . ltrim($path, '/'));
        if (file_exists($storageAppPath)) {
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

        if (!$assetUrl) return view('public.accomplishment_reports.show', compact('report'))->with('missing', true);

        $isExternal = preg_match('/^https?:\/\//', $path);
        $viewUrl = $isExternal ? $assetUrl : route('accomplishment-reports.file', $report);
        $downloadUrl = $isExternal ? $assetUrl : (route('accomplishment-reports.file', $report) . '?dl=1');
        $ext = strtolower(pathinfo($assetUrl, PATHINFO_EXTENSION));

        return view('public.accomplishment_reports.show', compact('report','viewUrl','downloadUrl','ext','isExternal'));
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
