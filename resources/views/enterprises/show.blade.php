@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-12 col-md-10">
            <div class="card">
                <div class="card-body">
                    <h3 class="mb-3">{{ $enterprise->name }}</h3>
                    <div class="mb-3 small text-muted">Category: {{ $enterprise->category }}</div>
                    @if(!empty($enterprise->image_url))
                        <div class="mb-3"><img src="{{ $enterprise->image_url }}" alt="{{ $enterprise->name }}" style="max-width:100%; border-radius:8px;"></div>
                    @endif
                    @if($enterprise->summary)
                        <p class="lead">{{ $enterprise->summary }}</p>
                    @endif
                    @if($enterprise->description)
                        <div class="mt-3">{!! nl2br(e($enterprise->description)) !!}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
