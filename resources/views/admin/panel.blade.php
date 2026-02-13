@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-12 col-md-10">
            <!-- Admin Header with Logo -->
            <div class="card mb-4 bg-light border-0">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        @if(file_exists(public_path('assets/images/logo/CCLDO.png')))
                            <img src="{{ asset('assets/images/logo/CCLDO.png') }}" alt="CCLDO logo" style="height:60px;object-fit:contain;">
                        @endif
                        <div>
                            <h2 class="mb-0">Admin Dashboard</h2>
                            <p class="text-muted mb-0">Logged in as <strong>{{ session('admin_email') }}</strong></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Content Management Section -->
            <div class="mb-5">
                <h5 class="text-uppercase fw-bold text-muted mb-3">
                    <svg width="18" height="18" fill="currentColor" viewBox="0 0 16 16" style="margin-right: 8px; vertical-align: -2px;">
                        <path d="M2.5 1A1.5 1.5 0 0 0 1 2.5v11A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-11A1.5 1.5 0 0 0 13.5 1h-11zM2 2.5a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 .5.5v11a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11z"/>
                        <path d="M4 5.5a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5zm0 3a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5zm0 3a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5z"/>
                    </svg>
                    Content Management
                </h5>
                <div class="admin-actions" role="group" aria-label="Content management actions">
                    <div class="row g-3">

                        <div class="col-12 col-sm-6 col-md-4">
                            <a href="{{ url('/admin/manage-news') }}" 
                               class="btn btn-outline-primary w-100 admin-action" 
                               role="button" 
                               aria-label="Manage news" 
                               target="_self">
                                <div class="action-content">
                                    <div class="action-icon">
                                        <svg width="24" height="24" fill="currentColor" viewBox="0 0 16 16">
                                            <path d="M3 0a3 3 0 0 0-3 3v10a3 3 0 0 0 3 3h10a3 3 0 0 0 3-3V3a3 3 0 0 0-3-3H3zm10 5L8.5 8.5l-2-2L2 13h12V5z"/>
                                        </svg>
                                    </div>
                                    <div class="action-title">Manage News</div>
                                    <div class="action-sub">Create, edit, remove items</div>
                                </div>
                            </a>
                        </div>

                        <div class="col-12 col-sm-6 col-md-4">
                            <a href="{{ url('/admin/manage-videos') }}" 
                               class="btn btn-outline-primary w-100 admin-action" 
                               role="button" 
                               aria-label="Manage videos" 
                               target="_self">
                                <div class="action-content">
                                    <div class="action-icon">
                                        <svg width="24" height="24" fill="currentColor" viewBox="0 0 16 16">
                                            <path d="M0 5a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v6a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V5zm6.354 5L2 11V5h12v6l-6.354-1z"/>
                                        </svg>
                                    </div>
                                    <div class="action-title">Manage Videos</div>
                                    <div class="action-sub">Add or remove landing videos</div>
                                </div>
                            </a>
                        </div>

                        <div class="col-12 col-sm-6 col-md-4">
                            <a href="{{ route('admin.memorandums.index') }}" 
                               class="btn btn-outline-primary w-100 admin-action" 
                               role="button" 
                               aria-label="Manage memorandums" 
                               target="_self">
                                <div class="action-content">
                                    <div class="action-icon">
                                        <svg width="24" height="24" fill="currentColor" viewBox="0 0 16 16">
                                            <path d="M4 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H4zm0 1h8a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1z"/>
                                            <path d="M6 3a.5.5 0 0 0 0 1h4a.5.5 0 0 0 0-1H6zm0 2a.5.5 0 0 0 0 1h4a.5.5 0 0 0 0-1H6zm0 2a.5.5 0 0 0 0 1h2a.5.5 0 0 0 0-1H6z"/>
                                        </svg>
                                    </div>
                                    <div class="action-title">Manage Memorandums</div>
                                    <div class="action-sub">Create, edit and remove</div>
                                </div>
                            </a>
                        </div>

                        <!-- New separate card for Accomplishment Reports -->
                        <div class="col-12 col-sm-6 col-md-4">
                            <a href="{{ route('admin.accomplishment-reports.index') }}" 
                               class="btn btn-outline-primary w-100 admin-action" 
                               role="button" 
                               aria-label="Manage accomplishment reports" 
                               target="_self">
                                <div class="action-content">
                                    <div class="action-icon">
                                        <svg width="24" height="24" fill="currentColor" viewBox="0 0 16 16">
                                            <path d="M9.5 13a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0z"/>
                                            <path d="M14 14V4.5L9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2zM9.5 1A1.5 1.5 0 0 0 8 2.5h5V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h5.5z"/>
                                        </svg>
                                    </div>
                                    <div class="action-title">Accomplishment Reports</div>
                                    <div class="action-sub">Create, edit and remove reports</div>
                                </div>
                            </a>
                        </div>

                    </div>
                </div>
            </div>

            <!-- Organization Management Section -->
            <div class="mb-5">
                <h5 class="text-uppercase fw-bold text-muted mb-3">
                    <svg width="18" height="18" fill="currentColor" viewBox="0 0 16 16" style="margin-right: 8px; vertical-align: -2px;">
                        <path d="M15 14s1 0 1-1V4s0-1-1-1H1s-1 0-1 1v9s0 1 1 1h14zM0 4a1 1 0 0 1 1-1h14a1 1 0 0 1 1 1v9a1 1 0 0 1-1 1H1a1 1 0 0 1-1-1V4zm5.5.5a.5.5 0 0 0-1 0 .5.5 0 0 0 1 0zm0 2a.5.5 0 0 0-1 0 .5.5 0 0 0 1 0zm0 2a.5.5 0 0 0-1 0 .5.5 0 0 0 1 0zm0 2a.5.5 0 0 0-1 0 .5.5 0 0 0 1 0zm4-4.5a.5.5 0 0 0-1 0 .5.5 0 0 0 1 0z"/>
                    </svg>
                    Organization Management
                </h5>
                <div class="admin-actions" role="group" aria-label="Organization management actions">
                    <div class="row g-3">
                        <div class="col-12 col-sm-6 col-md-4">
                            <a href="{{ route('admin.cooperatives.index') }}" class="btn btn-outline-primary w-100 admin-action" role="button" aria-label="Manage cooperatives" target="_self">
                                <div class="action-content">
                                    <div class="action-icon">
                                        <svg width="24" height="24" fill="currentColor" viewBox="0 0 16 16">
                                            <path d="M7 14s-1 0-1-1 1-4 5-4 5 3 5 4-1 1-1 1H7zm4-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/>
                                            <path fill-rule="evenodd" d="M5.216 14A2.238 2.238 0 0 1 5 13c0-1.355.68-2.75 1.936-3.72A6.325 6.325 0 0 0 5 9c-4 0-5 3-5 4s1 1 1 1h4.216z"/>
                                            <path d="M4 5a2 2 0 1 0 0-4 2 2 0 0 0 0 4z"/>
                                        </svg>
                                    </div>
                                    <div class="action-title">Manage Cooperatives</div>
                                    <div class="action-sub">Create, edit and delete</div>
                                </div>
                            </a>
                        </div>

                        <div class="col-12 col-sm-6 col-md-4">
                            <a href="{{ route('admin.enterprises.index') }}" class="btn btn-outline-primary w-100 admin-action" role="button" aria-label="Manage enterprises" target="_self">
                                <div class="action-content">
                                    <div class="action-icon">
                                        <svg width="24" height="24" fill="currentColor" viewBox="0 0 16 16">
                                            <path d="M4 5a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5H4zm3 0a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5H7zM4 9a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5H4zm3 0a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5H7z"/>
                                            <path d="M2 2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v13.5a.5.5 0 0 1-.777.416L8 13.71l-5.223 2.206A.5.5 0 0 1 2 15.5V2zm2-1a1 1 0 0 0-1 1v12.566l4.723-2.482a.5.5 0 0 1 .554 0L13 14.566V2a1 1 0 0 0-1-1H4z"/>
                                        </svg>
                                    </div>
                                    <div class="action-title">Manage Enterprises</div>
                                    <div class="action-sub">Create, edit and delete</div>
                                </div>
                            </a>
                        </div>

                        <div class="col-12 col-sm-6 col-md-4">
                            <a href="{{ url('/admin/manage-cooperative-resources') }}" class="btn btn-outline-primary w-100 admin-action" role="button" aria-label="Manage cooperative resources" target="_self">
                                <div class="action-content">
                                    <div class="action-icon">
                                        <svg width="24" height="24" fill="currentColor" viewBox="0 0 16 16">
                                            <path d="M5.5 7a.5.5 0 0 0 0 1h5a.5.5 0 0 0 0-1h-5zM5 9.5a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5zm0 2a.5.5 0 0 1 .5-.5h2a.5.5 0 0 1 0 1h-2a.5.5 0 0 1-.5-.5z"/>
                                            <path d="M9.5 1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-3a.5.5 0 0 1-.5-.5v-1a.5.5 0 0 1 .5-.5h3zm-3-1A1.5 1.5 0 0 0 5 1.5v1A1.5 1.5 0 0 0 6.5 4h3A1.5 1.5 0 0 0 11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3z"/>
                                            <path d="M0 12a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1V4.059H0V12z"/>
                                        </svg>
                                    </div>
                                    <div class="action-title">Cooperative Resources</div>
                                    <div class="action-sub">Upload and manage PDFs/PPTs</div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Store Management Section -->
            <div class="mb-5">
                <h5 class="text-uppercase fw-bold text-muted mb-3">
                    <svg width="18" height="18" fill="currentColor" viewBox="0 0 16 16" style="margin-right: 8px; vertical-align: -2px;">
                        <path d="M9.465 10H7.514L7 1H9l-.535 9z"/>
                        <path d="M6 12a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0zm7 0a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0z"/>
                        <path d="M1 3a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1H1zm7 8a.5.5 0 1 0-1 0 .5.5 0 0 0 1 0z"/>
                    </svg>
                    Store Management
                </h5>
                <div class="admin-actions" role="group" aria-label="Store management actions">
                    <div class="row g-3">
                        <div class="col-12 col-sm-6 col-md-4">
                            <a href="{{ route('admin.store_locations.index') }}" class="btn btn-outline-primary w-100 admin-action" role="button" aria-label="Manage store" target="_self">
                                <div class="action-content">
                                    <div class="action-icon">
                                        <svg width="24" height="24" fill="currentColor" viewBox="0 0 16 16">
                                            <path d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2zm3 6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2v-5a2 2 0 0 0-2-2z"/>
                                        </svg>
                                    </div>
                                    <div class="action-title">Manage Store</div>
                                    <div class="action-sub">Categories, items & locations</div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            @if(empty(env('YOUTUBE_API_KEY')))
                <div class="alert alert-warning">
                    <strong>YouTube API key not configured.</strong>
                    <div class="small">Add <code>YOUTUBE_API_KEY</code> to your <code>.env</code> to enable safe server-side embeddability checks. Without it, YouTube links will fall back to thumbnails and links.</div>
                </div>
            @endif

            <style>
                /* Admin panel styling */
                .admin-actions { margin-bottom: 2rem; }
                .admin-actions .row { align-items: stretch; }
                .admin-action { 
                    min-height: 140px;
                    height: 100%;
                    border-radius: .5rem;
                    padding: 1rem;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    text-align: center;
                    transition: all .12s ease;
                    box-sizing: border-box;
                    text-decoration: none;
                    word-wrap: break-word;
                    overflow-wrap: break-word;
                }
                .admin-action .action-content { 
                    display: flex; 
                    flex-direction: column; 
                    align-items: center; 
                    justify-content: center; 
                    width: 100%;
                    gap: 0.6rem;
                    min-height: 100%;
                }
                .admin-action .action-icon { 
                    color: var(--danger); 
                    display: flex; 
                    align-items: center; 
                    justify-content: center; 
                    flex-shrink: 0;
                    transition: color .12s ease;
                    height: 28px;
                    width: 28px;
                    margin: 0 !important;
                }
                .admin-action .action-title { 
                    font-weight: 700; 
                    font-size: 0.85rem;
                    line-height: 1.3;
                    word-break: break-word;
                }
                .admin-action .action-sub { 
                    font-size: .7rem; 
                    color: #6c757d;
                    line-height: 1.25;
                    word-break: break-word;
                }
                .admin-action:hover .action-icon {
                    color: #ffffff !important;
                }
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
                /* Use site danger (red) color for admin quick links */
                .admin-action.btn-outline-primary {
                    color: var(--danger) !important;
                    border-color: rgba(239,68,68,0.65) !important;
                    background-color: transparent !important;
                    box-shadow: inset 0 0 0 1px rgba(0,0,0,0.02);
                }
                .admin-action.btn-outline-primary .action-sub { color: rgba(0,0,0,0.55); }
                .admin-action.btn-outline-primary .action-title { color: var(--danger) !important; }
                .admin-action.btn-outline-primary:hover,
                .admin-action.btn-outline-primary:focus,
                .admin-action.btn-outline-primary:focus-visible {
                    background-color: var(--danger) !important;
                    color: #fff !important;
                    border-color: rgba(239,68,68,1) !important;
                    box-shadow: 0 0 0 .08rem rgba(239,68,68,0.18);
                }
                .admin-action.btn-outline-primary:hover .action-title,
                .admin-action.btn-outline-primary:focus .action-title,
                .admin-action.btn-outline-primary:focus-visible .action-title {
                    color: #ffffff !important;
                }
                @media (max-width: 767.98px) {
                    .admin-action { min-height: 130px; }
                }
                @media (max-width: 575.98px) {
                    .admin-action { min-height: 120px; }
                    .admin-action .action-title { font-size: 0.8rem; }
                    .admin-action .action-sub { font-size: .65rem; }
                }
            </style>
        </div>
    </div>
</div>
@endsection