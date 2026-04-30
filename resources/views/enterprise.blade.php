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

        /* Modern, minimal portal styles scoped to this page - theme integrated */
        .enterprise-portal .card {
            border: 1px solid var(--card-border);
            border-radius: var(--radius);
            background: linear-gradient(180deg, rgba(var(--primary-r), 0.08) 0%, rgba(255,245,245,0.95) 100%);
            box-shadow: 0 12px 32px rgba(var(--primary-r), 0.06);
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
            color: var(--primary);
            font-weight:700;
            margin-right:0;
            box-shadow: 0 6px 18px rgba(var(--primary-r), 0.06);
            flex:0 0 32px;
            text-align:center;
            font-size:0.88rem;
            border: 1px solid rgba(var(--primary-r), 0.06);
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
            color: var(--primary);
            font-weight:700;
            box-shadow: 0 8px 20px rgba(var(--primary-r), 0.04);
            border: 1px solid rgba(var(--primary-r), 0.06);
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
        .msme-items { 
            max-height: 400px;
            overflow-y: auto;
            transition: max-height .15s ease-in-out;
            scrollbar-width: thin;
            scrollbar-color: rgba(15,23,42,0.3) transparent;
        }
        .msme-items::-webkit-scrollbar {
            width: 6px;
        }
        .msme-items::-webkit-scrollbar-track {
            background: transparent;
        }
        .msme-items::-webkit-scrollbar-thumb {
            background: rgba(15,23,42,0.3);
            border-radius: 3px;
        }
        .msme-items::-webkit-scrollbar-thumb:hover {
            background: rgba(15,23,42,0.5);
        }

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
        
        /* See More button styles */
        .see-more-btn {
            background: transparent;
            border: 0;
            color: #f97316;
            text-decoration: none;
            padding: 0;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            transition: color .12s ease, opacity .12s ease;
        }
        .see-more-btn:hover, .see-more-btn:focus {
            color: #f59e0b;
            text-decoration: none;
            opacity: 0.95;
            outline: none;
        }
        .see-more-caret svg {
            width: 14px;
            height: 14px;
            transition: transform .18s ease;
            transform-origin: center;
            display: block;
        }
        .see-more-caret.expanded svg {
            transform: rotate(180deg);
        }
        
        /* Tabbed MSME interface */
        .msme-tabs {
            border: 1px solid rgba(15,23,42,0.08);
            border-radius: 10px;
            overflow: hidden;
            background: #fff;
            box-shadow: 0 6px 20px rgba(15,23,42,0.02);
        }
        .msme-tabs-nav {
            display: flex;
            gap: 0;
            border-bottom: 2px solid rgba(15,23,42,0.06);
            background: linear-gradient(180deg, #fafbfc, #f8fafc);
        }
        .msme-tabs-nav button {
            flex: 1;
            padding: 1rem;
            border: none;
            background: transparent;
            color: #475569;
            font-weight: 600;
            cursor: pointer;
            transition: all .2s ease;
            position: relative;
            font-size: 0.95rem;
        }
        .msme-tabs-nav button:hover {
            background: rgba(15,23,42,0.03);
            color: #0f172a;
        }
        .msme-tabs-nav button.active {
            color: #0f172a;
            background: #fff;
        }
        .msme-tabs-nav button.active::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            right: 0;
            height: 2px;
            background: linear-gradient(90deg, #2563eb, #1d4ed8);
        }
        .msme-tabs-content {
            padding: 0;
        }
        .msme-tab-pane {
            display: none;
            padding: 1.5rem;
        }
        .msme-tab-pane.active {
            display: block;
        }
        .msme-tab-pane .msme-search {
            margin-bottom: 1rem;
        }
        .msme-tab-pane .msme-items {
            display: grid;
            gap: 0.5rem;
        }
        .msme-tab-pane .list-group-item {
            border: 1px solid rgba(15,23,42,0.04);
            border-radius: 6px;
            padding: 1rem;
            transition: all .2s ease;
        }
        .msme-tab-pane .list-group-item:hover {
            background: rgba(15,23,42,0.02);
            border-color: rgba(15,23,42,0.08);
        }
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
                        <p class="portal-lead">MSML Enterprises operates within the Micro, Small, and Medium Enterprise (MSME) sector and continues to demonstrate steady growth alongside a strong commitment to delivering quality products and services. During the reporting period, the enterprise focused on strengthening its operations, enhancing customer satisfaction, and expanding its market reach. Efficient production processes and the availability of essential resources enabled the business to respond to customer demands in a timely and reliable manner.</p>
                        
                        <div id="msmeFull" style="display:none;">
                            <p class="portal-lead">The enterprise also implemented measures to improve overall performance, including enhanced inventory management, better coordination among staff, and more streamlined daily operations. These improvements contributed to smoother workflows and increased productivity. At the same time, the business remained responsive to market trends and evolving customer needs, allowing it to sustain its competitiveness within the sector.</p>
                            <p class="portal-lead">MSML Enterprises continues to place importance on maintaining strong relationships with its customers and partners through consistent communication and dependable service. This approach has supported the development of trust and a positive reputation within the community. Challenges encountered during the period were addressed through practical solutions and continuous improvement efforts, ensuring stable and efficient operations.</p>
                            <p class="portal-lead">The enterprise reflects resilience in managing its activities and remains focused on strengthening its operations and exploring opportunities for growth and long-term sustainability.</p>
                        </div>
                        
                        <button id="msmeMoreBtn" class="see-more-btn" aria-expanded="false" type="button" style="margin-top:0.5rem;">
                            <span class="see-more-label">See More</span>
                            <span class="see-more-caret" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6 6-6"></path></svg></span>
                        </button>
                        
                        <script>
                            (function(){
                                const btn = document.getElementById('msmeMoreBtn');
                                const full = document.getElementById('msmeFull');
                                const label = btn.querySelector('.see-more-label');
                                const caret = btn.querySelector('.see-more-caret');
                                let expanded = false;
                                
                                btn.addEventListener('click', function(){
                                    expanded = !expanded;
                                    if(expanded){
                                        full.style.display = 'block';
                                        btn.setAttribute('aria-expanded', 'true');
                                        label.textContent = 'See Less';
                                        caret.classList.add('expanded');
                                    } else {
                                        full.style.display = 'none';
                                        btn.setAttribute('aria-expanded', 'false');
                                        label.textContent = 'See More';
                                        caret.classList.remove('expanded');
                                    }
                                });
                            })();
                        </script>
                    </div>

                            <div class="mt-3">
                                <div class="msme-tabs">
                                    <div class="msme-tabs-nav">
                                        <button class="msme-tab-btn active" data-tab="micro">Micro <span class="msme-count" data-cat="Micro">({{ $msmeTotals['Micro'] ?? 0 }})</span></button>
                                        <button class="msme-tab-btn" data-tab="small">Small <span class="msme-count" data-cat="Small">({{ $msmeTotals['Small'] ?? 0 }})</span></button>
                                        <button class="msme-tab-btn" data-tab="medium">Medium <span class="msme-count" data-cat="Medium">({{ $msmeTotals['Medium'] ?? 0 }})</span></button>
                                        <button class="msme-tab-btn" data-tab="large">Large <span class="msme-count" data-cat="Large">({{ $msmeTotals['Large'] ?? 0 }})</span></button>
                                    </div>
                                    <div class="msme-tabs-content">
                                        <div class="msme-tab-pane active" data-tab-pane="micro" data-cat="Micro"></div>
                                        <div class="msme-tab-pane" data-tab-pane="small" data-cat="Small"></div>
                                        <div class="msme-tab-pane" data-tab-pane="medium" data-cat="Medium"></div>
                                        <div class="msme-tab-pane" data-tab-pane="large" data-cat="Large"></div>
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
    const tabBtns = document.querySelectorAll('.msme-tab-btn');
    const tabPanes = document.querySelectorAll('.msme-tab-pane');
    const loadedTabs = {};

    function loadTabData(cat, tabPane) {
        if(loadedTabs[cat]) return; // already loaded
        
        tabPane.innerHTML = '<div style="padding: 1rem; text-align: center; color: #999;">Loading...</div>';
        fetch('/enterprise-portal/enterprises?category=' + encodeURIComponent(cat), {headers:{'X-Requested-With':'XMLHttpRequest'}})
            .then(r => r.ok ? r.json() : Promise.reject(r))
            .then(json => {
                const allData = json.data || [];
                const limited = allData; // Show all items, scrollable
                
                // Create search box
                const searchBox = document.createElement('input');
                searchBox.type = 'search';
                searchBox.placeholder = 'Search enterprises...';
                searchBox.className = 'form-control form-control-sm msme-search';
                
                // Create items container
                const itemsWrap = document.createElement('div');
                itemsWrap.className = 'msme-items';
                
                tabPane.innerHTML = '';
                tabPane.appendChild(searchBox);
                tabPane.appendChild(itemsWrap);
                
                // Render items
                const renderItems = (items) => {
                    itemsWrap.innerHTML = '';
                    if(!items || items.length === 0) {
                        itemsWrap.innerHTML = '<div style="padding: 1rem; color: #999; text-align: center;">No enterprises found.</div>';
                        return;
                    }
                    items.forEach((ent, index) => {
                        const a = document.createElement('a');
                        a.className = 'list-group-item list-group-item-action d-flex gap-2 align-items-start';
                        a.href = ent.url || ('/enterprises/' + ent.id);
                        let imgSrc = ent.image_url || null;
                        if (!imgSrc && ent.image) {
                            const normalized = ent.image.replace(/\\+/g, '/');
                            imgSrc = (normalized.startsWith('http') ? normalized : '/storage/' + normalized.replace(/^\//, ''));
                        }
                        const secondary = (ent.nature_of_business ? (ent.nature_of_business + ' · ') : '') + (ent.address || '') + (ent.account_no ? (' · ' + ent.account_no) : '');
                        const num = (index || 0) + 1;
                        a.innerHTML = '<div class="msme-index-badge">' + num + '</div>' + (imgSrc ? '<img src="' + imgSrc + '" style="width:84px;height:56px;object-fit:cover;border-radius:6px">' : '') + '<div><div class="fw-semibold">' + (ent.name||'') + '</div><div class="small text-muted">' + (ent.summary||'') + '</div><div class="small text-muted">' + secondary + '</div></div>';
                        a.dataset.search = ((ent.name||'') + ' ' + (ent.summary||'') + ' ' + (ent.nature_of_business||'') + ' ' + (ent.address||'') + ' ' + (ent.account_no||'')).toLowerCase();
                        itemsWrap.appendChild(a);
                    });
                    
                    const more = document.createElement('div');
                    more.style.padding = '1rem';
                    more.style.textAlign = 'center';
                    more.style.color = '#999';
                    more.style.fontSize = '0.9rem';
                    more.textContent = 'Showing all ' + allData.length + ' enterprises';
                    itemsWrap.appendChild(more);
                };
                
                renderItems(limited);
                
                // Search functionality
                searchBox.addEventListener('input', function(){
                    const q = (this.value||'').trim().toLowerCase();
                    if(!q) {
                        renderItems(limited);
                    } else {
                        const filtered = limited.filter(ent => {
                            const text = ((ent.name||'') + ' ' + (ent.summary||'') + ' ' + (ent.nature_of_business||'') + ' ' + (ent.address||'') + ' ' + (ent.account_no||'')).toLowerCase();
                            return text.includes(q);
                        });
                        renderItems(filtered);
                    }
                });
                
                loadedTabs[cat] = true;
            })
            .catch(()=>{
                tabPane.innerHTML = '<div style="padding: 1rem; color: red; text-align: center;">Error loading enterprises</div>';
            });
    }

    // Load first tab (Micro) by default
    const firstPane = document.querySelector('[data-tab-pane="micro"]');
    if(firstPane) {
        loadTabData('Micro', firstPane);
    }

    // Tab switching
    tabBtns.forEach(btn => {
        btn.addEventListener('click', function(){
            const tabName = this.dataset.tab;
            const catMap = { micro: 'Micro', small: 'Small', medium: 'Medium', large: 'Large' };
            const cat = catMap[tabName];
            
            // Remove active class from all tabs
            tabBtns.forEach(b => b.classList.remove('active'));
            tabPanes.forEach(p => p.classList.remove('active'));
            
            // Add active class to clicked tab
            this.classList.add('active');
            const targetPane = document.querySelector(`[data-tab-pane="${tabName}"]`);
            if(targetPane) {
                targetPane.classList.add('active');
                loadTabData(cat, targetPane);
            }
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
