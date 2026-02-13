@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-12">
            <h4>Manage Videos</h4>
            {{-- Inline static success message removed; layout flash popup will be used. --}}

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
                <div class="card-body">
                    <h5 class="card-title">Add Video</h5>
                    <form method="POST" action="/admin/manage-videos" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Title</label>
                            <input name="title" class="form-control" required value="{{ old('title') }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description (optional)</label>
                            <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Video URL (YouTube/Vimeo or direct link)</label>
                            <input name="url" class="form-control" placeholder="https://..." value="{{ old('url') }}">
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" name="highlight_landing" id="highlight_landing" value="1" class="form-check-input" {{ old('highlight_landing') ? 'checked' : '' }}>
                            <label class="form-check-label" for="highlight_landing">Highlight on landing page</label>
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" name="highlight_enterprise" id="highlight_enterprise" value="1" class="form-check-input" {{ old('highlight_enterprise') ? 'checked' : '' }}>
                            <label class="form-check-label" for="highlight_enterprise">Highlight on Enterprise Portal</label>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Or Upload Video (mp4, webm)</label>
                            <input type="file" name="file" accept="video/*" class="form-control">
                            @if($errors->first('file'))
                                <div class="form-text text-danger">{{ $errors->first('file') }}</div>
                            @endif
                        </div>
                        <button id="video-submit" class="btn btn-danger">
                            <span id="video-submit-text">Add Video</span>
                            <span id="video-submit-spinner" class="spinner-border spinner-border-sm ms-2" role="status" aria-hidden="true" style="display:none"></span>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Highlights sections -->
            <div class="row mb-3">
                <div class="col-12 col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="card-title">Landing Page Highlights</h6>
                            @php $landing = \App\Models\Video::where('highlight_landing', true)->orderByDesc('created_at')->get(); @endphp
                            @if($landing->count())
                                <div class="list-group list-group-flush">
                                    @foreach($landing as $lv)
                                        <div class="list-group-item d-flex align-items-center justify-content-between">
                                            <div class="d-flex align-items-center gap-2">
                                                <div style="width:80px">@if($lv->file_path)<video src="{{ asset('storage/'.$lv->file_path) }}" style="max-width:80px;max-height:60px"></video>@elseif($lv->url)<div style="width:80px;height:60px;background:#f8f9fa;border:1px solid #e9ecef;display:flex;align-items:center;justify-content:center">Link</div>@else<div style="width:80px;height:60px;background:#f1f5f9;border:1px solid #e9ecef"></div>@endif</div>
                                                <div>
                                                    <div class="fw-semibold">{{ $lv->title }}</div>
                                                    <div class="small text-muted">{{ $lv->created_at->toDateString() }}</div>
                                                </div>
                                            </div>
                                            <div>
                                                <a href="/admin/manage-videos/{{ $lv->id }}/edit" class="btn btn-sm btn-outline-primary">Edit</a>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="small text-muted">No landing highlights configured.</div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-6 mt-3 mt-md-0">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="card-title">Enterprise Highlights</h6>
                            @php $enterprise = \App\Models\Video::where('highlight_enterprise', true)->orderByDesc('created_at')->get(); @endphp
                            @if($enterprise->count())
                                <div class="list-group list-group-flush">
                                    @foreach($enterprise as $ev)
                                        <div class="list-group-item d-flex align-items-center justify-content-between">
                                            <div class="d-flex align-items-center gap-2">
                                                <div style="width:80px">@if($ev->file_path)<video src="{{ asset('storage/'.$ev->file_path) }}" style="max-width:80px;max-height:60px"></video>@elseif($ev->url)<div style="width:80px;height:60px;background:#f8f9fa;border:1px solid #e9ecef;display:flex;align-items:center;justify-content:center">Link</div>@else<div style="width:80px;height:60px;background:#f1f5f9;border:1px solid #e9ecef"></div>@endif</div>
                                                <div>
                                                    <div class="fw-semibold">{{ $ev->title }}</div>
                                                    <div class="small text-muted">{{ $ev->created_at->toDateString() }}</div>
                                                </div>
                                            </div>
                                            <div>
                                                <a href="/admin/manage-videos/{{ $ev->id }}/edit" class="btn btn-sm btn-outline-primary">Edit</a>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="small text-muted">No enterprise highlights configured.</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Loading overlay -->
            <div id="upload-loading" style="display:none;position:fixed;inset:0;background:rgba(255,255,255,0.8);z-index:1050;align-items:center;justify-content:center">
                <div style="text-align:center">
                    <div class="spinner-border text-danger" role="status" style="width:3rem;height:3rem"></div>
                    <div class="mt-2">Uploading... please wait</div>
                </div>
            </div>

            <div class="list-group">
                @foreach($videos as $v)
                    <div class="list-group-item d-flex gap-3 align-items-start">
                        <div style="width:220px">
                            @if($v->file_path)
                                <video src="{{ asset('storage/'.$v->file_path) }}" controls style="max-width:220px;max-height:140px"></video>
                            @elseif($v->url)
                                <div style="height:140px;display:flex;align-items:center;justify-content:center;background:#f8f9fa;border:1px solid #e9ecef">Link</div>
                            @else
                                <div class="bg-light border" style="height:80px;display:flex;align-items:center;justify-content:center">No preview</div>
                            @endif
                        </div>
                        <div class="flex-fill">
                            <h6 class="mb-1">{{ $v->title }}
                                @if($v->highlight_landing)
                                    <span class="badge bg-success ms-2">Landing</span>
                                @endif
                                @if($v->highlight_enterprise)
                                    <span class="badge bg-info ms-1">Enterprise</span>
                                @endif
                            </h6>
                            <div class="small text-muted">{{ $v->created_at->toDateString() }}</div>
                            <p class="mb-1 small">{{ \Illuminate\Support\Str::limit(strip_tags($v->description), 160) }}</p>
                            <div class="mt-2">
                                <a href="/admin/manage-videos/{{ $v->id }}/edit" class="btn btn-sm btn-outline-primary">Edit</a>
                                <form method="POST" action="/admin/manage-videos/{{ $v->id }}/delete" style="display:inline" data-confirm="Delete this video?">
                                    @csrf
                                    <button class="btn btn-sm btn-outline-danger">Delete</button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-3">{{ $videos->links() }}</div>
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
