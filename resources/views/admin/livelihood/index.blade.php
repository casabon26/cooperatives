@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0">Manage SLPA Entries</h5>
        <a href="{{ route('admin.livelihood.create') }}" class="btn btn-primary">Add SLPA</a>
    </div>

    {{-- success flash is shown via the global dynamic popup in the layout; remove inline duplicate --}}

    @if($slpas->count())
        <div class="list-group">
            @foreach($slpas as $s)
                <div class="list-group-item d-flex justify-content-between align-items-start">
                    <div class="me-3" style="min-width:0;flex:1 1 auto;">
                        <div class="fw-semibold">{{ $s->name }}</div>
                        @if($s->description)
                            <div class="small text-muted text-truncate d-block">{{ Str::limit($s->description,120) }}</div>
                        @endif
                        @if($s->members_count)
                            <div class="small text-muted">Members: {{ $s->members_count }}</div>
                        @endif    
                        @if($s->business)
                            <div class="small text-muted">Business: {{ $s->business }}</div>
                        @endif
                        @if($s->address)
                            <div class="small text-muted text-truncate">{{ Str::limit($s->address,80) }}</div>
                        @endif
                    </div>
                    <div class="text-end d-flex align-items-center gap-2 flex-shrink-0">
                        @if($s->image_url)
                            <img src="{{ $s->image_url }}" alt="{{ $s->name }}" style="height:48px;object-fit:cover;border-radius:.35rem;margin-right:.5rem;">
                        @endif
                        <a href="{{ route('admin.livelihood.edit', $s) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                        <form method="post" action="{{ route('admin.livelihood.destroy', $s) }}" style="display:inline" data-confirm="Delete SLPA entry?">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger" type="submit">Delete</button></form>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-3">{{ $slpas->links() }}</div>
    @else
        <div class="small text-muted">No SLPA entries yet.</div>
    @endif
</div>

@endsection
