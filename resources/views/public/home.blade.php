@extends('layouts.app')

@section('content')
<div class="py-4">
    <style>
        /* Your original styles remain untouched */
        .hero h1 {
            text-shadow: 0 2px 8px rgba(0,0,0,0.6);
            color: #ffffff;
        }
        .hero p {
            text-shadow: 0 1px 3px rgba(0,0,0,0.5);
            color: rgba(255,255,255,0.85);
        }

        .hero {
            padding: 1rem 1.25rem;
            border-radius: .6rem;
            box-shadow: 0 12px 40px rgba(0,0,0,0.5);
            border: 1px solid rgba(255,255,255,0.04);
        }

        .card:not(.coop-item):hover,
        .card:not(.coop-item) .card-body:hover {
            transform: none !important;
            box-shadow: none !important;
            filter: none !important;
        }
        .card:not(.coop-item) {
            transition: none !important;
        }

        .yt-thumb,
        .yt-thumb img,
        .yt-thumb svg {
            pointer-events: auto !important;
        }

        .yt-thumb:hover {
            opacity: 0.92;
        }

        .memo-link {
            color: #5b1b1b;
            font-weight: 700;
            display: inline-block;
            padding: 4px 6px;
            border-radius: 6px;
            background-color: transparent;
            transition: background .12s ease, color .12s ease, box-shadow .12s ease;
        }

        .memo-item:hover .memo-link,
        .memo-link:focus {
            background: #ef4444;
            color: #ffffff !important;
            box-shadow: 0 6px 18px rgba(var(--primary-r), 0.12);
            text-decoration: none;
        }

        .memo-item { padding: 6px 0; }

        .memo-list { display:block; margin:0; padding:0; }
        .memo-item { 
            display:flex; gap:.6rem; align-items:flex-start; 
            padding:.55rem .5rem; border-radius:8px; 
            transition:background .12s ease, box-shadow .12s ease; 
        }
        .memo-item > .flex-grow-1 { min-width: 0; }
        .memo-item + .memo-item { margin-top:.45rem; }
        .memo-item:hover { 
            background: linear-gradient(180deg, rgba(var(--primary-r), 0.03), rgba(0,0,0,0.01)); 
            box-shadow: 0 8px 20px rgba(15,23,42,0.03); 
        }
        .memo-icon {
            width:44px; height:44px; border-radius:8px;
            background:linear-gradient(180deg,#fff,#fff);
            display:inline-flex; align-items:center; justify-content:center;
            border:1px solid rgba(0,0,0,0.04); color:var(--primary); font-weight:700
        }
        .memo-link { 
            color: #5b1b1b; font-weight:700; display:block; 
            text-decoration:none; overflow:hidden; position:relative; cursor:pointer 
        }
        .memo-link {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            white-space: normal;
            text-overflow: ellipsis;
        }
        .memo-link:hover { text-decoration:none; }

        .memo-link::after {
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
        .memo-link:hover::after, .memo-link:focus::after { 
            opacity:1; transform: translateY(0); 
        }

        .memo-meta { font-size:.825rem; color: #6b6b6b; margin-top:.25rem }
        .memo-actions { display:flex; align-items:center; gap:.4rem }
        .memo-badge { 
            background: rgba(var(--primary-r), 0.08); 
            color:var(--primary); padding:.18rem .45rem; 
            border-radius:6px; font-weight:700; font-size:.75rem 
        }
        .memo-item:hover .memo-badge { 
            background:#ef4444; color:#fff; 
        }

        .memo-filter .dropdown-toggle {
            display: inline-flex;
            align-items: center;
            gap: .5rem;
        }
        .memo-filter .dropdown-menu .active {
            background-color: rgba(var(--primary-r), 0.08);
            color: var(--danger);
            font-weight: 600;
        }

        .video-highlights-title {
            font-size: 1.4rem;
            font-weight: 700;
            color: var(--primary-dark);
            margin-bottom: 0.5rem;
        }

        .video-subtitle {
            color: #555;
            font-size: 0.95rem;
            margin-bottom: 1.25rem;
        }

        .video-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
            gap: 1.25rem;
        }

        .video-card {
            border-radius: 1rem;           /* softer, modern corners */
            overflow: hidden;
            background: #ffffff;
            box-shadow: 
                0 4px 16px rgba(0,0,0,0.08),
                0 1px 3px rgba(0,0,0,0.1);
            transition: all 0.28s cubic-bezier(0.4, 0, 0.2, 1) !important;
            border: 1px solid rgba(var(--primary-r), 0.06);
        }

        .video-card:hover {
            transform: translateY(-8px) scale(1.02) !important;
            box-shadow: 
                0 20px 40px rgba(var(--primary-r), 0.22),
                0 8px 20px rgba(0,0,0,0.12) !important;
        }
        .video-card:hover .play-button-overlay {
            background: rgba(var(--primary-r), 0.54);
        }

        .video-thumb {
            position: relative;
            aspect-ratio: 16/9;
            background: #0f0f0f;
            overflow: hidden;
        }

        .video-thumb img,
        .video-thumb iframe {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .play-button-overlay {
            position: absolute;
            inset: 0;
            background: rgba(0,0,0,0.35);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.32s ease;
            z-index: 2;
        }

        .play-button-overlay:hover {
            background: rgba(var(--primary-r), 0.6);
        }

        .video-thumb::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(
                to bottom,
                transparent 40%,
                rgba(0,0,0,0.42) 82%,
                rgba(0,0,0,0.68) 100%
            );
            opacity: 0;
            transition: opacity 0.4s ease;
            pointer-events: none;
            z-index: 1;
        }

        .video-card:hover .video-thumb::after {
            opacity: 1;
        }

        .video-thumb img,
        .video-thumb video,
        .video-thumb iframe {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.45s ease, filter 0.35s ease;
        }

        /* If a real player is present (iframe or video), hide the overlay so clicks reach the player */
        .video-thumb iframe + .play-button-overlay,
        .video-thumb video + .play-button-overlay {
            display: none !important;
        }

        .video-card:hover .video-thumb img,
        .video-card:hover .video-thumb video {
            transform: scale(1.06);
            filter: brightness(1.08) contrast(1.05);
        }

        .play-icon {
            width: 68px;
            height: 68px;
            background: #dc2626;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 2.1rem;
            box-shadow: 0 6px 20px rgba(220,38,38,0.4);
            transition: all 0.28s ease;
            transform: scale(0.92);
        }
        .video-card:hover .play-icon {
            transform: scale(1.08);
            box-shadow: 0 10px 30px rgba(220,38,38,0.55);
        }

        /* Duration badge (add this in Blade if you have duration) */
        .video-duration {
            position: absolute;
            bottom: 12px;
            right: 12px;
            background: rgba(0,0,0,0.75);
            color: white;
            font-size: 0.78rem;
            font-weight: 600;
            padding: 0.28rem 0.6rem;
            border-radius: 6px;
            z-index: 3;
            letter-spacing: 0.4px;
        }

        /* Info section - semi-transparent gradient option */
        .video-info {
            padding: 1.1rem 1rem;
            background: linear-gradient(to top, rgba(255,255,255,0.98), #ffffff);
            position: relative;
            z-index: 2;
        }

        .video-title {
            font-size: 1.02rem;
            font-weight: 700;
            margin-bottom: 0.4rem;
            line-height: 1.38;
            color: #dc2626;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .video-date {
            font-size: 0.84rem;
            color: #666;
            font-weight: 500;
            margin-bottom: 0.6rem;
        }

        .video-description {
            font-size: 0.85rem;
            color: #555;
            line-height: 1.4;
            max-height: 60px;
            overflow-y: auto;
            padding-right: 0.4rem;
        }

        .video-description::-webkit-scrollbar {
            width: 4px;
        }

        .video-description::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.05);
            border-radius: 4px;
        }

        .video-description::-webkit-scrollbar-thumb {
            background: rgba(220, 38, 38, 0.4);
            border-radius: 4px;
        }

        .video-description::-webkit-scrollbar-thumb:hover {
            background: rgba(220, 38, 38, 0.6);
        }

        /* Optional: colored left accent stripe on hover */
        .video-card::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 5px;
            background: #dc2626;
            transform: scaleY(0);
            transform-origin: bottom;
            transition: transform 0.35s ease;
            z-index: 1;
        }

        .video-card:hover::before {
            transform: scaleY(1);
        }

        .latest-updates-title {
            font-size: 1.4rem;
            font-weight: 700;
            color: var(--primary-dark);
            margin: 2.5rem 0 1rem;
        }

        .update-list {
            display: flex;
            flex-direction: column;
            gap: 0.9rem;
        }

        .update-row {
            display: flex;
            align-items: center;
            gap: 1.1rem;
            padding: 0.8rem 1rem;
            background: #fff;
            border-radius: 8px;
            border: 1px solid #eee;
            position: relative;
            overflow: hidden;
            transition: transform 0.24s cubic-bezier(0.2, 0.9, 0.2, 1),
                        box-shadow 0.22s ease, border-color 0.18s ease, background 0.18s ease;
        }

        /* subtle colored accent that grows on hover */
        .update-row::before {
            content: '';
            position: absolute;
            left: 0;
            top: 12px;
            bottom: 12px;
            width: 6px;
            background: linear-gradient(180deg, #fca5a5, #dc2626);
            transform: scaleY(0);
            transform-origin: bottom;
            transition: transform 0.28s ease;
            border-top-right-radius: 6px;
            border-bottom-right-radius: 6px;
            opacity: 0.98;
            pointer-events: none;
            z-index: 0;
        }

        .update-row:hover {
            transform: translateY(-6px);
            box-shadow: 0 18px 40px rgba(var(--primary-r), 0.16), 0 6px 20px rgba(0,0,0,0.08);
            border-color: rgba(var(--primary-r), 0.14);
            background: linear-gradient(180deg, rgba(255,245,245,0.9), #ffffff);
        }

        .update-row:hover::before {
            transform: scaleY(1);
        }

        .update-number {
            width: 36px;
            height: 36px;
            background: #dc2626;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1.1rem;
            flex-shrink: 0;
        }

        /* If updates use images instead of numbers, ensure they display correctly */
        .update-row img { height: 48px; width: 48px; object-fit: cover; border-radius: 6px; flex-shrink: 0; }

        .update-content h6 {
            margin: 0 0 0.3rem;
            font-size: 1rem;
            font-weight: 600;
            color: #222;
        }

        .update-content p {
            margin: 0;
            font-size: 0.88rem;
            color: #555;
        }
    </style>

    <section class="mb-5">
        <div class="container">
            <div class="row g-4">
                <!-- Left main content -->
                <div class="col-12 col-lg-8">

                    <!-- Video Highlights -->
                    <div class="mb-5">
                        <h2 class="video-highlights-title">Video Highlights</h2>
                        <p class="video-subtitle">
                            Latest livelihood trainings, cooperative events, success stories and enterprise tips.
                        </p>

                        <div class="video-grid">
                            @if(isset($videos) && $videos->count())
                                @foreach($videos as $v)
                                    <div class="video-card">
                                        <div class="video-thumb">
                                            @if($v->file_path)
                                                @php
                                                    $videoUrl = null;
                                                    if (file_exists(public_path($v->file_path))) $videoUrl = asset($v->file_path);
                                                    elseif (file_exists(public_path('storage/'.$v->file_path))) $videoUrl = asset('storage/'.$v->file_path);
                                                    elseif (preg_match('/^https?:\/\//', $v->file_path)) $videoUrl = $v->file_path;

                                                    $ext = strtolower(pathinfo($v->file_path ?? '', PATHINFO_EXTENSION));
                                                    $mime = 'video/mp4';
                                                    if ($ext === 'webm') $mime = 'video/webm';
                                                    if (in_array($ext, ['ogg','ogv'])) $mime = 'video/ogg';
                                                @endphp

                                                @if($videoUrl)
                                                    <video preload="metadata" class="w-100 h-100 object-fit-cover">
                                                        <source src="{{ $videoUrl }}" type="{{ $mime }}">
                                                    </video>
                                                @else
                                                    <div class="w-100 h-100 d-flex align-items-center justify-content-center text-white">
                                                        Video not found
                                                    </div>
                                                @endif
                                            @elseif($v->url && $v->youtube_id)
                                                <iframe class="w-100 h-100"
                                                        src="https://www.youtube.com/embed/{{ $v->youtube_id }}?rel=0&modestbranding=1&playsinline=1"
                                                        frameborder="0" allowfullscreen loading="lazy"></iframe>
                                            @else
                                                <div class="w-100 h-100 d-flex align-items-center justify-content-center text-white">
                                                    No source
                                                </div>
                                            @endif

                                            <div class="play-button-overlay">
                                                <div class="play-icon">▶</div>
                                            </div>
                                        </div>

                                        <div class="video-info">
                                            <div class="video-title">{{ $v->title }}</div>
                                            <div class="video-date">
                                                {{ optional($v->created_at)->format('M d, Y') }}
                                            </div>
                                            @if($v->description)
                                                <div class="video-description">{{ $v->description }}</div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="text-center py-5 text-muted">
                                    No videos available at the moment.
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Latest Updates -->
                    <div>
                        <h2 class="latest-updates-title">Latest Updates</h2>

                        @php $cards = isset($cardNews) ? collect($cardNews)->sortKeys() : collect(); @endphp

                        <div class="update-list">
                            @if($cards->isNotEmpty())
                                @foreach($cards as $index => $article)
                                    @php
                                        $imgUrl = null;
                                        if($article && ($article->image ?? null)){
                                            $storagePath = public_path('storage/'. $article->image);
                                            $directPath = public_path($article->image);
                                            $publicNewsPath = public_path('assets/images/news/'.basename($article->image));
                                            if(file_exists($storagePath)){
                                                $imgUrl = asset('storage/'.$article->image);
                                            } elseif(file_exists($directPath)){
                                                $imgUrl = asset($article->image);
                                            } elseif(file_exists($publicNewsPath)){
                                                $imgUrl = asset('assets/images/news/'.basename($article->image));
                                            }
                                        }
                                    @endphp
                                    <a href="{{ $article ? route('news.show', $article) : '#' }}" class="update-row text-decoration-none text-reset" target="_self">
                                        @if(!empty($article->image_data) || $imgUrl)
                                            <div>
                                                @if(!empty($article->image_data))
                                                    <img src="data:{{ $article->image_mime }};base64,{{ $article->image_data }}" alt="" style="height:48px; width:48px; object-fit:cover; border-radius:6px; flex-shrink:0;">
                                                @else
                                                    <img src="{{ $imgUrl }}" alt="" style="height:48px; width:48px; object-fit:cover; border-radius:6px; flex-shrink:0;">
                                                @endif
                                            </div>
                                        @else
                                            <div class="update-number">{{ $index + 1 }}</div>
                                        @endif
                                        <div class="update-content">
                                            <h6>{{ $article->title ?? 'Update Title' }}</h6>
                                            @if(!empty($article->content))
                                                <p>{{ Str::limit(strip_tags($article->content), 80) }}</p>
                                            @endif
                                        </div>
                                    </a>
                                @endforeach
                            @else
                                @for($i = 1; $i <= 3; $i++)
                                    <div class="update-row">
                                        <div class="update-number">{{ $i }}</div>
                                        <div class="update-content">
                                            <h6>Sample Update {{ $i }}</h6>
                                            <p>Brief description placeholder goes here...</p>
                                        </div>
                                    </div>
                                @endfor
                            @endif
                        </div>
                    </div>

                </div>

                <!-- Right sidebar — completely unchanged -->
                <aside class="col-12 col-lg-4">
                    @component('components.sidebar-list-section', [
                        'title' => 'Memorandum Circulars',
                        'items' => $memorandums ?? [],
                        'years' => $years ?? [],
                        'yearCounts' => $yearCounts ?? [],
                        'selectedCount' => $selectedCount ?? 0,
                        'totalCount' => $totalCount ?? 0,
                        'iconColor' => '#B82132',
                        'badgeGradient' => 'linear-gradient(135deg,#fee2e2,#fdd2d2)',
                        'badgeColor' => '#991b1b',
                        'actionType' => 'link',
                        'actionRoute' => '/memorandums/{id}',
                        'viewAllRoute' => route('memorandums.index'),
                        'viewAllText' => 'View All Circulars',
                        'noItemsText' => 'No memorandum circulars available for the selected year.',
                    ])
                    @endcomponent

                    

                    @component('components.sidebar-list-section', [
                        'title' => 'Accomplishment Reports',
                        'items' => $accomplishmentReports ?? [],
                        'years' => [],
                        'yearCounts' => [],
                        'selectedCount' => 0,
                        'totalCount' => 0,
                        'iconColor' => '#059669',
                        'badgeGradient' => 'linear-gradient(135deg,#d1fae5,#a7f3d0)',
                        'badgeColor' => '#065f46',
                        'actionType' => 'modal',
                        'viewAllRoute' => route('accomplishment-reports.index'),
                        'viewAllText' => 'View All Reports',
                        'noItemsText' => 'No accomplishment reports available.',
                    ])
                    @endcomponent
                    <!-- Service Request panel linking to external Google Form -->
                    <div class="card sidebar-card mt-3">
                        <div class="card-body">
                            <h5 class="card-title">Service Request</h5>
                            <p class="small text-muted">Use this form to request support for cooperatives, livelihood programs, or enterprise development — for example: capacity-building, training, cooperative registration help, market linkages, business planning, product development, or small-grant guidance.</p>
                            <p class="small text-muted mb-2">Please include your cooperative/enterprise name, contact person, location, a short description of the service you need, expected timeline, and any supporting documents. Our team reviews submissions and will respond within 3–5 business days.</p>
                            <a href="https://docs.google.com/forms/d/e/1FAIpQLSdMlbMUHI9aUCzqgIs5zPs1nNq67wI_7coX1H7HLJpdYk-CFw/viewform" target="_blank" rel="noopener noreferrer" class="btn btn-outline-danger">Open Service Request Form</a>
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </section>

    <!-- Accomplishment Report Modal (unchanged) -->
    <div class="modal fade" id="accomplishmentReportModal" tabindex="-1" aria-labelledby="accomplishmentReportModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="accomplishmentReportModalLabel">Accomplishment Report</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="accomplishmentReportModalBody">
                    <!-- Content will be loaded here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <a href="#" id="accomplishmentFileLink" class="btn btn-primary" target="_blank" style="display:none;">Download Document</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Your existing script (unchanged) -->
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

        // Handle modal item links (generic handler for any modal-based list items)
        document.addEventListener('click', function(e) {
            const modalLink = e.target.closest('.item-modal-link');
            if (modalLink) {
                e.preventDefault();
                e.stopPropagation();
                
                const id = modalLink.dataset.id;
                const title = modalLink.dataset.title;
                const content = modalLink.dataset.content;
                const filePath = modalLink.dataset.file;
                const published = modalLink.dataset.published;
                
                // Update modal content
                const modalTitle = document.getElementById('accomplishmentReportModalLabel');
                const modalBody = document.getElementById('accomplishmentReportModalBody');
                const fileLink = document.getElementById('accomplishmentFileLink');
                
                modalTitle.textContent = title;
                
                let bodyHTML = '';
                
                // Add PDF preview if file exists
                if (filePath) {
                    const ext = filePath.split('.').pop().toLowerCase();
                    if (ext === 'pdf') {
                        const assetUrl = '{{ asset('storage/') }}' + '/' + filePath;
                        bodyHTML += '<div style="height:400px; margin-bottom:15px;">';
                        bodyHTML += '<iframe src="' + assetUrl + '" style="width:100%; height:100%; border:0;" loading="lazy"></iframe>';
                        bodyHTML += '</div>';
                    }
                }
                
                // Add content section
                bodyHTML += '<div class="mb-3">';
                if (published) {
                    bodyHTML += '<p class="small text-muted mb-2"><strong>Published:</strong> ' + published + '</p>';
                }
                if (content) {
                    bodyHTML += '<div class="content-section">' + content + '</div>';
                } else {
                    bodyHTML += '<p class="text-muted">No content available.</p>';
                }
                bodyHTML += '</div>';
                
                modalBody.innerHTML = bodyHTML;
                
                if (filePath) {
                    fileLink.href = '{{ asset('storage/') }}' + '/' + filePath;
                    fileLink.style.display = 'block';
                } else {
                    fileLink.style.display = 'none';
                }
                
                // Show the modal
                const modal = new bootstrap.Modal(document.getElementById('accomplishmentReportModal'));
                modal.show();
                
                return false;
            }
        }, false);

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