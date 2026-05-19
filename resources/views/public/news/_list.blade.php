@foreach($news as $item)
  <div class="col-12 col-md-6 col-lg-4">
    <article class="card news-card h-100">
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
        <div class="news-figure" data-bg="data:{{ $item->image_mime }};base64,{{ $item->image_data }}"></div>
      @elseif($imgUrl)
        <div class="news-figure" data-bg="{{ $imgUrl }}"></div>
      @else
        <div class="news-figure placeholder"></div>
      @endif

      <div class="card-body d-flex flex-column">
        <div class="meta small text-muted mb-2">{{ optional($item->published_at ?? $item->created_at)->toDayDateTimeString() }}</div>
        <h3 class="news-title">{{ $item->title }}</h3>
        <p class="card-text text-muted mb-3">{{ Str::limit($item->summary ?? $item->description ?? $item->content, 110) }}</p>

        <div class="mt-auto d-flex justify-content-between align-items-center pt-2 border-top">
          <a href="{{ route('news.show', $item) }}" class="btn btn-readmore">Read more <i class="bi bi-arrow-right ms-2"></i></a>
        </div>
      </div>
    </article>
  </div>
@endforeach
