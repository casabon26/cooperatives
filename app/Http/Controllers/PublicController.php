<?php

namespace App\Http\Controllers;

use App\Models\Cooperative;
use App\Models\News;
use App\Models\Video;
use App\Models\Memorandum;
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
        // Load latest videos (display on landing page) and enrich with embeddability info
        $videos = Video::orderByDesc('created_at')->limit(6)->get();
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

        return view('public.home', compact('news','cardNews','cooperatives','videos','memorandums','years','yearCounts','totalCount','selectedCount'));
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
        return view('public.directory', compact('cooperatives'));
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
}
