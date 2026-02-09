@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-12 col-md-8">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Admin Panel</h5>
                    <p class="small text-muted">You are signed in as <strong>{{ session('admin_email') }}</strong>.</p>

                    <p class="mb-3">Quick links for administrators:</p>

                    <div class="admin-actions" role="group" aria-label="Administrator quick actions">
                        <div class="row g-3">
                            <!-- Dashboard quick-link removed per request -->

                            <div class="col-12 col-sm-6 col-md-4">
                                <a href="{{ url('/admin/manage-news') }}" class="btn btn-outline-primary w-100 admin-action" role="button" aria-label="Manage latest updates" target="_self">
                                    <div class="action-content">
                                        <div class="action-title">Manage News</div>
                                        <div class="action-sub">Create, edit, remove items</div>
                                    </div>
                                </a>
                            </div>


                            <div class="col-12 col-sm-6 col-md-4">
                                <a href="{{ route('admin.cooperatives.view') }}" class="btn btn-outline-primary w-100 admin-action" role="button" aria-label="View cooperatives" target="_self">
                                    <div class="action-content">
                                        <div class="action-title">Cooperatives</div>
                                        <div class="action-sub">View cooperatives (read-only)</div>
                                    </div>
                                </a>
                            </div>

                            <div class="col-12 col-sm-6 col-md-4">
                                <a href="{{ route('admin.cooperatives.index') }}" class="btn btn-outline-primary w-100 admin-action" role="button" aria-label="Manage cooperatives" target="_self">
                                    <div class="action-content">
                                        <div class="action-title">Manage Cooperatives</div>
                                        <div class="action-sub">Create, edit and delete</div>
                                    </div>
                                </a>
                            </div>
                            <div class="col-12 col-sm-6 col-md-4">
                                <a href="{{ url('/') }}" class="btn btn-outline-secondary w-100 admin-action" role="button" aria-label="View public site" target="_self">
                                    <div class="action-content">
                                        <div class="action-title">View Site</div>
                                        <div class="action-sub">Open public website</div>
                                    </div>
                                </a>
                            </div>
                            <div class="col-12 col-sm-6 col-md-4">
                                <a href="{{ url('/admin/manage-videos') }}" class="btn btn-outline-primary w-100 admin-action" role="button" aria-label="Manage videos" target="_self">
                                    <div class="action-content">
                                        <div class="action-title">Manage Videos</div>
                                        <div class="action-sub">Add or remove landing videos</div>
                                    </div>
                                </a>
                            </div>
                            <div class="col-12 col-sm-6 col-md-4">
                                <a href="{{ route('admin.store_locations.index') }}" class="btn btn-outline-primary w-100 admin-action" role="button" aria-label="Manage store" target="_self">
                                    <div class="action-content">
                                        <div class="action-title">Manage Store</div>
                                        <div class="action-sub">Manage categories, items and locations</div>
                                    </div>
                                </a>
                            </div>
                            <div class="col-12 col-sm-6 col-md-4">
                                <a href="{{ route('admin.memorandums.index') }}" class="btn btn-outline-primary w-100 admin-action" role="button" aria-label="Manage memorandums" target="_self">
                                    <div class="action-content">
                                        <div class="action-title">Manage Memorandums</div>
                                        <div class="action-sub">Create, edit and remove memorandums</div>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>

                    @if(empty(env('YOUTUBE_API_KEY')))
                        <div class="mt-3 alert alert-warning">
                            <strong>YouTube API key not configured.</strong>
                            <div class="small">Add <code>YOUTUBE_API_KEY</code> to your <code>.env</code> to enable safe server-side embeddability checks. Without it, YouTube links will fall back to thumbnails and links.</div>
                        </div>
                    @endif

                    <style>
                        /* Ensure all admin quick-action buttons are uniform height and centered */
                        .admin-actions .row { align-items: stretch; }
                        .admin-action { 
                            min-height: 72px;
                            height: 100%;
                            border-radius: .5rem;
                            padding: .75rem 1rem;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            text-align: center;
                            transition: transform .12s ease, box-shadow .12s ease;
                            box-sizing: border-box;
                        }
                        .admin-action .action-content { display:flex; flex-direction:column; align-items:center; justify-content:center; width:100%; }
                        .admin-action .action-title { font-weight: 600; }
                        .admin-action .action-sub { font-size: .85rem; color: #6c757d; }
                        .admin-action:focus .action-sub,
                        .admin-action:focus-visible .action-sub,
                        .admin-action:hover .action-sub {
                            color: #ffffff !important;
                        }
                        .admin-action:focus, .admin-action:focus-visible { 
                            outline: none;
                            box-shadow: 0 0 0 .2rem rgba(13,110,253,.25);
                            transform: translateY(-1px);
                        }
                        .admin-action:hover { transform: translateY(-2px); }
                        /* Use site danger (red) color for admin quick links instead of default blue */
                        .admin-action.btn-outline-primary {
                            color: var(--danger) !important;
                            border-color: rgba(239,68,68,0.65) !important;
                            background-color: transparent !important;
                            box-shadow: inset 0 0 0 1px rgba(0,0,0,0.02);
                        }
                        .admin-action.btn-outline-primary .action-sub { color: rgba(0,0,0,0.55); }
                        .admin-action.btn-outline-primary:hover,
                        .admin-action.btn-outline-primary:focus,
                        .admin-action.btn-outline-primary:focus-visible {
                            background-color: var(--danger) !important;
                            color: #fff !important;
                            border-color: rgba(239,68,68,1) !important;
                            box-shadow: 0 0 0 .08rem rgba(239,68,68,0.18);
                        }
                        @media (max-width: 575.98px) {
                            .admin-action { min-height: 64px; }
                        }
                    </style>
                   
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
