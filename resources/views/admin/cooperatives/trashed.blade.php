@extends('layouts.app')

@section('back-button')
    @include('partials.back-button', ['url' => route('admin.cooperatives.index'), 'label' => 'Back to Manage', 'class' => 'btn-sm'])
@endsection

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Recently Deleted Cooperatives</h4>
    </div>

    <div class="row">
        <div class="col-12">
            @if($cooperatives->isEmpty())
                <div class="alert alert-secondary">No recently deleted cooperatives.</div>
            @else
                <div class="list-group">
                    @foreach($cooperatives as $c)
                        <div class="list-group-item d-flex gap-3 align-items-center">
                            <div class="flex-fill">
                                <h6 class="mb-1">{{ $c->name }} <small class="text-muted">&middot; deleted {{ $c->deleted_at ? $c->deleted_at->diffForHumans() : '' }}</small></h6>
                                <div class="small text-muted">{{ $c->sector }} — {{ $c->region }}</div>
                                <p class="mb-1 small">{{ \Illuminate\Support\Str::limit(strip_tags($c->description), 160) }}</p>
                                <div class="mt-2">
                                    <form method="POST" action="{{ route('admin.cooperatives.restore', $c->id) }}" style="display:inline">
                                        @csrf
                                        <button class="btn btn-sm btn-outline-success">Restore</button>
                                    </form>

                                    <form method="POST" action="{{ route('admin.cooperatives.force_delete', $c->id) }}" style="display:inline" data-confirm="Permanently delete this cooperative? This cannot be undone.">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger">Delete Permanently</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
