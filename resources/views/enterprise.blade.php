@extends('layouts.app')

@section('content')
<div class="container py-5">
    <style>
        /* Disable hover effects for Enterprise Portal cards */
        .enterprise-no-hover,
        .enterprise-no-hover .card {
            transition: none !important;
        }
        .enterprise-no-hover .card:hover,
        .no-hover-card:hover {
            transform: none !important;
            box-shadow: none !important;
            filter: none !important;
        }

        /* Modern, minimal portal styles scoped to this page */
        .enterprise-portal .card {
            border: 1px solid rgba(16,24,40,0.04);
            border-radius: 12px;
            background: linear-gradient(180deg, #ffffff, #ffffff);
            box-shadow: 0 8px 24px rgba(15,23,42,0.03);
        }

        .enterprise-portal .card-body h3 {
            font-size: 1.375rem;
            font-weight: 700;
            letter-spacing: -0.2px;
            margin-bottom: 0.25rem;
            color: #0f172a;
        }

        .enterprise-portal .card-body p.small.text-muted {
            color: #475569;
            font-size: 0.95rem;
            margin-bottom: 1rem;
        }

        .enterprise-portal .portal-intro ul {
            list-style: none;
            padding-left: 0;
            margin: 0.6rem 0 1rem 0;
            display: grid;
            gap: 0.5rem;
        }
        .enterprise-portal .portal-intro ul li {
            padding-left: 1.5rem;
            position: relative;
            color: #334155;
        }
        .enterprise-portal .portal-intro ul li::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0.55rem;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: linear-gradient(180deg,#16a34a,#059669);
            box-shadow: 0 1px 0 rgba(0,0,0,0.05);
        }

        /* Accordion modern look */
        .enterprise-portal .accordion {
            gap: 0.6rem;
        }
        .enterprise-portal .accordion .accordion-item {
            background: transparent;
            border: none;
        }
        .enterprise-portal .accordion .accordion-button {
            border: 1px solid rgba(15,23,42,0.06);
            border-radius: 8px;
            background: #fff;
            padding: 0.9rem 1rem;
            color: #0f172a;
            box-shadow: 0 6px 14px rgba(15,23,42,0.03);
        }
        .enterprise-portal .accordion .accordion-button:not(.collapsed) {
            background: linear-gradient(180deg, #f8fafc, #ffffff);
        }
        .enterprise-portal .accordion .accordion-body {
            padding: 0.5rem 0 0 0;
        }
        .enterprise-portal .msme-list .list-group-item {
            border: none;
            padding: 0.6rem 0.75rem;
            border-radius: 6px;
        }
        /* Number badge shown on each list item (light, theme-complementing and right-aligned) */
        .msme-index-badge {
            display:inline-flex;
            align-items:center;
            justify-content:center;
            width:32px;
            height:32px;
            border-radius:8px;
            background: linear-gradient(180deg,#fff5f5,#fff8f8);
            color: #b91c1c;
            font-weight:700;
            margin-right:0;
            box-shadow: 0 6px 18px rgba(185,28,28,0.06);
            flex:0 0 32px;
            text-align:center;
            font-size:0.88rem;
            border: 1px solid rgba(185,28,28,0.06);
            order: 2; /* render at end of flex row */
            margin-left: auto; /* push to the right */
        }

        /* Total count badge in accordion header: right-side boxed label with rounded right corners */
        .msme-total-badge {
            display:inline-flex !important;
            align-items:center;
            justify-content:center;
            min-width:44px;
            height:30px;
            padding:0 12px;
            border-radius:0 10px 10px 0; /* rounded only on the right side */
            background: linear-gradient(180deg,#fff9f9,#fffdfd);
            color: #b91c1c;
            font-weight:700;
            box-shadow: 0 8px 20px rgba(185,28,28,0.04);
            border: 1px solid rgba(185,28,28,0.06);
            font-size:0.86rem;
            position: absolute;
            right: 56px; /* sit left of the caret (swapped) */
            top: 50%;
            transform: translateY(-50%);
            text-align: center;
            line-height: 1;
            z-index: 1; /* keep below caret */
            pointer-events: none; /* don't block caret clicks */
        }

        /* Subtle variant when zero */
        .msme-total-badge.zero {
            background: linear-gradient(180deg,#f8fafc,#fff);
            color:#475569;
            border-color: rgba(15,23,42,0.04);
            box-shadow: none;
        }

        /* Make accordion header layout use flex so badge can sit on the right; add right padding for badge */
        .enterprise-portal .accordion .accordion-button {
            display:flex;
            align-items:center;
            gap:0.6rem;
            position: relative;
            padding-right: 72px; /* room for the total badge + caret */
        }

        /* Ensure the accordion caret renders above the badge */
        /* Hide Bootstrap's default caret and render a custom one to the right of the badge */
        .enterprise-portal .accordion .accordion-button::after { display: none !important; }

        .enterprise-portal .accordion .accordion-button::before{
            content: '\25BE'; /* small down-pointing triangle */
            position: absolute;
            right: 8px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 18px;
            color: rgba(15,23,42,0.9);
            z-index: 999;
            transition: transform 0.18s ease;
            pointer-events: none;
        }

        /* Rotate caret when accordion is expanded */
        .enterprise-portal .accordion .accordion-button:not(.collapsed)::before{
            transform: translateY(-50%) rotate(-180deg);
        }
        /* MSME helper styles: search box and items container which becomes scrollable
           when the number of items exceeds the visible limit (8 items). */
        .msme-search { width:100%; box-sizing:border-box; }
        .msme-items { transition: max-height .15s ease-in-out; }

        /* Sidebar card tweaks */
        .enterprise-portal aside .card {
            background: linear-gradient(180deg,#fff,#feffff);
        }

        /* Responsive tweaks */
        @media (max-width: 767px) {
            .enterprise-portal .card-body h3 { font-size: 1.25rem; }
        }
        /* Hero and text styles */
        .enterprise-portal .portal-hero {
            padding: 1.25rem 1.25rem 1.5rem;
            border-radius: 10px;
            background: linear-gradient(180deg, rgba(99,102,241,0.04), rgba(99,102,241,0.02));
            border: 1px solid rgba(99,102,241,0.06);
            margin-bottom: 1rem;
        }
        .enterprise-portal .portal-hero .portal-sub {
            color: #2563eb;
            font-weight: 700;
            font-size: 0.9rem;
            margin-bottom: 0.25rem;
            display: inline-block;
            padding: 3px 8px;
            border-radius: 6px;
            background: rgba(37,99,235,0.06);
        }
        .enterprise-portal .portal-hero .portal-lead {
            font-size: 1rem;
            color: #334155;
            margin-bottom: 0.75rem;
        }
        .enterprise-portal .section-title {
            font-size: 1.05rem;
            font-weight: 700;
            margin-top: 0.6rem;
            margin-bottom: 0.5rem;
            color: #0f172a;
        }
        .enterprise-portal .lead-muted {
            color: #64748b;
            margin-bottom: 0.85rem;
        }
        .enterprise-portal .check-card {
            background: #fff;
            border: 1px solid rgba(15,23,42,0.04);
            border-radius: 10px;
            padding: 0.85rem 1rem;
            box-shadow: 0 6px 20px rgba(15,23,42,0.02);
        }
        .enterprise-portal .check-card ul { margin:0; padding:0; }
        .enterprise-portal .check-card li { display:flex; gap:0.8rem; align-items:flex-start; padding:0.45rem 0; }
        .enterprise-portal .check-card li .dot { width:10px;height:10px;border-radius:50%;background:linear-gradient(180deg,#10b981,#059669);margin-top:6px;flex:0 0 10px }
        .enterprise-portal .check-card li .text { color:#334155 }
    </style>
    @php
        // Prefer videos explicitly highlighted for the Enterprise Portal; fall back to recent videos
        $enterpriseQuery = \App\Models\Video::where('highlight_enterprise', true)->orderByDesc('created_at');
        if ($enterpriseQuery->count() > 0) {
            $videos = $enterpriseQuery->limit(6)->get();
        } else {
            $videos = \App\Models\Video::orderByDesc('created_at')->limit(6)->get();
        }

        // Enrich with YouTube id + embeddable hint like the PublicController does so the view can embed thumbnails/iframes
        $hasKey = !empty(env('YOUTUBE_API_KEY'));
        foreach ($videos as $v) {
            $v->youtube_id = method_exists($v, 'youtubeId') ? $v->youtubeId() : null;
            $v->embed_allowed = null;
            if ($v->youtube_id) {
                if ($hasKey && method_exists($v, 'checkYouTubeEmbeddable')) {
                    $emb = $v->checkYouTubeEmbeddable();
                    $v->embed_allowed = ($emb === true);
                } else {
                    $v->embed_allowed = null; // optimistic fallback
                }
            }
        }
    @endphp

    @php
        // show totals even before expanding accordions
        $msmeTotals = [];
        foreach(['Micro','Small','Medium','Large'] as $cat){
            try{
                $msmeTotals[$cat] = \App\Models\Enterprise::where('category', $cat)->count();
            }catch(\Throwable $e){
                $msmeTotals[$cat] = 0;
            }
        }
    @endphp

    <div class="row g-4">
        <main class="col-12 col-lg-8 enterprise-no-hover enterprise-portal">
            <div class="card">
                <div class="card-body">
                    <div class="portal-hero">
                        <h3 class="mb-1">Enterprise Portal — MSME Sector</h3>
                        <p class="portal-lead">Welcome to the Enterprise Portal. This section focuses on Micro, Small and Medium Enterprises (MSME) initiatives, resources, and support provided through the portal.</p>
                        <div class="check-card">
                            <div class="section-title">About the MSME Sector</div>
                            <p class="lead-muted">MSMEs play a vital role in local economic development. This portal provides information on available programs, training, funding opportunities, and a directory of enterprise support services. Use the links below to access tools and submit enterprise data.</p>
                            <ul>
                                <li><span class="dot"></span><span class="text">Business registration and advisory services</span></li>
                                <li><span class="dot"></span><span class="text">Training and capacity building</span></li>
                                <li><span class="dot"></span><span class="text">Access to markets and procurement</span></li>
                                <li><span class="dot"></span><span class="text">Financial inclusion and micro-loans</span></li>
                            </ul>
                        </div>
                    </div>

                            <div class="mt-3">
                                <div class="accordion" id="msmeAccordion">
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="headingMicro">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseMicro" aria-expanded="false" aria-controls="collapseMicro">
                                                Micro <span class="badge bg-light ms-2 msme-total-badge" data-cat="Micro">{{ $msmeTotals['Micro'] ?? 0 }}</span>
                                            </button>
                                        </h2>
                                        <div id="collapseMicro" class="accordion-collapse collapse" aria-labelledby="headingMicro" data-bs-parent="#msmeAccordion">
                                            <div class="accordion-body p-0">
                                                <div class="list-group msme-list" data-cat="Micro"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="headingSmall">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSmall" aria-expanded="false" aria-controls="collapseSmall">
                                                Small <span class="badge bg-light ms-2 msme-total-badge" data-cat="Small">{{ $msmeTotals['Small'] ?? 0 }}</span>
                                            </button>
                                        </h2>
                                        <div id="collapseSmall" class="accordion-collapse collapse" aria-labelledby="headingSmall" data-bs-parent="#msmeAccordion">
                                            <div class="accordion-body p-0">
                                                <div class="list-group msme-list" data-cat="Small"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="headingMedium">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseMedium" aria-expanded="false" aria-controls="collapseMedium">
                                                Medium <span class="badge bg-light ms-2 msme-total-badge" data-cat="Medium">{{ $msmeTotals['Medium'] ?? 0 }}</span>
                                            </button>
                                        </h2>
                                        <div id="collapseMedium" class="accordion-collapse collapse" aria-labelledby="headingMedium" data-bs-parent="#msmeAccordion">
                                            <div class="accordion-body p-0">
                                                <div class="list-group msme-list" data-cat="Medium"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="headingLarge">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseLarge" aria-expanded="false" aria-controls="collapseLarge">
                                                Large <span class="badge bg-light ms-2 msme-total-badge" data-cat="Large">{{ $msmeTotals['Large'] ?? 0 }}</span>
                                            </button>
                                        </h2>
                                        <div id="collapseLarge" class="accordion-collapse collapse" aria-labelledby="headingLarge" data-bs-parent="#msmeAccordion">
                                            <div class="accordion-body p-0">
                                                <div class="list-group msme-list" data-cat="Large"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                    </section>
                </div>
            </div>
        </main>

        <aside class="col-12 col-lg-4 enterprise-no-hover enterprise-portal">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-2">Video Highlights</h5>
                    <p class="text-muted small">Latest videos related to MSME activities and highlights.</p>

                    <div class="mt-3">
                        @if($videos && $videos->count())
                            <div class="list-group">
                                @foreach($videos as $v)
                                    @php
                                        // compute public URL for uploaded files
                                        $fileUrl = null;
                                        if ($v->file_path) {
                                            $publicPath = public_path($v->file_path);
                                            $storagePath = public_path('storage/'.$v->file_path);
                                            if (file_exists($publicPath)) {
                                                $fileUrl = asset($v->file_path);
                                            } elseif (file_exists($storagePath)) {
                                                $fileUrl = asset('storage/'.$v->file_path);
                                            }
                                        }
                                        $yid = $v->youtube_id ?? null;
                                    @endphp
                                    <div class="list-group-item">
                                        <div class="d-flex gap-2 align-items-start">
                                            <div style="flex:0 0 120px; max-width:120px">
                                                @if($fileUrl)
                                                    <a href="#" class="enterprise-video-thumb" data-type="file" data-src="{{ $fileUrl }}">
                                                        <video muted style="width:100%;height:80px;object-fit:cover;border-radius:4px;" preload="metadata">
                                                            <source src="{{ $fileUrl }}" type="video/mp4">
                                                        </video>
                                                    </a>
                                                @elseif($yid)
                                                    <a href="#" class="enterprise-video-thumb" data-type="youtube" data-yid="{{ $yid }}">
                                                        <img src="https://img.youtube.com/vi/{{ $yid }}/hqdefault.jpg" alt="{{ $v->title }}" style="width:100%; height:80px; object-fit:cover; border-radius:4px;">
                                                    </a>
                                                @elseif($v->url)
                                                    <div class="small text-muted">External video</div>
                                                @else
                                                    <div class="small text-muted">No media</div>
                                                @endif
                                            </div>

                                            <div class="flex-fill">
                                                <div class="fw-semibold">{{ \Illuminate\Support\Str::limit($v->title, 60) }}</div>
                                                <div class="small text-muted">{{ optional($v->created_at)->diffForHumans() }}</div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="alert alert-info mb-0">No videos available.</div>
                        @endif
                    </div>
                </div>
            </div>
        </aside>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function(){
    const panels = document.querySelectorAll('.accordion-collapse');
    if(!panels || !panels.length) return;

    panels.forEach(panel => {
        panel.addEventListener('show.bs.collapse', function(e){
            const list = panel.querySelector('.msme-list');
            if(!list) return;
            // if already loaded, don't re-fetch
            if(list.dataset.loaded === '1') return;
            const cat = list.dataset.cat || '';
            list.innerHTML = '<div class="list-group-item small text-muted">Loading...</div>';
            fetch('/enterprise-portal/enterprises?category=' + encodeURIComponent(cat), {headers:{'X-Requested-With':'XMLHttpRequest'}})
                .then(r => r.ok ? r.json() : Promise.reject(r))
                .then(json => {
                    const data = json.data || [];
                    // build structure: search box + scrollable items container
                    list.innerHTML = '';
                    const search = document.createElement('input');
                    search.type = 'search';
                    search.placeholder = 'Search enterprises...';
                    search.className = 'form-control form-control-sm msme-search mb-2';
                    const itemsWrap = document.createElement('div');
                    itemsWrap.className = 'msme-items list-group';
                    list.appendChild(search);
                    list.appendChild(itemsWrap);

                    // update badge in header with total (if provided)
                    try{
                        var headerBtn = panel.parentElement.querySelector('.accordion-header .accordion-button');
                        if(headerBtn){
                            var badge = headerBtn.querySelector('.msme-total-badge');
                            if(badge) badge.textContent = (json.total !== undefined ? json.total : data.length);
                        }
                    }catch(e){}

                    if(data.length === 0){
                        itemsWrap.innerHTML = '';
                        const placeholder = document.createElement('div');
                        placeholder.className = 'list-group-item small text-muted msme-no-results';
                        placeholder.textContent = 'No enterprises found.';
                        itemsWrap.appendChild(placeholder);
                    } else {
                        data.forEach((ent, index) => {
                            const a = document.createElement('a');
                            a.className = 'list-group-item list-group-item-action d-flex gap-2 align-items-start';
                            a.href = ent.url || ('/enterprises/' + ent.id);
                            // prefer server-computed image_url; fall back to normalizing local paths
                            let imgSrc = ent.image_url || null;
                            if (!imgSrc && ent.image) {
                                const normalized = ent.image.replace(/\\+/g, '/');
                                imgSrc = (normalized.startsWith('http') ? normalized : '/storage/' + normalized.replace(/^\//, ''));
                            }
                            const secondary = (ent.nature_of_business ? (ent.nature_of_business + ' · ') : '') + (ent.address || '') + (ent.account_no ? (' · ' + ent.account_no) : '');
                            const num = (index || 0) + 1;
                            a.innerHTML = '<div class="msme-index-badge">' + num + '</div>' + (imgSrc ? '<img src="' + imgSrc + '" style="width:84px;height:56px;object-fit:cover;border-radius:6px">' : '') + '<div><div class="fw-semibold">' + (ent.name||'') + '</div><div class="small text-muted">' + (ent.summary||'') + '</div><div class="small text-muted">' + secondary + '</div></div>';
                            // searchable text for faster client filtering
                            a.dataset.search = ((ent.name||'') + ' ' + (ent.summary||'') + ' ' + (ent.nature_of_business||'') + ' ' + (ent.address||'') + ' ' + (ent.account_no||'')).toLowerCase();
                            itemsWrap.appendChild(a);
                        });
                        // ensure a hidden placeholder exists for "no results"
                        let placeholder = itemsWrap.querySelector('.msme-no-results');
                        if(!placeholder){
                            placeholder = document.createElement('div');
                            placeholder.className = 'list-group-item small text-muted msme-no-results';
                            placeholder.textContent = 'No enterprises found.';
                            placeholder.style.display = 'none';
                            itemsWrap.appendChild(placeholder);
                        } else {
                            placeholder.style.display = 'none';
                        }
                    }

                    // helper: update max-height and scrolling based on visible items
                    const updateScroll = () => {
                        const visible = Array.from(itemsWrap.querySelectorAll('a.list-group-item')).filter(ch => ch.offsetParent !== null && ch.style.display !== 'none');
                        const ph = itemsWrap.querySelector('.msme-no-results');
                        if(visible.length === 0){
                            if(ph) ph.style.display = '';
                            itemsWrap.style.maxHeight = '';
                            itemsWrap.style.overflowY = '';
                            return;
                        }
                        if(ph) ph.style.display = 'none';
                        if(visible.length > 8){
                            const itemH = (visible[0].offsetHeight || 72);
                            itemsWrap.style.maxHeight = (itemH * 8) + 'px';
                            itemsWrap.style.overflowY = 'auto';
                        } else {
                            itemsWrap.style.maxHeight = '';
                            itemsWrap.style.overflowY = '';
                        }
                    };

                    // helper to render items into itemsWrap
                    let initialData = data.slice();
                    // preserve initial total (server-provided `json.total` when available)
                    let initialTotal = (json.total !== undefined ? json.total : initialData.length);
                    const renderItems = (rows) => {
                        itemsWrap.innerHTML = '';
                        if(!rows || rows.length === 0){
                            const placeholder = document.createElement('div');
                            placeholder.className = 'list-group-item small text-muted msme-no-results';
                            placeholder.textContent = 'No enterprises found.';
                            itemsWrap.appendChild(placeholder);
                            updateScroll();
                            return;
                        }
                        rows.forEach((ent, index) => {
                            const a = document.createElement('a');
                            a.className = 'list-group-item list-group-item-action d-flex gap-2 align-items-start';
                            a.href = ent.url || ('/enterprises/' + ent.id);
                            let imgSrc = ent.image_url || null;
                            if (!imgSrc && ent.image) {
                                const normalized = ent.image.replace(/\\+/g, '/');
                                imgSrc = (normalized.startsWith('http') ? normalized : '/storage/' + normalized.replace(/^\//, ''));
                            }
                            const secondary2 = (ent.nature_of_business ? (ent.nature_of_business + ' · ') : '') + (ent.address || '') + (ent.account_no ? (' · ' + ent.account_no) : '');
                            const num = (index || 0) + 1;
                            a.innerHTML = '<div class="msme-index-badge">' + num + '</div>' + (imgSrc ? '<img src="' + imgSrc + '" style="width:84px;height:56px;object-fit:cover;border-radius:6px">' : '') + '<div><div class="fw-semibold">' + (ent.name||'') + '</div><div class="small text-muted">' + (ent.summary||'') + '</div><div class="small text-muted">' + secondary2 + '</div></div>';
                            a.dataset.search = ((ent.name||'') + ' ' + (ent.summary||'') + ' ' + (ent.nature_of_business||'') + ' ' + (ent.address||'') + ' ' + (ent.account_no||'')).toLowerCase();
                            itemsWrap.appendChild(a);
                        });
                        // ensure placeholder exists but hidden
                        let placeholder = itemsWrap.querySelector('.msme-no-results');
                        if(!placeholder){
                            placeholder = document.createElement('div');
                            placeholder.className = 'list-group-item small text-muted msme-no-results';
                            placeholder.textContent = 'No enterprises found.';
                            placeholder.style.display = 'none';
                            itemsWrap.appendChild(placeholder);
                        }
                        updateScroll();
                    };

                    // initial render
                    renderItems(initialData);
                    // update badge for initial render
                    try{
                        var headerBtn2 = panel.parentElement.querySelector('.accordion-header .accordion-button');
                        if(headerBtn2){
                            var badge2 = headerBtn2.querySelector('.msme-total-badge');
                            if(badge2) badge2.textContent = (json.total !== undefined ? json.total : initialData.length);
                        }
                    }catch(e){}

                    // debounce helper
                    const debounce = (fn, wait) => {
                        let t;
                        return function(...args){
                            clearTimeout(t);
                            t = setTimeout(() => fn.apply(this, args), wait);
                        };
                    };

                    // perform AJAX search and render results
                    const doSearch = (q) => {
                        const catParam = encodeURIComponent(cat);
                        const url = '/enterprise-portal/enterprises?category=' + catParam + (q ? ('&q=' + encodeURIComponent(q)) : '');
                        // show loading indicator
                        itemsWrap.innerHTML = '<div class="list-group-item small text-muted">Searching...</div>';
                        fetch(url, {headers:{'X-Requested-With':'XMLHttpRequest'}})
                            .then(r => r.ok ? r.json() : Promise.reject(r))
                            .then(json => {
                                const rows = json.data || [];
                                renderItems(rows);
                                // update badge with filtered total
                                try{
                                    var headerBtn3 = panel.parentElement.querySelector('.accordion-header .accordion-button');
                                    if(headerBtn3){
                                        var badge3 = headerBtn3.querySelector('.msme-total-badge');
                                        if(badge3) badge3.textContent = (json.total !== undefined ? json.total : rows.length);
                                    }
                                }catch(e){}
                            }).catch(()=>{
                                itemsWrap.innerHTML = '<div class="list-group-item small text-danger">Error searching</div>';
                            });
                    };

                    const debouncedSearch = debounce((val) => {
                        const q = (val||'').trim();
                        if(q === ''){
                            renderItems(initialData);
                            // restore header badge to initial total when search is cleared
                            try{
                                var headerBtnClear = panel.parentElement.querySelector('.accordion-header .accordion-button');
                                if(headerBtnClear){
                                    var badgeClear = headerBtnClear.querySelector('.msme-total-badge');
                                    if(badgeClear) badgeClear.textContent = initialTotal;
                                }
                            }catch(e){}
                        } else {
                            doSearch(q);
                        }
                    }, 300);

                    // wire up search input to AJAX search
                    search.addEventListener('input', function(){
                        debouncedSearch(this.value);
                    });

                    list.dataset.loaded = '1';
                }).catch(()=>{
                    list.innerHTML = '<div class="list-group-item small text-danger">Error loading list</div>';
                });
        });
    });
});
</script>
<!-- Video player modal -->
<div class="modal fade" id="enterpriseVideoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body p-0" style="position:relative; padding:0;">
                <button type="button" class="btn-close position-absolute" data-bs-dismiss="modal" aria-label="Close" style="z-index:1051; right:8px; top:8px"></button>
                <div id="enterpriseVideoContainer" style="width:100%;height:0;padding-bottom:56.25%;position:relative;">
                    <!-- player injected here -->
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function(){
        const modalEl = document.getElementById('enterpriseVideoModal');
        const playerContainer = document.getElementById('enterpriseVideoContainer');
        if (!modalEl || !playerContainer) return;
        const modal = new bootstrap.Modal(modalEl);

        document.querySelectorAll('.enterprise-video-thumb').forEach(el => {
                el.addEventListener('click', function(e){
                        e.preventDefault();
                        const type = el.dataset.type;
                        while(playerContainer.firstChild) playerContainer.removeChild(playerContainer.firstChild);
                        if (type === 'youtube') {
                                const yid = el.dataset.yid;
                                const params = new URLSearchParams({rel:0,modestbranding:1,playsinline:1,autoplay:1,enablejsapi:1});
                                const iframe = document.createElement('iframe');
                                iframe.src = 'https://www.youtube.com/embed/' + encodeURIComponent(yid) + '?' + params.toString();
                                iframe.setAttribute('frameborder','0');
                                iframe.setAttribute('allow','accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; fullscreen');
                                iframe.style.position = 'absolute'; iframe.style.inset = '0'; iframe.style.width = '100%'; iframe.style.height = '100%'; iframe.loading = 'lazy';
                                playerContainer.appendChild(iframe);
                        } else if (type === 'file') {
                                const src = el.dataset.src;
                                const video = document.createElement('video');
                                video.src = src;
                                video.controls = true;
                                video.autoplay = true;
                                video.style.position = 'absolute'; video.style.inset = '0'; video.style.width = '100%'; video.style.height = '100%';
                                playerContainer.appendChild(video);
                        }
                        modal.show();
                });
        });

        // Cleanup on close
        modalEl.addEventListener('hidden.bs.modal', function(){
                while(playerContainer.firstChild) playerContainer.removeChild(playerContainer.firstChild);
        });
});
</script>
@endsection
