@extends('layouts.app')

@section('content')
  <div class="py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h1 class="m-0">News</h1>
      <small class="text-muted">Latest announcements</small>
    </div>

    @if($news->count())
      <div class="row g-3">
        @foreach($news as $item)
          <div class="col-12 col-md-6 col-lg-4">
            <article class="card h-100">
              @php
                $imgUrl = null;
                if($item->image){
                  $storagePath = public_path('storage/'.$item->image);
                  $directPath = public_path($item->image);
                  $publicNewsPath = public_path('assets/images/news/'.basename($item->image));
                  if(file_exists($storagePath)){
                    $imgUrl = asset('storage/'.$item->image);
                  } elseif(file_exists($directPath)){
                    $imgUrl = asset($item->image);
                  } elseif(file_exists($publicNewsPath)){
                    $imgUrl = asset('assets/images/news/'.basename($item->image));
                  }
                }
              @endphp
              @if($item->image_data)
                <img src="data:{{ $item->image_mime }};base64,{{ $item->image_data }}" class="card-img-top" alt="" style="height:200px; object-fit:cover;">
              @elseif($imgUrl)
                <img src="{{ $imgUrl }}" class="card-img-top" alt="" style="height:200px; object-fit:cover;">
              @endif
              <div class="card-body d-flex flex-column">
                <div class="mb-2 small text-muted">{{ optional($item->published_at ?? $item->created_at)->toDayDateTimeString() }}</div>
                <p class="card-text text-muted mb-3">{{ Str::limit($item->summary ?? $item->body, 120) }}</p>

                <div class="mt-auto d-flex justify-content-between align-items-center pt-2 border-top">
                  <h5 class="h6 mb-0" style="font-size:1rem;">{{ $item->title }}</h5>
                  <a href="{{ route('news.show', $item) }}" class="btn btn-sm btn-primary">Read more</a>
                </div>
              </div>
            </article>
          </div>
        @endforeach
      </div>

      <div class="mt-4">
        {{ $news->links() }}
      </div>
    @else
      <div class="alert alert-info">No news items yet.</div>
    @endif
  </div>
@endsection
