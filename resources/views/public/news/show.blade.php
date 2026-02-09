@extends('layouts.app')

@section('content')
  <div class="py-4">
    <div class="mb-3">
      <a href="{{ route('news.index') }}" class="btn btn-sm btn-primary">&larr; Back to news</a>
    </div>
    <h1 class="mt-0">{{ $news->title }}</h1>
    <p class="text-muted">{{ optional($news->published_at)->toDayDateTimeString() }}</p>

    @php
      $imgUrl = null;
      if($news->image){
        $storagePath = public_path('storage/'.$news->image);
        $directPath = public_path($news->image);
        $publicNewsPath = public_path('news_images/'.basename($news->image));
        if(file_exists($storagePath)){
          $imgUrl = asset('storage/'.$news->image);
        } elseif(file_exists($directPath)){
          $imgUrl = asset($news->image);
        } elseif(file_exists($publicNewsPath)){
          $imgUrl = asset('news_images/'.basename($news->image));
        }
      }
    @endphp
    @if($news->image_data)
      <img src="data:{{ $news->image_mime }};base64,{{ $news->image_data }}" alt="" class="img-fluid rounded mb-3">
    @elseif($imgUrl)
      <img src="{{ $imgUrl }}" alt="" class="img-fluid rounded mb-3">
    @endif

    <div class="card">
      <div class="card-body">
        {!! nl2br(e($news->body)) !!}
      </div>
    </div>
  </div>
@endsection
