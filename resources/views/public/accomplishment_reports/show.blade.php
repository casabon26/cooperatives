@extends('layouts.app')

@section('content')
<div class="py-4">
    <div class="container">
        <div class="mb-3 d-flex justify-content-between align-items-center">
            <h3 class="mb-0">{{ $report->title }}</h3>
            <div>
                <a href="{{ $downloadUrl ?? $viewUrl }}" class="btn btn-outline-secondary">Download</a>
                <a href="/accomplishment-reports" class="btn btn-outline-primary">Back</a>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                @if(!empty($viewUrl) && in_array($ext,['pdf']) && !$isExternal)
                    <div style="height:700px;">
                        <iframe src="{{ $viewUrl }}" style="width:100%; height:100%; border:0;" loading="lazy"></iframe>
                    </div>
                @else
                    @if(in_array($ext,['pdf']) && $isExternal)
                        <p class="small text-muted">This file is hosted externally. Use Download to open.</p>
                    @elseif(in_array($ext,['ppt','pptx']))
                        <div class="alert alert-warning small">This is a PowerPoint ({{ strtoupper($ext) }}). Preview unavailable — please download.</div>
                    @else
                        <p class="small text-muted">Preview unavailable. Use Download to get the file.</p>
                    @endif
                @endif

                @if($report->description)
                    <div class="mt-3">{{ nl2br(e($report->description)) }}</div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
