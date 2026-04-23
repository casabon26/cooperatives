@extends('layouts.app')

@section('content')
  <div class="py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h1 class="m-0">Videos</h1>
      <small class="text-muted">Video resources</small>
    </div>

    @if($videos->count())
      <div class="row g-3">
        @foreach($videos as $video)
          <div class="col-12 col-md-6 col-lg-4">
            <div class="card h-100">
              <div class="ratio ratio-16x9">
                @if($video->youtubeId())
                  <iframe src="https://www.youtube.com/embed/{{ $video->youtubeId() }}" title="{{ $video->title }}" allowfullscreen></iframe>
                @else
                  <div class="d-flex align-items-center justify-content-center p-3">No preview</div>
                @endif
              </div>
              <div class="card-body d-flex flex-column">
                <h5 class="card-title">{{ $video->title }}</h5>
                <div class="small text-muted mb-2">{{ optional($video->created_at)->toDayDateTimeString() }}</div>
                <p class="card-text text-muted flex-grow-1">{{ Str::limit($video->description, 100) }}</p>
                <div class="btn-group" style="width: fit-content;">
                  <a href="{{ route('videos.show', $video) }}" class="btn btn-sm btn-primary" target="_self">Details</a>
                  <button type="button" class="btn btn-sm btn-primary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
                    <span class="visually-hidden">Toggle</span>
                  </button>
                  <ul class="dropdown-menu dropdown-menu-end">
                    @if($video->youtubeId())
                      <li><a class="dropdown-item" href="https://www.youtube.com/watch?v={{ $video->youtubeId() }}" target="_blank" rel="noopener noreferrer">Open in YouTube</a></li>
                      <li><a class="dropdown-item" href="{{ route('videos.show', $video) }}" target="_self">Open in site</a></li>
                    @elseif($video->file_path)
                      @php
                        $fileUrl = asset('storage/'.$video->file_path);
                      @endphp
                      <li><a class="dropdown-item" href="{{ $fileUrl }}" target="_blank" rel="noopener noreferrer">Open file</a></li>
                      <li><a class="dropdown-item" href="{{ route('videos.show', $video) }}" target="_self">Details</a></li>
                    @else
                      <li><a class="dropdown-item" href="{{ route('videos.show', $video) }}">Open</a></li>
                    @endif
                  </ul>
                </div>
              </div>
            </div>
          </div>
        @endforeach
      </div>

      <div class="mt-4">
        {{ $videos->links() }}
      </div>
    @else
      <div class="alert alert-info">No videos yet.</div>
    @endif
  </div>
@endsection
