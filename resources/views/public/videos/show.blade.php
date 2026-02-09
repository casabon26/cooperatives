@extends('layouts.app')

@section('content')
  <div class="py-4">
    <a href="{{ route('videos.index') }}" class="text-decoration-none">&larr; Back to videos</a>
    <h1 class="mt-3">{{ $video->title }}</h1>
    <p class="text-muted">{{ optional($video->created_at)->toDayDateTimeString() }}</p>

    <div class="ratio ratio-16x9 mb-3">
      @if($video->youtube_id)
        <iframe src="https://www.youtube.com/embed/{{ $video->youtube_id }}" title="{{ $video->title }}" allowfullscreen></iframe>
      @else
        <div class="d-flex align-items-center justify-content-center">No preview available</div>
      @endif
    </div>

    <div class="card">
      <div class="card-body">
        {!! nl2br(e($video->description)) !!}
      </div>
    </div>
  </div>
@endsection
