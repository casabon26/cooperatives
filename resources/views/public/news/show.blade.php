@extends('layouts.app')

@section('content')
  <div class="py-4">
    <div class="mb-3">
      <a href="{{ route('news.index') }}" class="btn btn-outline-danger d-inline-flex align-items-center" role="button" aria-label="Back to news" title="Back to news" target="_self">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.4" style="margin-right:8px;">
          <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
        </svg>
        Back to news
      </a>
    </div>
    <h1 class="mt-0">{{ $news->title }}</h1>
    <p class="text-muted">{{ optional($news->published_at)->toDayDateTimeString() }}</p>

    @php
      $imgUrl = null;
      if($news->image){
        $storagePath = public_path('storage/'.$news->image);
        $directPath = public_path($news->image);
        $publicNewsPath = public_path('assets/images/news/'.basename($news->image));
        if(file_exists($storagePath)){
          $imgUrl = asset('storage/'.$news->image);
        } elseif(file_exists($directPath)){
          $imgUrl = asset($news->image);
        } elseif(file_exists($publicNewsPath)){
          $imgUrl = asset('assets/images/news/'.basename($news->image));
        }
      }

      // Prefer embedded image data if available (use as data URI)
      if(!empty($news->image_data)){
        $imgUrl = 'data:'.($news->image_mime ?? 'image/jpeg').';base64,'.trim($news->image_data);
      }
    @endphp

    <div class="card news-card">
      @if($imgUrl)
        <img src="{{ $imgUrl }}" alt="" class="card-img-top">
      @endif

      <div class="card-body">
        <h1 class="mt-0">{{ $news->title }}</h1>
        <p class="text-muted mb-3">{{ optional($news->published_at)->toDayDateTimeString() }}</p>
        {!! nl2br(e($news->body)) !!}
      </div>
    </div>
  </div>
@endsection
