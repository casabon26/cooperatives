@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Manage Enterprises</h4>
        <div>
            <a href="{{ route('admin.enterprises.create') }}" class="btn btn-sm btn-primary me-2" target="_self">Create Enterprise</a>
            <a href="#" class="btn btn-sm btn-primary" onclick="history.back(); return false;">Back</a>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            @if(isset($enterprises) && $enterprises->count())
                <div class="list-group">
                        @foreach($enterprises as $e)
                        <div class="list-group-item d-flex justify-content-between align-items-start">
                            <div class="d-flex gap-3 align-items-start">
                                @if(!empty($e->image_url))
                                    <img src="{{ $e->image_url }}" alt="" style="width:84px;height:56px;object-fit:cover;border-radius:6px;">
                                @endif
                                <div>
                                    <div class="fw-semibold">{{ $e->name }}</div>
                                    <div class="small text-muted">{{ $e->category }} — {{ $e->summary }}</div>
                                </div>
                            </div>
                            <div class="text-end">
                                <a href="{{ route('admin.enterprises.edit', $e->id) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                <form method="POST" action="{{ route('admin.enterprises.destroy', $e->id) }}" style="display:inline" onsubmit="return confirm('Delete this enterprise?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">Delete</button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-3">{{ $enterprises->links() }}</div>
            @else
                <div class="alert alert-info">No enterprises created yet.</div>
            @endif
        </div>
    </div>
</div>
@endsection
