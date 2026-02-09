@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Manage Cooperatives</h4>
        <div>
            <a href="{{ route('admin.cooperatives.view') }}" class="btn btn-sm btn-primary me-2" target="_self">View Cooperatives</a>
            <a href="{{ route('admin.cooperatives.create') }}" class="btn btn-sm btn-primary me-2" target="_self">Create Cooperative</a>
            <a href="#" class="btn btn-sm btn-primary" onclick="history.back(); return false;">Back</a>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="list-group">
                @foreach($cooperatives as $c)
                    <div class="list-group-item d-flex gap-3 align-items-center">
                        <div class="flex-fill">
                            <h6 class="mb-1">{{ $c->name }} <small class="text-muted">&middot; {{ ucfirst($c->status) }}</small></h6>
                            <div class="small text-muted">{{ $c->sector }} — {{ $c->region }}</div>
                            <p class="mb-1 small">{{ \Illuminate\Support\Str::limit(strip_tags($c->description), 160) }}</p>
                            <div class="mt-2">
                                <a href="/admin/cooperatives/{{ $c->id }}/edit" class="btn btn-sm btn-outline-primary">Edit</a>
                                <form method="POST" action="/admin/cooperatives/{{ $c->id }}" style="display:inline" onsubmit="return confirm('Delete this cooperative?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">Delete</button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
