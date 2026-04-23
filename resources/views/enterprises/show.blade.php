@extends('layouts.app')

@section('content')
<div class="py-5">
    <div class="row justify-content-center">
        <div class="col-12 col-md-10">
            <div class="mb-3">
                <a href="{{ url()->previous() }}" class="btn btn-outline-danger d-inline-flex align-items-center" role="button" aria-label="Back" title="Back" target="_self">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.4" style="margin-right:8px;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                    </svg>
                    Back
                </a>
            </div>
            <div class="card">
                <div class="card-body">
                    <h3 class="mb-3">{{ $enterprise->name }}</h3>
                    <div class="mb-2 small text-muted">SIZE: {{ $enterprise->category }} · Industry: {{ $enterprise->industry ?? '—' }}</div>
                    @if(!empty($enterprise->address))
                        <div class="mb-2">Address: {{ $enterprise->address }}</div>
                    @endif
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
