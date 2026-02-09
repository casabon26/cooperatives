@extends('layouts.app')

@section('content')
  <div class="py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h1 class="m-0">Videos</h1>
      <small class="text-muted">Video resources and tutorials</small>
    </div>

    <div class="row g-3">
      <!-- Placeholder video card -->
      <div class="col-12 col-md-6 col-lg-4">
        <div class="card h-100">
          <div class="ratio ratio-16x9"> 
            <iframe src="https://www.youtube.com/embed/dQw4w9WgXcQ" title="Placeholder video" allowfullscreen></iframe>
          </div>
          <div class="card-body">
            <h5 class="card-title">Sample video</h5>
            <p class="card-text text-muted">Replace with your video links or embed codes.</p>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
