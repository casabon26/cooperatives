@extends('layouts.app')

@section('content')
<div class="py-4">
    <div class="container">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">{{ $resource->title }}</h4>
                @if(!empty($viewUrl) && isset($ext) && isset($isExternal))
                    @if(!$isExternal && in_array($ext, ['pdf']))
                        <div style="height:700px;">
                            <iframe src="{{ $viewUrl }}" style="width:100%; height:100%; border:0;" loading="lazy"></iframe>
                        </div>
                        <div class="mb-3 mt-2">
                            <a href="{{ $viewUrl }}" class="btn btn-primary">Open Document</a>
                            <a href="{{ $downloadUrl ?? $viewUrl }}" class="btn btn-outline-secondary" download>Download</a>
                        </div>
                    @else
                        <div class="mb-3">
                            <a href="{{ $viewUrl }}" class="btn btn-primary">Open Document</a>
                            <a href="{{ $downloadUrl ?? $viewUrl }}" class="btn btn-outline-secondary" download>Download</a>
                        </div>
                    @endif
                @else
                    <div class="small text-muted">Document file not found on server.</div>
                @endif

                @if($resource->description)
                    <div class="mt-3">{{ nl2br(e($resource->description)) }}</div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
