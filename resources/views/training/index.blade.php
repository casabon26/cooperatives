@extends('layouts.app')

@section('content')
<div class="py-4">
    <h1>Training Videos</h1>
    <div class="row">
        @foreach($videos as $v)
            <div class="col-md-4 mb-3">
                <div class="card h-100">
                    <div class="card-body position-relative">
                        <h6>{{ $v->title }}</h6>
                        <p class="small text-muted">{{ Str::limit($v->description, 120) }}</p>

                        @auth
                            @if(in_array($v->id, $completedIds ?? []))
                                <span class="badge bg-success position-absolute" style="right:10px; top:10px;">Completed</span>
                            @endif
                        @endauth

                        <a href="{{ route('training.show', $v) }}" class="stretched-link"></a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    {{ $videos->links() }}
</div>
@endsection
