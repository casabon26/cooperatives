@extends('layouts.app')

@section('content')
<div class="py-4">
    <div class="container">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">{{ $memorandum->code ?? $memorandum->title }}</h4>
                @if($memorandum->title && ($memorandum->code ?? null))
                    <div class="small text-muted mb-3">{{ $memorandum->title }}</div>
                @endif

                @if(!empty($memorandum->file_path))
                    @php
                        $publicPath = public_path($memorandum->file_path);
                        $storagePath = public_path('storage/'.$memorandum->file_path);
                        $assetUrl = null;
                        if(file_exists($publicPath)){
                            $assetUrl = asset($memorandum->file_path);
                        } elseif(file_exists($storagePath)){
                            $assetUrl = asset('storage/'.$memorandum->file_path);
                        } else {
                            if(preg_match('/^https?:\/\//', $memorandum->file_path)){
                                $assetUrl = $memorandum->file_path;
                            }
                        }
                    @endphp

                        @if($assetUrl)
                            @php
                                // Use our file route to serve the file inline when local
                                $isExternal = preg_match('/^https?:\/\//', $memorandum->file_path);
                                $viewUrl = $isExternal ? $assetUrl : route('memorandums.file', $memorandum);
                                $ext = strtolower(pathinfo($assetUrl, PATHINFO_EXTENSION));
                            @endphp

                            @php
                                $downloadUrl = $isExternal ? $assetUrl : (route('memorandums.file', $memorandum) . '?dl=1');
                            @endphp

                            @if(!$isExternal && in_array($ext, ['pdf']))
                                <div style="height:700px;">
                                    <iframe src="{{ $viewUrl }}" style="width:100%; height:100%; border:0;" loading="lazy"></iframe>
                                </div>
                                <div class="mb-3 mt-2">
                                    <a href="{{ $viewUrl }}" class="btn btn-primary">Open Document</a>
                                    <a href="{{ $downloadUrl }}" class="btn btn-outline-secondary" download>Download</a>
                                </div>
                            @else
                                <div class="mb-3">
                                    <a href="{{ $viewUrl }}" class="btn btn-primary">Open Document</a>
                                    <a href="{{ $downloadUrl }}" class="btn btn-outline-secondary" download>Download</a>
                                </div>
                            @endif
                        @else
                        <div class="small text-muted">Document file not found on server.</div>
                    @endif
                @else
                    <div class="small text-muted">No document attached for this memorandum.</div>
                @endif

                @if($memorandum->content)
                    <div class="mt-3">{!! nl2br(e($memorandum->content)) !!}</div>
                @endif

                <div class="mt-3">
                    <a href="{{ url('/') }}" class="btn btn-outline-danger d-inline-flex align-items-center" role="button" aria-label="Back to landing page" title="Back to landing page" target="_self">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.4" style="margin-right:8px;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                        </svg>
                        Back to Landing Page
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
