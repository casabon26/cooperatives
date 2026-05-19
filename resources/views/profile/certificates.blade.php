@extends('layouts.app')

@section('hero')
    <div class="py-4 text-center bg-white mb-4">
        <div class="container">
            <h1 class="h3">My Certificates</h1>
            <p class="text-muted">Certificates you've earned from completed trainings</p>
        </div>
    </div>
@endsection

@section('content')
<div class="row">
    <div class="col-md-10">
        @if($certs->count())
            <div class="row g-3">
                @foreach($certs as $c)
                    <div class="col-md-4">
                        <div class="card p-3 h-100">
                            <h6 class="mb-1">{{ $c->video->title ?? 'Training' }}</h6>
                            <div class="small text-muted">Completed: {{ $c->completed_at ? $c->completed_at->toDayDateTimeString() : '-' }}</div>
                            <div class="mt-3 d-flex gap-2">
                                @if(!empty($c->certificate_token) && !empty($c->video))
                                    <a href="{{ route('training.certificate', $c->video) }}" class="btn btn-sm btn-primary">View certificate</a>
                                @endif
                                <div class="flex-grow-1"></div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="card p-4">
                <div class="small text-muted">You have no certificates yet.</div>
            </div>
        @endif
    </div>
</div>
@endsection
