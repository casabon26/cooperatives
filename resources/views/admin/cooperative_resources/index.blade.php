@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0">Manage Cooperative Resources</h5>
        <a href="{{ route('admin.cooperative-resources.create') }}" class="btn btn-primary">Add Resource</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($resources->count())
        <div class="list-group">
            @foreach($resources as $res)
                <div class="list-group-item d-flex justify-content-between align-items-start">
                    <div class="me-3" style="min-width:0;flex:1 1 auto;">
                        <div class="fw-semibold">{{ $res->title }}</div>
                        @if($res->description)
                            <div class="small text-muted text-truncate d-block">{{ Str::limit($res->description,120) }}</div>
                        @endif
                    </div>
                    <div class="text-end d-flex align-items-center gap-2 flex-shrink-0">
                        @if($res->file_path)
                            @php $ext = strtolower(pathinfo($res->file_path, PATHINFO_EXTENSION)); @endphp
                            @if(in_array($ext, ['ppt','pptx']))
                                <a href="{{ route('cooperative-resources.file', $res) }}?dl=1" class="btn btn-sm btn-outline-secondary">Download</a>
                            @else
                                <a href="{{ route('cooperative-resources.file', $res) }}" target="_blank" class="btn btn-sm btn-outline-secondary">Open</a>
                            @endif
                        @if($res->gdrive_link)
                            <a href="{{ $res->gdrive_link }}" target="_blank" class="btn btn-sm btn-outline-success">Drive</a>
                        @endif
                        @endif
                        <a href="{{ route('admin.cooperative-resources.edit', $res) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                        <form method="post" action="{{ route('admin.cooperative-resources.destroy', $res) }}" style="display:inline" data-confirm="Delete resource?">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger" type="submit">Delete</button></form>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-3">{{ $resources->links() }}</div>
    @else
        <div class="small text-muted">No resources yet.</div>
    @endif
</div>

@endsection
