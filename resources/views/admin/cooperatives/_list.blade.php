<div class="list-group" id="cooperativesList">
    @forelse($cooperatives as $c)
        <div class="list-group-item d-flex gap-3 align-items-center">
            <div class="flex-fill">
                <h6 class="mb-1">{{ $c->name }} <small class="text-muted">&middot; {{ ucfirst($c->status) }}</small></h6>
                <div class="small text-muted">{{ $c->sector }} — {{ $c->region }}</div>
                <p class="mb-1 small">{{ \Illuminate\Support\Str::limit(strip_tags($c->description), 160) }}</p>
                <div class="mt-2">
                    <a href="{{ route('admin.cooperatives.edit', $c) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                    <form method="POST" action="{{ route('admin.cooperatives.destroy', $c) }}" style="display:inline" data-confirm="Delete this cooperative?">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    @empty
        <div class="list-group-item text-center text-muted">No cooperatives found.</div>
    @endforelse
</div>
