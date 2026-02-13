@extends('layouts.app')

@section('content')
<div class="py-4">
    <style>
        /* Match cooperative name styling to header nav link and center inside card */
        .coop-name-link {
            font-weight: 600;
            text-decoration: none;
            transition: color .12s ease;
        }
        /* Centered, navbar-red title when shown inside cards */
        .card .coop-name-link {
            color: #b91c1c;
            display: block;
            text-align: center;
        }
        .coop-name-link:hover, .coop-name-link:focus {
            color: #8f1515;
            text-decoration: none;
        }
        .coop-meta { color: #6b7280; }
        /* Ensure cooperative cards keep consistent height and layout */
        .coop-card .card-body { display: flex; flex-direction: column; }
        .coop-card .coop-description { flex: 1 1 auto; }

        /* The More About Cooperative aside card: styled to match theme */
        .no-hover-card {
            border-left: 4px solid rgba(185,28,28,0.12);
            background: linear-gradient(180deg, rgba(249,250,251,0.6), rgba(255,255,255,0.85));
        }
        .no-hover-card .card-title { color: #7f1d1d; font-weight:700; }
        .memo-item { display:flex; gap:.6rem; align-items:flex-start; }
        .memo-icon { width:28px; height:28px; display:flex; align-items:center; justify-content:center; }
        .memo-link { color: #b91c1c; font-weight:600; text-decoration:none; display:inline-block; padding:.15rem .35rem; border-radius:.35rem; transition: background .12s ease, color .12s ease; }
        .memo-link:hover { text-decoration:none; }
        .memo-item { transition: background .12s ease, transform .08s ease; display:flex; gap:.6rem; align-items:center; }
        .memo-item { padding:.5rem .5rem; }
        .memo-item:hover { background: rgba(185,28,28,0.04); transform: translateY(-2px); border-radius: .375rem; }
        /* On hover, make the link text sit on a red background to match theme */
        .memo-item:hover .memo-link { background: #ef4444; color: #fff !important; box-shadow: 0 2px 6px rgba(239,68,68,0.12); }

        /* Icon container (left) */
        .memo-icon { width:36px; height:36px; border-radius:8px; background: #fff; display:flex; align-items:center; justify-content:center; box-shadow: 0 0 0 1px rgba(0,0,0,0.02); }
        .memo-item:hover .memo-icon { background: rgba(239,68,68,0.08); }

        /* Year / code pill on the right */
        .memo-badge { background: rgba(0,0,0,0.03); color: #374151; padding: .25rem .5rem; border-radius: .4rem; font-weight:600; }
        .memo-item:hover .memo-badge { background: #ef4444; color: #ffffff; box-shadow: 0 2px 6px rgba(239,68,68,0.12); }

        /* If resource title contains a short code, visually emphasize it as pill (optional) */
        .memo-link .code-pill { display:inline-block; background:#fecaca; color:#7f1d1d; padding:.18rem .5rem; border-radius:.5rem; font-weight:700; margin-right:.5rem; }
        .memo-item:hover .memo-link .code-pill { background:#ef4444; color:#fff; }
    </style>
    <h1 class="h4">Cooperatives Directory</h1>
    <form class="row g-2 my-3" method="get" role="search" aria-label="Search cooperatives">
        <div class="col-12 col-md-8"><input name="q" value="{{ request('q') }}" class="form-control" placeholder="Search by name"></div>
    </form>

    <div class="row">
        <div class="col-lg-8">
            <div class="row row-cols-1 row-cols-md-3 g-3">
                @foreach($cooperatives as $coop)
                    <div class="col">
                        <article class="card h-100 coop-card">
                            <div class="card-body">
                                <h3 class="h6 mb-2"><a class="coop-name-link" href="{{ route('cooperatives.profile',$coop) }}">{{ $coop->name }}</a></h3>
                                <p class="small coop-meta mb-2">{{ $coop->sector }} · {{ $coop->region }}</p>
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
                <div class="card no-hover-card">
                    <div class="card-body">
                        <h5 class="card-title">More About Cooperative</h5>

                        @if(isset($resourceYears) && count($resourceYears))
                            <div class="mb-2">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <label class="small mb-0">Filter by year</label>
                                    <div class="small text-muted">Showing: <strong>{{ $resourceSelectedCount ?? ($resourceTotalCount ?? ($coopResources->count() ?? 0)) }}</strong></div>
                                </div>

                                <div class="memo-filter">
                                    <div class="dropdown">
                                        @php
                                            $selectedLabel = request('resource_year') ?: 'All years';
                                        @endphp
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="resourceYearDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                            {{ $selectedLabel }}
                                            <span class="badge bg-secondary ms-2">{{ request('resource_year') ? ($resourceYearCounts[request('resource_year')] ?? 0) : ($resourceTotalCount ?? 0) }}</span>
                                        </button>
                                        <ul class="dropdown-menu" aria-labelledby="resourceYearDropdown">
                                            <li><a class="dropdown-item d-flex justify-content-between align-items-center {{ request('resource_year') ? '' : 'active' }}" href="{{ url('/cooperatives?per_page=34') }}">All years <span class="badge bg-secondary ms-2">{{ $resourceTotalCount ?? 0 }}</span></a></li>
                                            @foreach($resourceYears as $y)
                                                <li>
                                                    <a class="dropdown-item d-flex justify-content-between align-items-center {{ request('resource_year') == $y ? 'active' : '' }}" href="{{ url('/cooperatives?per_page=34&resource_year='.$y) }}">
                                                        {{ $y }}
                                                        <span class="badge bg-secondary ms-2">{{ $resourceYearCounts[$y] ?? 0 }}</span>
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

                        @if(isset($coopResources) && $coopResources->count())
                            <ul class="list-unstyled small mb-0 memo-list">
                                @foreach($coopResources as $res)
                                    <li class="memo-item py-2 border-bottom">
                                        <div class="memo-icon flex-shrink-0" aria-hidden="true">
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M7 7h6v6H7z" stroke="#b91c1c" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/><path d="M21 15V7a2 2 0 0 0-2-2H9" stroke="#b91c1c" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                        </div>

                                        <div class="flex-grow-1">
                                            <a href="{{ route('cooperative-resources.show', $res) }}" class="memo-link d-block" target="_self" title="click to open" aria-label="{{ $res->title ?? 'Resource' }}">
                                                {{ $res->title ?? 'Resource' }}
                                            </a>
                                            @if(isset($res->created_at))
                                                <div class="memo-meta">Published: {{ optional($res->created_at)->toFormattedDateString() }}</div>
                                            @endif
                                        </div>
                                        <div class="flex-shrink-0 text-end" style="min-width:56px;">
                                            <div class="memo-badge">{{ optional($res->created_at)->format('Y') ?? '' }}</div>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <div class="small text-muted">No resources available for the selected year.</div>
                        @endif

                    </div>
                </div>
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
