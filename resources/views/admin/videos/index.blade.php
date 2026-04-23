@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-12">
            <h4>Manage Videos</h4>

            {{-- flash messages for success or error after form actions --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <div class="card mb-4">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <h5 class="mb-0">Manage Videos</h5>
                    <a href="{{ url('/admin/manage-videos/create') }}" class="btn btn-primary">Add Video</a>
                </div>
            </div>

            <!-- Highlights sections (redesigned) -->
            <style>
                .highlight-card {
                    border-left: 4px solid #dc2626;
                }
                .highlight-card video,
                .highlight-card iframe {
                    width: 100%;
                    height: auto;
                    max-height: 120px;
                    display: block;
                }
                .highlight-preview {
                    flex: 0 0 220px;
                    max-width: 220px;
                }
                .highlight-card .btn {
                    min-width: 80px;
                }
            </style>
            <div class="row mb-3">
                <div class="col-12 col-md-6">
                    <h6>Landing Page Highlights</h6>
                    @php
                        $landing = \App\Models\Video::where('highlight_landing', true)->orderByDesc('created_at')->get();
                        // compute youtube_id property for preview logic
                        $landing->each(function($v){
                            $v->youtube_id = method_exists($v,'youtubeId') ? $v->youtubeId() : null;
                        });
                    @endphp

                    @if($landing->count())
                        <div class="row g-3">
                            @foreach($landing as $lv)
                                <div class="col-12">
                                    <div class="card highlight-card">
                                        <div class="row g-0 align-items-center">
                                            <div class="col-auto highlight-preview p-2">
                                                @php
                                                    $isVideoUrl = false;
                                                    if(!empty($lv->url)){
                                                        $ext = strtolower(pathinfo($lv->url, PATHINFO_EXTENSION));
                                                        if(in_array($ext,['mp4','webm','ogg'])) $isVideoUrl = true;
                                                    }
                                                @endphp

                                                @if($lv->file_path)
                                                    <video src="{{ asset('storage/'.$lv->file_path) }}" controls muted></video>
                                                @elseif($lv->url && $lv->youtube_id)
                                                    <div class="ratio ratio-16x9">
                                                        <iframe src="https://www.youtube.com/embed/{{ $lv->youtube_id }}?rel=0&amp;modestbranding=1" allowfullscreen></iframe>
                                                    </div>
                                                @elseif($isVideoUrl)
                                                    <video src="{{ $lv->url }}" controls muted></video>
                                                @elseif($lv->url)
                                                    <a href="{{ $lv->url }}" target="_blank" rel="noopener" class="d-flex align-items-center justify-content-center h-100 w-100 text-decoration-none text-muted">
                                                        Link
                                                    </a>
                                                @else
                                                    <div class="bg-light border" style="width:140px;height:100px;display:flex;align-items:center;justify-content:center">No preview</div>
                                                @endif
                                            </div>
                                            <div class="col">
                                                <div class="card-body py-2">
                                                    <h6 class="card-title mb-1">{{ $lv->title }}</h6>
                                                    <p class="card-text small text-muted mb-1">{{ $lv->created_at->toDateString() }}</p>
                                                    <div class="d-flex flex-column gap-1">
                                                        <a href="/admin/manage-videos/{{ $lv->id }}/edit" class="btn btn-sm btn-outline-primary w-100">Edit</a>
                                                        <form method="POST" action="/admin/manage-videos/{{ $lv->id }}/delete" style="display:inline" data-confirm="Remove this highlight and delete video?">
                                                            @csrf
                                                            <button class="btn btn-sm btn-outline-danger w-100">Delete</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="small text-muted">No landing highlights configured.</div>
                    @endif
                </div>

                <div class="col-12 col-md-6 mt-3 mt-md-0">
                    <h6>Enterprise Highlights</h6>
                    @php
                        $enterprise = \App\Models\Video::where('highlight_enterprise', true)->orderByDesc('created_at')->get();
                        $enterprise->each(function($v){
                            $v->youtube_id = method_exists($v,'youtubeId') ? $v->youtubeId() : null;
                        });
                    @endphp

                    @if($enterprise->count())
                        <div class="row g-3">
                            @foreach($enterprise as $ev)
                                <div class="col-12">
                                    <div class="card highlight-card">
                                        <div class="row g-0 align-items-center">
                                            <div class="col-auto highlight-preview p-2">
                                                @php
                                                    $isVideoUrl = false;
                                                    if(!empty($ev->url)){
                                                        $ext = strtolower(pathinfo($ev->url, PATHINFO_EXTENSION));
                                                        if(in_array($ext,['mp4','webm','ogg'])) $isVideoUrl = true;
                                                    }
                                                @endphp
                                                @if($ev->file_path)
                                                    <video src="{{ asset('storage/'.$ev->file_path) }}" controls muted></video>
                                                @elseif($ev->url && $ev->youtube_id)
                                                    <div class="ratio ratio-16x9">
                                                        <iframe src="https://www.youtube.com/embed/{{ $ev->youtube_id }}?rel=0&amp;modestbranding=1" allowfullscreen></iframe>
                                                    </div>
                                                @elseif($isVideoUrl)
                                                    <video src="{{ $ev->url }}" controls muted></video>
                                                @elseif($ev->url)
                                                    <a href="{{ $ev->url }}" target="_blank" rel="noopener" class="d-flex align-items-center justify-content-center h-100 w-100 text-decoration-none text-muted">
                                                        Link
                                                    </a>
                                                @else
                                                    <div class="bg-light border" style="width:140px;height:100px;display:flex;align-items:center;justify-content:center">No preview</div>
                                                @endif
                                            </div>
                                            <div class="col">
                                                <div class="card-body py-2">
                                                    <h6 class="card-title mb-1">{{ $ev->title }}</h6>
                                                    <p class="card-text small text-muted mb-1">{{ $ev->created_at->toDateString() }}</p>
                                                    <div class="d-flex flex-column gap-1">
                                                        <a href="/admin/manage-videos/{{ $ev->id }}/edit" class="btn btn-sm btn-outline-primary w-100">Edit</a>
                                                        <form method="POST" action="/admin/manage-videos/{{ $ev->id }}/delete" style="display:inline" data-confirm="Remove this highlight and delete video?">
                                                            @csrf
                                                            <button class="btn btn-sm btn-outline-danger w-100">Delete</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="small text-muted">No enterprise highlights configured.</div>
                    @endif
                </div>

            <!-- Loading overlay -->
            <div id="upload-loading" style="display:none;position:fixed;inset:0;background:rgba(255,255,255,0.8);z-index:1050;align-items:center;justify-content:center">
                <div style="text-align:center">
                    <div class="spinner-border text-danger" role="status" style="width:3rem;height:3rem"></div>
                    <div class="mt-2">Uploading... please wait</div>
                </div>
            </div>

            {{-- list of all videos removed; only highlights are managed above --}}
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <script>
        (function(){
            const form = document.querySelector('form[enctype="multipart/form-data"]');
            if (!form) return;
            const overlay = document.getElementById('upload-loading');
            const submitBtn = document.getElementById('video-submit');
            const submitText = document.getElementById('video-submit-text');
            const spinner = document.getElementById('video-submit-spinner');

            form.addEventListener('submit', function(e){
                // show loading UI
                if (overlay) overlay.style.display = 'flex';
                if (submitBtn) submitBtn.disabled = true;
                if (submitText) submitText.textContent = 'Uploading...';
                if (spinner) spinner.style.display = 'inline-block';
            });

            // Hide overlay when page has a success or error message (server redirect)
            window.addEventListener('load', function(){
                const hasFlash = document.querySelector('.alert');
                if (hasFlash && overlay) overlay.style.display = 'none';
            });
        })();
    </script>
@endsection
