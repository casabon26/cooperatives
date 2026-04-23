@extends('layouts.app')

@section('hero')
<div class="carousel-wrapper">
  <div id="heroCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="3000">
    <div class="carousel-inner">
      <div class="carousel-item active">
        <img src="{{ asset('assets/images/logo/hero-bg.jpg') }}" class="d-block w-100" alt="">
      </div>
      <div class="carousel-item">
        <img src="{{ asset('assets/images/logo/hero-bg2.jpg') }}" class="d-block w-100" alt="">
      </div>
      <div class="carousel-item">
        <img src="{{ asset('assets/images/logo/hero-bg3.jpg') }}" class="d-block w-100" alt="">
      </div>
    </div>
  </div>

</div>
@endsection

@section('content')
<div class="diag-divider" aria-hidden="true"></div>

<section id="videos" class="container my-5">
  <div class="row">
    <div class="col-md-8">
      <h3 class="mb-4">Video Highlights</h3>
      <div class="row g-4 video-grid">
        <div class="col-md-6 col-lg-4 video-card micro-anim">
          <div class="card h-100">
            <img class="card-img-top" src="https://via.placeholder.com/800x420.png?text=Video+1" alt="Video 1">
            <div class="card-body">
              <h5 class="card-title">Livelihood Training Highlights</h5>
              <p class="card-text text-muted">Practical sessions and success stories from beneficiaries.</p>
            </div>
          </div>
        </div>

        <div class="col-md-6 col-lg-4 video-card micro-anim">
          <div class="card h-100">
            <img class="card-img-top" src="https://via.placeholder.com/800x420.png?text=Video+2" alt="Video 2">
            <div class="card-body">
              <h5 class="card-title">Cooperative Fair 2026</h5>
              <p class="card-text text-muted">Community exhibits, product showcases and networking.</p>
            </div>
          </div>
        </div>

        <div class="col-md-6 col-lg-4 video-card micro-anim">
          <div class="card h-100">
            <img class="card-img-top" src="https://via.placeholder.com/800x420.png?text=Video+3" alt="Video 3">
            <div class="card-body">
              <h5 class="card-title">Enterprise Development Tips</h5>
              <p class="card-text text-muted">Practical tips for micro-enterprises and local markets.</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Latest updates (static placeholders) -->
      <div class="mt-5">
        <h4 class="mb-3">Latest Updates</h4>
        <div class="row g-3">
          @for($i = 1; $i <= 3; $i++)
            <div class="col-12">
              <a href="#" class="text-decoration-none card update-card" style="display: block; cursor: pointer;">
                <div class="card-body d-flex">
                  <div class="me-3 flex-shrink-0">
                    <div class="bg-primary text-white d-flex align-items-center justify-content-center" style="height:64px; width:64px; border-radius:6px; font-weight:bold;">
                      {{ chr(64+$i) }}
                    </div>
                  </div>
                  <div>
                    <h6 class="card-title mb-1">Sample Update {{ $i }}</h6>
                    <p class="card-text small text-muted">Brief description of the update goes here.</p>
                  </div>
                </div>
              </a>
            </div>
          @endfor
        </div>
      </div>
    </div>

    <aside class="col-md-4">
      <div class="card sidebar-card mb-4">
        <div class="card-body">
          <h5 class="card-title">Memorandum Circulars</h5>
          <ul class="memorandum-list mt-3">
            <li class="memorandum-item">
              <div class="mn-icon">1</div>
              <div class="mn-body">
                <a href="#" class="d-block fw-bold">MC 2026-01: Cooperative Registration</a>
                <small class="text-muted">Issued Feb 10, 2026</small>
              </div>
            </li>
            <li class="memorandum-item mt-2">
              <div class="mn-icon">2</div>
              <div class="mn-body">
                <a href="#" class="d-block fw-bold">MC 2026-02: Livelihood Grants</a>
                <small class="text-muted">Issued Jan 28, 2026</small>
              </div>
            </li>
            <li class="memorandum-item mt-2">
              <div class="mn-icon">3</div>
              <div class="mn-body">
                <a href="#" class="d-block fw-bold">MC 2026-03: Market Day Guidelines</a>
                <small class="text-muted">Issued Jan 15, 2026</small>
              </div>
            </li>
          </ul>
        </div>
      </div>

      <div class="card sidebar-card">
        <div class="card-body">
          <h5 class="card-title">Accomplishment Reports</h5>
          <ul class="aside-list">
            @for($j = 1; $j <= 3; $j++)
              <li class="aside-item">
                <div class="aside-icon">{{ $j }}</div>
                <div class="aside-link-wrapper">
                  <a href="#" class="aside-link">Report {{ $j }}</a>
                  <div class="aside-meta small text-muted">Feb {{ 25+$j }}, 2026</div>
                </div>
              </li>
            @endfor
          </ul>
        </div>
      </div>
    </aside>
  </div>
</section>

<!-- Accomplishment Report Modal (demo) -->
<div class="modal fade" id="accomplishmentReportModal" tabindex="-1" aria-labelledby="accomplishmentReportModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="accomplishmentReportModalLabel">Accomplishment Report</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="accomplishmentReportModalBody">
        <p>Sample report content goes here.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <a href="#" id="accomplishmentFileLink" class="btn btn-primary" target="_blank" style="display:none;">Download Document</a>
      </div>
    </div>
  </div>
</div>

@endsection
@section('styles')
  /* Override global styles for hero on this page */
  /* carousel container should fill parent and not constrain height */
  /* simplified carousel container with fixed height */
  .carousel-wrapper {
    max-width: 900px;
    margin: 0 auto;
    overflow: hidden;
    height: 290px; /* fixed viewport height */
  }

  #heroCarousel {
    width: 100%;
    height: 100%;
  }

  /* slide image fills container fully */
  #heroCarousel .carousel-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
    margin: 0;
  }

  /* if any body padding rule existed, not needed for demo */
  
  .video-grid .card-img-top{width:100%;height:200px;object-fit:cover}
  .video-card .card-body{min-height:84px}
  .sidebar-card.sticky-top{top:calc(80px + 1rem) !important}

  @media (max-width:768px){
    .video-grid .card-img-top{height:160px}
  }
@endsection