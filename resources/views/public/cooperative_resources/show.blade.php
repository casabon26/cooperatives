@extends('layouts.app')

@section('content')
<style>
    /* Disable card hover effects on document preview pages */
    .card:hover {
        transform: none !important;
        box-shadow: 0 12px 32px rgba(var(--primary-r), 0.06) !important;
    }
    .card {
        transition: none !important;
    }
</style>
<div class="py-4">
    <div class="container">
        <div class="mb-3">
            @include('partials.back-button', ['url' => '/cooperatives?per_page=34', 'label' => 'Back'])
        </div>
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">{{ $resource->title }}</h4>
                @if(!empty($viewUrl))
                    @if(!$isExternal && in_array($ext, ['pdf']))
                        <div style="height:700px;">
                            <iframe src="{{ $viewUrl }}" style="width:100%; height:100%; border:0;" loading="lazy"></iframe>
                        </div>
                        <div class="mb-3 mt-2">
                            <a href="{{ $downloadUrl ?? $viewUrl }}" class="btn btn-outline-secondary" download>Download</a>
                        </div>
                        @if(!empty($gdriveLink))
                            <div class="mb-2">
                                <a href="{{ $gdriveLink }}" target="_blank" class="btn btn-sm btn-outline-primary">Open in Drive</a>
                            </div>
                        @endif
                    @else
                        @php $isPpt = in_array($ext, ['ppt','pptx']); @endphp
                        @if($isPpt)
                            <div class="alert alert-warning small">
                                This file is a PowerPoint presentation ({{ strtoupper($ext) }}). In-browser preview is not available — please download the file to open it on your computer.
                            </div>
                        @endif
                        <div class="mb-3">
                            <a href="{{ $downloadUrl ?? $viewUrl }}" class="btn btn-outline-secondary" download>Download</a>
                        </div>
                        @if(!empty($gdriveLink))
                            <div class="mb-2">
                                <a href="{{ $gdriveLink }}" target="_blank" class="btn btn-sm btn-outline-primary">Open in Drive</a>
                            </div>
                        @endif
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
