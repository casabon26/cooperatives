@extends('layouts.app')

@section('content')
  <div class="py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h1 class="m-0">News</h1>
      <small class="text-muted">Latest announcements and updates</small>
    </div>

    <div class="row g-3">
      <!-- Example card to be replaced with dynamic content -->
      <div class="col-12 col-md-6 col-lg-4">
        <article class="card h-100">
          <img src="/build/assets/placeholder-news.jpg" class="card-img-top" alt="" onerror="this.remove()">
          <div class="card-body">
            <h5 class="card-title">Sample news title</h5>
            <p class="card-text text-muted">Short summary. Replace with real news items when available.</p>
            <a href="#" class="stretched-link">Read more</a>
          </div>
        </article>
      </div>
    </div>
  </div>
@endsection
