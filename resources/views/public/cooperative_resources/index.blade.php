@extends('layouts.app')

@section('content')
<div class="py-4">
    <div class="container">
        <div class="mb-3">
            <h3>Cooperative Resources</h3>
            <p class="small text-muted">Files and resources related to cooperatives. Presentations are download-only.</p>
        </div>

        @if($resources->count())
            <div class="row g-3">
                @foreach($resources as $res)
                    @php
                        $pathForExt = $res->file_path ?? '';
                        if (empty($pathForExt) && !empty($res->gdrive_link)) {
                            $pathForExt = $res->gdrive_link;
                        }
                        $ext = strtolower(pathinfo($pathForExt, PATHINFO_EXTENSION));
                    @endphp
                    <div class="col-12">
                        <div class="card shadow-sm">
                            <div class="card-body d-flex justify-content-between align-items-center">
                                <div style="min-width:0;flex:1 1 auto;">
                                    <div class="fw-semibold text-danger">{{ $res->title }}</div>
                                    @if($res->description)
                                        <div class="small text-muted text-truncate">{{ Str::limit($res->description,140) }}</div>
                                    @endif
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    @if(!empty($res->gdrive_link))
                                        <a href="{{ $res->gdrive_link }}" target="_blank" class="btn btn-sm btn-outline-secondary">Open in Drive</a>
                                    @elseif(in_array($ext,['pdf']))
                                        <a href="{{ route('cooperative-resources.show', $res) }}" class="btn btn-sm btn-outline-secondary">Preview</a>
                                        <a href="{{ route('cooperative-resources.file', $res) }}?dl=1" class="btn btn-sm btn-primary">Download</a>
                                    @elseif(in_array($ext,['ppt','pptx']))
                                        <span class="badge bg-secondary">PPT</span>
                                        <a href="{{ route('cooperative-resources.file', $res) }}?dl=1" class="btn btn-sm btn-primary">Download</a>
                                    @else
                                        <a href="{{ route('cooperative-resources.file', $res) }}" target="_blank" class="btn btn-sm btn-primary">Open</a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-3">{{ $resources->links() }}</div>
        @else
            <div class="small text-muted">No resources found.</div>
        @endif
    </div>
</div>
@endsection
