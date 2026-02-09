@extends('layouts.app')

@section('content')
<div class="py-4">
    <style>
        /* Slightly darker text-shadow for the landing header */
        .hero h1 {
            text-shadow: 0 2px 8px rgba(0,0,0,0.6);
            color: #ffffff;
        }
        .hero p {
            text-shadow: 0 1px 3px rgba(0,0,0,0.5);
            color: rgba(255,255,255,0.85);
        }

        /* Hero styling: visual details only; background comes from theme.css so images show correctly */
        .hero {
            padding: 1rem 1.25rem;
            border-radius: .6rem;
            box-shadow: 0 12px 40px rgba(0,0,0,0.5);
            border: 1px solid rgba(255,255,255,0.04);
            /* do not set background here — theme.css controls the hero background image */
        }

        /* Disable hover effects except for cooperative items */
        .card:not(.coop-item):hover,
        .card:not(.coop-item) .card-body:hover {
            transform: none !important;
            box-shadow: none !important;
            filter: none !important;
        }
        .card:not(.coop-item) {
            transition: none !important;
        }

        /* Make sure thumbnail clicks are not stolen by parent links */
        .yt-thumb,
        .yt-thumb img,
        .yt-thumb svg {
            pointer-events: auto !important;
        }

        /* Prevent any parent links from interfering on thumbnails only */
        /* (specific '.yt-thumb' elements already set to accept pointer events) */

        .yt-thumb:hover {
            opacity: 0.92;
        }
        /* Memorandum link: plain bright red text (no outline) */
        .memo-link {
            color: var(--danger);
            font-weight: 600;
            display: inline-block;
            padding: 4px 6px;
            border-radius: 4px;
            background-color: transparent;
            border: none;
            -webkit-text-stroke: 0;
            text-shadow: none;
        }
        .memo-link:hover, .memo-link:focus {
            color: #ffffff;
            text-decoration: none;
            background-color: var(--danger);
            padding: 4px 8px;
            border-radius: 6px;
            box-shadow: 0 6px 18px rgba(0,0,0,0.06);
        }
        .memo-item { padding: 6px 0; }
            /* Memorandum circulars: theme-aligned list */
            .memo-list { display:block; margin:0; padding:0; }
            .memo-item { display:flex; gap:.6rem; align-items:flex-start; padding:.55rem .5rem; border-radius:8px; transition:background .12s ease, box-shadow .12s ease; }
            .memo-item > .flex-grow-1 { min-width: 0; }
            .memo-item + .memo-item { margin-top:.45rem; }
            .memo-item:hover { background: linear-gradient(180deg, rgba(239,68,68,0.03), rgba(0,0,0,0.01)); box-shadow: 0 8px 20px rgba(15,23,42,0.03); }
            .memo-icon{width:44px;height:44px;border-radius:8px;background:linear-gradient(180deg,#fff,#fff);display:inline-flex;align-items:center;justify-content:center;border:1px solid rgba(0,0,0,0.04);color:#b91c1c;font-weight:700}
            .memo-link { color: #5b1b1b; font-weight:700; display:block; text-decoration:none; overflow:hidden; position:relative; cursor:pointer }
            /* Clamp long titles to two lines with ellipsis */
            .memo-link {
                display: -webkit-box;
                -webkit-line-clamp: 2;
                -webkit-box-orient: vertical;
                white-space: normal;
                text-overflow: ellipsis;
            }
            .memo-link:hover, .memo-link:focus { color:#7f1d1d; text-decoration:underline; }
            /* Hover hint shown when codename is hovered */
            .memo-link::after{
                content: "click to open";
                position: absolute;
                top: -1.6rem;
                right: 0;
                background: rgba(0,0,0,0.7);
                color: #fff;
                font-size: .72rem;
                padding: .18rem .45rem;
                border-radius: 6px;
                opacity: 0;
                transform: translateY(6px);
                transition: opacity .12s ease, transform .12s ease;
                white-space: nowrap;
                pointer-events: none;
            }
            .memo-link:hover::after, .memo-link:focus::after{ opacity:1; transform: translateY(0); }
            .memo-meta { font-size:.825rem; color: #6b6b6b; margin-top:.25rem }
            .memo-actions { display:flex; align-items:center; gap:.4rem }
            .memo-badge { background: rgba(185,28,28,0.08); color:#b91c1c; padding:.18rem .45rem; border-radius:6px; font-weight:700; font-size:.75rem }
        /* Dropdown filter tweaks */
        .memo-filter .dropdown-toggle {
            display: inline-flex;
            align-items: center;
            gap: .5rem;
        }
        .memo-filter .dropdown-menu .active {
            background-color: rgba(239,68,68,0.08);
            color: var(--danger);
            font-weight: 600;
        }
    </style>

    {{-- <header class="mb-4 container hero">
        <h1 class="h3">Welcome to the Government Cooperative Portal</h1>
        <p class="text-muted">Official listing and transparency for registered cooperatives.</p>
    </header> --}}

    <section class="mb-4">
        <div class="container">
            <div class="row g-4">
                <!-- Main content (left column) -->
                <div class="col-12 col-lg-8">

                    <!-- Video gallery -->
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title mb-2">Video Highlights</h5>
                            <p class="text-muted small">Latest videos added by administrators. Videos may be uploaded or linked (YouTube/Vimeo).</p>
                            
                            <div class="row mt-3 g-3">
                                @if(isset($videos) && $videos->count())
                                    @foreach($videos as $v)
                                        <div class="col-12 col-md-6">
                                            <div class="card h-100">
                                                <div class="card-body d-flex flex-column video-container">
                                                    <div style="position: relative; min-height: 160px; flex: 1; display: flex; align-items: center; justify-content: center; background: #111; border-radius: 6px; overflow: hidden;">
                                                        @if($v->file_path)
                                                            @php
                                                                $videoUrl = null;
                                                                $publicPath = public_path($v->file_path);
                                                                $storagePath = public_path('storage/'.$v->file_path);
                                                                // if file is already in public folder, use direct asset
                                                                if(file_exists($publicPath)){
                                                                    $videoUrl = asset($v->file_path);
                                                                } elseif(file_exists($storagePath)){
                                                                    $videoUrl = asset('storage/'.$v->file_path);
                                                                } else {
                                                                    // fallback: if file_path already looks like a full URL, use it
                                                                    if(preg_match('/^https?:\/\//', $v->file_path)){
                                                                        $videoUrl = $v->file_path;
                                                                    }
                                                                }

                                                                // infer mime type from extension
                                                                $ext = strtolower(pathinfo($v->file_path, PATHINFO_EXTENSION));
                                                                $mime = 'video/mp4';
                                                                if(in_array($ext, ['webm'])) $mime = 'video/webm';
                                                                if(in_array($ext, ['ogg','ogv'])) $mime = 'video/ogg';
                                                            @endphp
                                                            @if($videoUrl)
                                                                <video controls playsinline preload="metadata" style="width:100%; height:auto; max-height:260px;">
                                                                    <source src="{{ $videoUrl }}" type="{{ $mime }}">
                                                                    Your browser does not support the video tag.
                                                                </video>
                                                            @else
                                                                <div class="text-center p-3">
                                                                    <div class="small text-muted">Video file not found on server.</div>
                                                                    @if(preg_match('/^https?:\/\//', $v->file_path))
                                                                        <a href="{{ $v->file_path }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-primary mt-2">Open video link</a>
                                                                    @else
                                                                        <div class="small text-muted mt-2">Contact admin to re-upload the file.</div>
                                                                    @endif
                                                                </div>
                                                            @endif
                                                        @elseif($v->url)
                                                            @php
                                                                $yid = $v->youtube_id ?? null;
                                                                $embedAllowed = $v->embed_allowed ?? null;
                                                            @endphp

                                                            @if($yid)
                                                                @php
                                                                    $origin = urlencode(request()->getSchemeAndHttpHost());
                                                                    $params = http_build_query([
                                                                        'rel' => 0,
                                                                        'modestbranding' => 1,
                                                                        'playsinline' => 1,
                                                                        'autoplay' => 0,
                                                                        'enablejsapi' => 1,
                                                                        'origin' => request()->getSchemeAndHttpHost(),
                                                                    ]);
                                                                    $iframeSrc = "https://www.youtube.com/embed/{$yid}?{$params}";
                                                                @endphp
                                                                <div style="position:relative;width:100%;height:100%;">
                                                                    <iframe src="{{ $iframeSrc }}" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; fullscreen" allowfullscreen style="position:absolute;inset:0;width:100%;height:100%;border:0;" loading="lazy"></iframe>
                                                                </div>
                                                            @else
                                                                @php
                                                                    $isFacebook = isset($v->url) && preg_match('/facebook\.com|fb\.watch|fbcdn/i', $v->url);
                                                                @endphp
                                                                @if($isFacebook)
                                                                        <div class="d-flex flex-column align-items-center justify-content-center" style="height:100%;">
                                                                            <div class="mb-2 small text-muted">This video is hosted on Facebook</div>
                                                                            <a href="{{ $v->url }}" target="_blank" rel="noopener noreferrer" class="btn btn-primary">Open on Facebook</a>
                                                                        </div>
                                                                @else
                                                                    <a href="{{ $v->url }}" target="_blank" rel="noopener noreferrer" class="btn btn-outline-primary btn-sm">
                                                                        Open video link
                                                                    </a>
                                                                @endif
                                                            @endif
                                                        @else
                                                            <div class="text-muted">No video source available</div>
                                                        @endif
                                                    </div>

                                                    <h6 class="mt-3 mb-1">{{ $v->title }}</h6>
                                                    <div class="small text-muted">{{ optional($v->created_at)->toDayDateTimeString() }}</div>
                                                    @if($v->description)
                                                        <div class="small text-muted mt-1">
                                                            {{ \Illuminate\Support\Str::limit(strip_tags($v->description), 120) }}
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="col-12">
                                        <div class="alert alert-info text-center py-4">
                                            No videos available at the moment.
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Latest Updates cards (dynamic from card_slot assignments) -->
                    <div class="row mt-4 g-3">
                        <h2 id="news" class="h5 mb-3">Latest Updates</h2>
                        @php
                            $cards = isset($cardNews) ? collect($cardNews)->sortKeys() : collect();
                        @endphp

                        @if($cards->isNotEmpty())
                            @foreach($cards as $slot => $article)
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-body d-flex">
                                            <div class="me-3 flex-shrink-0">
                                                @php
                                                    $imgUrl = null;
                                                    if($article && $article->image){
                                                        $storagePath = public_path('storage/'.$article->image);
                                                        $directPath = public_path($article->image);
                                                        $publicNewsPath = public_path('news_images/'.basename($article->image));
                                                        if(file_exists($storagePath)){
                                                            $imgUrl = asset('storage/'.$article->image);
                                                        } elseif(file_exists($directPath)){
                                                            $imgUrl = asset($article->image);
                                                        } elseif(file_exists($publicNewsPath)){
                                                            $imgUrl = asset('news_images/'.basename($article->image));
                                                        }
                                                    }
                                                @endphp
                                                @if($article->image_data)
                                                    <img src="data:{{ $article->image_mime }};base64,{{ $article->image_data }}" alt="" style="height:64px; width:64px; object-fit:cover; border-radius:6px;">
                                                @elseif($imgUrl)
                                                    <img src="{{ $imgUrl }}" alt="" style="height:64px; width:64px; object-fit:cover; border-radius:6px;">
                                                @else
                                                    <div class="bg-primary text-white d-flex align-items-center justify-content-center" style="height:64px; width:64px; border-radius:6px; font-weight:bold;">
                                                        {{ $slot }}
                                                    </div>
                                                @endif
                                            </div>
                                            <div>
                                                @if($article)
                                                    <h6 class="card-title mb-1">
                                                        <a href="#" target="_blank" rel="noopener noreferrer">{{ $article->title }}</a>
                                                    </h6>
                                                    <div class="small text-muted">{{ optional($article->published_at ?? $article->created_at)->toDayDateTimeString() }}</div>
                                                    <p class="card-text small text-muted mt-1">
                                                        {{ \Illuminate\Support\Str::limit(strip_tags($article->content), 160) }}
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            @for($i=1; $i<=3; $i++)
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-body d-flex">
                                            <div class="me-3 flex-shrink-0">
                                                <div class="bg-primary text-white d-flex align-items-center justify-content-center" style="height:64px; width:64px; border-radius:6px; font-weight:bold;">
                                                    {{ chr(64+$i) }}
                                                </div>
                                            </div>
                                            <div>
                                                <h6 class="card-title mb-1">Sample Update {{ $i }}</h6>
                                                <p class="card-text small text-muted">Short placeholder description.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endfor
                        @endif
                    </div>
                </div>

                <!-- Right sidebar: Memorandum Circulars -->
                <aside class="col-12 col-lg-4">
                    <div class="card mb-3">
                        <div class="card-body">
                            <h5 class="card-title mb-3">Memorandum Circulars</h5>

                            @if(isset($years) && count($years))
                                <div class="mb-2">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <label class="small mb-0">Filter by year</label>
                                        <div class="small text-muted">Showing: <strong>{{ $selectedCount ?? (isset($totalCount) ? $totalCount : (isset($memorandums) ? $memorandums->count() : 0)) }}</strong></div>
                                    </div>

                                    <div class="memo-filter">
                                        <div class="dropdown">
                                            @php
                                                $selectedLabel = request('memo_year') ?: 'All years';
                                                $queryBase = url('/');
                                            @endphp
                                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="memoYearDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                                {{ $selectedLabel }}
                                                <span class="badge bg-secondary ms-2">{{ request('memo_year') ? ($yearCounts[request('memo_year')] ?? 0) : ($totalCount ?? 0) }}</span>
                                            </button>
                                            <ul class="dropdown-menu" aria-labelledby="memoYearDropdown">
                                                <li><a class="dropdown-item d-flex justify-content-between align-items-center {{ request('memo_year') ? '' : 'active' }}" href="{{ url('/') }}">All years <span class="badge bg-secondary ms-2">{{ $totalCount ?? 0 }}</span></a></li>
                                                @foreach($years as $y)
                                                    <li>
                                                        <a class="dropdown-item d-flex justify-content-between align-items-center {{ request('memo_year') == $y ? 'active' : '' }}" href="{{ url('/?memo_year='.$y) }}">
                                                            {{ $y }}
                                                            <span class="badge bg-secondary ms-2">{{ $yearCounts[$y] ?? 0 }}</span>
                                                        </a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="mb-2 small text-muted">No years available</div>
                            @endif

                            @if(isset($memorandums) && $memorandums->count())
                                    <ul class="list-unstyled small mb-0 memo-list">
                                    @foreach($memorandums as $memo)
                                            <li class="memo-item">
                                                <div class="memo-icon flex-shrink-0" aria-hidden="true">
                                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M7 7h6v6H7z" stroke="#b91c1c" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/><path d="M21 15V7a2 2 0 0 0-2-2H9" stroke="#b91c1c" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                                </div>

                                                <div class="flex-grow-1">
                                                    <a href="{{ url('/memorandums/'.$memo->id) }}" class="memo-link" title="click to open" aria-label="{{ $memo->title ?? 'Memorandum' }}">
                                                        {{ $memo->code ?? $memo->title ?? 'Memorandum' }}
                                                    </a>
                                                    @if(isset($memo->published_at) || isset($memo->created_at))
                                                        <div class="memo-meta">Published: {{ optional($memo->published_at ?? $memo->created_at)->toFormattedDateString() }}</div>
                                                    @endif
                                                </div>
                                                <div class="flex-shrink-0 text-end" style="min-width:56px;">
                                                    <div class="memo-badge">{{ optional($memo->published_at ?? $memo->created_at)->format('Y') ?? '' }}</div>
                                                </div>
                                            </li>
                                    @endforeach
                                </ul>
                            @else
                                <div class="small text-muted">No memorandum circulars available for the selected year.</div>
                            @endif

                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </section>

    {{-- More News list removed per request; homepage now shows only card highlights and video section. --}}

    <script>
    (function() {
        function createYouTubeIframe(yid) {
            const origin = encodeURIComponent(window.location.origin || window.location.protocol + '//' + window.location.host);
            const params = new URLSearchParams({
                rel: '0',
                enablejsapi: '1',
                modestbranding: '1',
                origin: origin,
                autoplay: '1',
                mute: '1',
                playsinline: '1',
                fs: '1'
            });

            const iframe = document.createElement('iframe');
            iframe.width = '100%';
            iframe.height = '100%';           // better aspect ratio preservation
            iframe.src = `https://www.youtube.com/embed/${yid}?${params.toString()}`;
            iframe.frameBorder = '0';
            iframe.allow = 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; fullscreen';
            iframe.allowFullscreen = true;
            iframe.style.position = 'absolute';
            iframe.style.inset = '0';
            return iframe;
        }

        function activateThumbnail(el) {
            const yid = el.dataset.yid;
            const allowed = el.dataset.embedAllowed;

            console.log('[YouTube Embed] Click detected → Video ID:', yid, 'Allowed:', allowed);

            if (!yid) {
                console.warn('[YouTube Embed] No video ID found');
                return;
            }

            if (allowed === 'false') {
                console.log('[YouTube Embed] Embedding blocked → opening watch page in new tab');
                window.open(`https://www.youtube.com/watch?v=${yid}`, '_blank', 'noopener,noreferrer');
                return;
            }

            console.log('[YouTube Embed] Replacing thumbnail with playable iframe');
            
            const container = el.parentElement;
            const iframe = createYouTubeIframe(yid);
            
            // Clear container and insert iframe
            container.innerHTML = '';
            container.appendChild(iframe);
        }

        // Capture phase - highest priority
        document.addEventListener('click', function(e) {
            let target = e.target.closest('.yt-thumb');
            if (target) {
                console.log('[YouTube Embed] Captured click on .yt-thumb');
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                activateThumbnail(target);
                return false;
            }
        }, true);

        // Keyboard support
        document.addEventListener('keydown', e => {
            if (e.key !== 'Enter' && e.key !== ' ') return;
            const active = document.activeElement;
            if (active?.closest('.yt-thumb')) {
                e.preventDefault();
                activateThumbnail(active.closest('.yt-thumb'));
            }
        }, false);
    })();
    </script>
</div>
@endsection