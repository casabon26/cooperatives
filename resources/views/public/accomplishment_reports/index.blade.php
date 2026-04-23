@extends('layouts.app')

@section('content')
<div class="py-4">
    <div class="container">
        <div class="mb-3">
            <h3>Accomplishment Reports</h3>
            <p class="small text-muted">Recent accomplishment reports — download to view.</p>
        </div>

        @if($reports->count())
            <div class="row g-3">
                @foreach($reports as $r)
                    @php $ext = strtolower(pathinfo($r->file_path ?? '', PATHINFO_EXTENSION)); @endphp
                    <div class="col-12">
                        <div class="card shadow-sm">
                            <div class="card-body d-flex justify-content-between align-items-center">
                                <div style="min-width:0;flex:1 1 auto;">
                                    <div class="fw-semibold">{{ $r->title }}</div>
                                    @if($r->description)
                                        <div class="small text-muted text-truncate">{{ Str::limit($r->description,140) }}</div>
                                    @endif
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    @if(in_array($ext,['pdf']))
                                        <a href="{{ route('accomplishment-reports.show', $r) }}" class="btn btn-sm btn-outline-secondary">Preview</a>
                                        <a href="{{ route('accomplishment-reports.file', $r) }}?dl=1" class="btn btn-sm btn-primary">Download</a>
                                    @elseif(in_array($ext,['ppt','pptx']))
                                        <span class="badge bg-secondary">PPT</span>
                                        <a href="{{ route('accomplishment-reports.file', $r) }}?dl=1" class="btn btn-sm btn-primary">Download</a>
                                    @else
                                        <a href="{{ route('accomplishment-reports.file', $r) }}?dl=1" class="btn btn-sm btn-primary">Download</a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-3">{{ $reports->links() }}</div>
        @else
            <div class="small text-muted">No reports found.</div>
        @endif
    </div>
</div>
@endsection
