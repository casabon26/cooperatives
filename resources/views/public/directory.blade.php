@extends('layouts.app')

@section('content')
<div class="py-4">
    <style>
        /* Match cooperative name styling to header nav link and center inside card */
        .coop-name-link { font-weight:600; text-decoration:none; transition: color .12s ease; }
        .card .coop-name-link { color: #b91c1c; display:block; text-align:center; }
        .coop-name-link:hover, .coop-name-link:focus { color:#8f1515; text-decoration:none; }
        .coop-meta { color:#6b7280; }
        .coop-card .card-body { display:flex; flex-direction:column; }
        .coop-card .coop-description { flex:1 1 auto; }

        /* Sidebar memo list styles (matching home memo look) */
        .memo-list { display:block; margin:0; padding:0; }
        .memo-item { display:flex; gap:.6rem; align-items:flex-start; padding:.55rem .5rem; border-radius:8px; transition:background .12s ease, box-shadow .12s ease; }
        .memo-item + .memo-item { margin-top:.45rem; }
        .memo-item:hover { background: linear-gradient(180deg, rgba(239,68,68,0.03), rgba(0,0,0,0.01)); box-shadow: 0 8px 20px rgba(15,23,42,0.03); }
        .memo-icon { width:44px; height:44px; border-radius:8px; display:inline-flex; align-items:center; justify-content:center; color:#b91c1c; }
        .memo-link { color:#5b1b1b; font-weight:700; display:block; text-decoration:none; overflow:hidden; }
        .memo-link { display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; white-space:normal; text-overflow:ellipsis; }
        .memo-link::after { content: "click to open"; position:absolute; top:-1.6rem; right:0; background:rgba(0,0,0,0.7); color:#fff; font-size:.72rem; padding:.18rem .45rem; border-radius:6px; opacity:0; transform:translateY(6px); transition:opacity .12s ease, transform .12s ease; pointer-events:none; }
        .memo-link:hover::after, .memo-link:focus::after { opacity:1; transform:translateY(0); }
        .memo-meta { font-size:.825rem; color:#6b6b6b; margin-top:.25rem }
        .memo-badge { background: rgba(185,28,28,0.08); color:#b91c1c; padding:.18rem .45rem; border-radius:6px; font-weight:700; font-size:.75rem }
        .memo-item:hover .memo-badge { background:#ef4444; color:#fff; }

        /* Prevent year badge highlight on hover for PDF items */
        .no-year-hover:hover .memo-badge { background: rgba(0,0,0,0.03); color:#374151; box-shadow:none; }

        /* Make the "More About Cooperative" sidebar match memorandum circulars */
        aside > .card {
            background: linear-gradient(180deg, #fff5f6, #fff0f2);
            border: 1px solid rgba(239,68,68,0.07);
            border-radius: 12px;
            box-shadow: 0 12px 36px rgba(185,28,28,0.06);
        }
        aside > .card .card-body { padding: 1rem; }
        aside > .card .card-title { color:#7f1d1d; font-weight:700; margin-bottom:.6rem; }
        aside .memo-item { padding:.6rem; border-radius:10px; }
        /* Use the same hover and link colors as Memorandum circulars on the home page */
        aside .memo-item:hover { background: linear-gradient(180deg, rgba(239,68,68,0.03), rgba(0,0,0,0.01)); box-shadow:none; transform:none; }
        aside .memo-link { color:#5b1b1b; font-weight:700; }
        aside .memo-link:hover, aside .memo-link:focus { background: #ef4444; color: #ffffff !important; box-shadow: 0 6px 18px rgba(239,68,68,0.12); text-decoration: none; }
        aside .memo-badge { background: rgba(185,28,28,0.08); color:#b91c1c; padding:.18rem .45rem; border-radius:6px; font-weight:700; }
        aside .memo-item:hover .memo-badge { background:#ef4444; color:#fff; }
    </style>

    <h1 class="h4">Cooperatives Directory</h1>
    <form class="row g-2 my-3" method="get" role="search" aria-label="Search cooperatives">
        <div class="col-12 col-md-6"><input name="q" value="{{ request('q') }}" class="form-control" placeholder="Search by name"></div>
        <div class="col-6 col-md-2">
            <select name="per_page" class="form-select" onchange="this.form.submit()" aria-label="Results per page">
                @foreach([12,24,34,48] as $n)
                    <option value="{{ $n }}" {{ request('per_page',12) == $n ? 'selected' : '' }}>{{ $n }} per page</option>
                @endforeach
            </select>
        </div>
        <div class="col-6 col-md-4 text-end">
            <button type="submit" class="btn btn-outline-secondary">Apply</button>
        </div>
    </form>

    <div class="row">
        <div class="col-lg-8">
            <div class="row row-cols-1 row-cols-md-3 g-3">
                @foreach($cooperatives as $coop)
                    <div class="col">
                        <article class="card h-100 coop-card">
                            @php
                                $imgUrl = null;
                                if (!empty($coop->image ?? null)) {
                                    $storagePath = public_path('storage/'.$coop->image);
                                    $directPath = public_path($coop->image);
                                    $publicCopy = public_path('cooperative_images/'.basename($coop->image));
                                    if (file_exists($storagePath)) {
                                        $imgUrl = asset('storage/'.$coop->image);
                                    } elseif (file_exists($directPath)) {
                                        $imgUrl = asset($coop->image);
                                    } elseif (file_exists($publicCopy)) {
                                        $imgUrl = asset('cooperative_images/'.basename($coop->image));
                                    }
                                }
                                if (!$imgUrl && !empty($coop->profile->image ?? null)) {
                                    $p = $coop->profile->image;
                                    $storagePath = public_path('storage/'.$p);
                                    $directPath = public_path($p);
                                    $publicCopy = public_path('cooperative_images/'.basename($p));
                                    if (file_exists($storagePath)) {
                                        $imgUrl = asset('storage/'.$p);
                                    } elseif (file_exists($directPath)) {
                                        $imgUrl = asset($p);
                                    } elseif (file_exists($publicCopy)) {
                                        $imgUrl = asset('cooperative_images/'.basename($p));
                                    }
                                }
                            @endphp

                            @if($imgUrl)
                                <div class="card-img-top" style="height:120px;background-image:url('{{ $imgUrl }}');background-size:cover;background-position:center;border-top-left-radius:.375rem;border-top-right-radius:.375rem"></div>
                            @else
                                <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height:120px;border-top-left-radius:.375rem;border-top-right-radius:.375rem">
                                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#9ca3af" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4z"></path><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path></svg>
                                </div>
                            @endif

                            <div class="card-body text-center">
                                @php
                                    $displayName = $coop->name ?? '';
                                    $displaySector = $coop->sector ?? '';
                                    $displayRegion = $coop->region ?? '';
                                @endphp
                                <h3 class="h6 mb-2"><a class="coop-name-link" href="{{ route('cooperatives.profile',$coop) }}">{{ $displayName }}</a></h3>
                                <p class="small coop-meta mb-2">{{ trim(trim($displaySector) . ( ($displaySector && $displayRegion) ? ' · ' : '') . $displayRegion) }}</p>
                                <p class="mb-0 coop-description">{{ Str::limit($coop->description,120) }}</p>
                            </div>
                        </article>
                    </div>
                @endforeach
            </div>

            <div class="mt-3">{{ $cooperatives->links() }}</div>
        </div>

        <aside class="col-lg-4 mt-3 mt-lg-0">
            @if(request()->input('per_page') == 34)
                @component('components.sidebar-list-section', [
                    'title' => 'More About Cooperative',
                    'items' => $coopResources ?? [],
                    'years' => $resourceYears ?? [],
                    'yearCounts' => $resourceYearCounts ?? [],
                    'selectedCount' => $resourceSelectedCount ?? ($resourceTotalCount ?? 0),
                    'totalCount' => $resourceTotalCount ?? 0,
                    'iconColor' => '#b91c1c',
                    'badgeGradient' => 'linear-gradient(135deg,#fee2e2,#fdd2d2)',
                    'badgeColor' => '#991b1b',
                    'actionType' => 'link',
                    'actionRoute' => '/cooperative-resources/{id}',
                    'viewAllRoute' => route('cooperative-resources.index'),
                    'viewAllText' => 'View All About Cooperative',
                    'noItemsText' => 'No resources available for the selected year.',
                ])
                @endcomponent
            @endif
        </aside>
    </div>

    <!-- Cooperative detail modal (AJAX) -->
    <div class="modal fade" id="coopModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Cooperative</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="coopModalBody">
                    <div class="text-center text-muted py-3">Loading...</div>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function(){
        // Intercept cooperative link clicks to open modal via AJAX
        document.querySelectorAll('.coop-name-link').forEach(el=>{
            el.addEventListener('click', function(ev){
                ev.preventDefault();
                const href = el.getAttribute('href');
                if(!href) return;
                // Build modal endpoint by appending /modal (normalize trailing slash)
                const modalUrl = href.replace(/\/+$/,'') + '/modal';
                const modalEl = document.getElementById('coopModal');
                const modalBody = document.getElementById('coopModalBody');
                if(!modalEl || !modalBody) { window.location = href; return; }
                // show loading state
                modalBody.innerHTML = '<div class="text-center text-muted py-3">Loading...</div>';
                fetch(modalUrl, {headers:{'X-Requested-With':'XMLHttpRequest'}})
                    .then(r=>{ if(!r.ok) throw r; return r.text(); })
                    .then(html=>{
                        modalBody.innerHTML = html;
                        try {
                            const bsModal = new bootstrap.Modal(modalEl);
                            bsModal.show();
                        } catch(e) {
                            // If bootstrap not available, fallback to redirect
                            console.warn('Bootstrap modal unavailable', e);
                        }
                    }).catch(()=>{
                        // fallback to full page navigation
                        window.location = href;
                    });
            });
        });
    });
    </script>
</div>
@endsection
