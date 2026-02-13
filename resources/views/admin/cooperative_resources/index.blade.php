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
                    <div>
                        <div class="fw-semibold">{{ $res->title }}</div>
                        @if($res->description)
                            <div class="small text-muted">{{ Str::limit($res->description,120) }}</div>
                        @endif
                    </div>
                    <div class="text-end">
                        @if($res->file_path)
                            <a href="{{ asset('storage/'.$res->file_path) }}" target="_blank" class="btn btn-sm btn-outline-secondary">Open</a>
                        @endif
                        <a href="{{ route('admin.cooperative-resources.edit', $res) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                        <form method="post" action="{{ route('admin.cooperative-resources.destroy', $res) }}" style="display:inline">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete resource?')">Delete</button></form>
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
