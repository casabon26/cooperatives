@extends('layouts.app')

@section('content')
<div class="container py-5">
  <style>
    /* Scoped livelihood portal styles (based on enterprise portal) */
    .livelihood-portal .card { border-radius:12px; box-shadow:0 8px 24px rgba(15,23,42,0.03); }
    .livelihood-portal .card-body h3 { font-size:1.375rem; font-weight:700; color:#0f172a; }
    .livelihood-portal .portal-intro ul { list-style:none; padding-left:0; margin:0.6rem 0 1rem 0; display:grid; gap:0.5rem }
    .livelihood-portal .portal-intro ul li::before { content:''; width:8px; height:8px; border-radius:50%; background:linear-gradient(180deg,#f59e0b,#f97316); display:inline-block; margin-right:0.6rem }
    .livelihood-portal .portal-hero { padding:1rem; border-radius:10px; background: linear-gradient(180deg, rgba(245,158,11,0.04), rgba(245,158,11,0.02)); border:1px solid rgba(245,158,11,0.06); margin-bottom:1rem }
    .livelihood-portal .portal-hero .portal-lead { color:#334155; margin-bottom:0.75rem }
    .livelihood-portal aside .card { background: linear-gradient(180deg,#fff,#feffff); }

    /* SLPA card styling to match cooperative cards (screenshot) */
    .slpa-card { border-radius:12px; border:1px solid rgba(var(--primary-r), 0.12); background: #fff7f7; padding:1rem; text-align:center; }
    .slpa-card .slpa-media { height:96px; display:flex; align-items:center; justify-content:center; overflow:hidden; background:#fff; border-radius:8px; margin:0 auto; width:88px; }
    .slpa-card img { max-height:64px; max-width:100%; object-fit:contain; display:block; }
    .slpa-card .slpa-title { color:var(--primary); font-weight:700; font-size:1rem; margin-top:.6rem; margin-bottom:.25rem; text-align:center; }
    .slpa-card .slpa-meta { color:#94a3b8; font-size:.86rem; margin-bottom:.5rem; text-align:center; }
    .slpa-card .slpa-desc { color:#0f172a; text-align:center; margin-top:.35rem; }
    .slpa-card .slpa-products { color:#475569; margin-top:.5rem; font-size:.875rem; text-align:center; }

    /* Layout adjustments to match spacing in screenshot */
    .slpa-grid-row { gap:1rem; }
    @media (min-width: 992px) { .slpa-grid-row > [class*='col-'] { padding-left:.5rem; padding-right:.5rem; } }

    @media (max-width:767px){ .livelihood-portal .card-body h3{font-size:1.25rem} }

    /* See More button styles: interactive, no underline */
    .see-more-btn {
      background: transparent;
      border: 0;
      color: #f97316;
      text-decoration: none;
      padding: 0;
      font-weight: 600;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      gap: .4rem;
      transition: color .12s ease, opacity .12s ease;
    }
    .see-more-btn:hover, .see-more-btn:focus {
      color: #f59e0b;
      text-decoration: none;
      opacity: 0.95;
      outline: none;
    }
    .see-more-caret svg {
      width: 14px;
      height: 14px;
      transition: transform .18s ease;
      transform-origin: center;
      display: block;
    }
    .see-more-caret.expanded svg {
      transform: rotate(180deg);
    }
    /* Scrollable container for Available Stalls (show ~10 visible, scroll for more) */
    .available-stalls-scroll { max-height: 420px; overflow-y: auto; }
  </style>

  <div class="row g-4">
    <main class="col-12 col-lg-8 livelihood-portal">
      <div class="card">
        <div class="card-body">
          <div class="portal-hero">
            <h3 class="mb-1">Livelihood Portal — Cooperative & Livelihood Development</h3>
            <p class="portal-lead">Resources and support for cooperative development, livelihood projects, and enterprise strengthening. Use the tools below to find programs, training, and assistance available to your group or enterprise.</p>

            <div class="mt-3" id="livelihood-summary">
              <h5 class="mb-2">Livelihood Trainings – First Quarter 2026</h5>
              <ul class="portal-intro" id="training-preview">
                <li>Livelihood Training on Dishwashing Soap Making & Meat Processing (February 21-27, 2026)</li>
                <li>SumeCo Soap Making Training (January 24, 2026)</li>
              </ul>

              <div id="narrative-preview" class="small text-muted mb-2">
                The Livelihood Division continues to show its commitment to helping the community by conducting different skills training and small business programs for cooperative members and local residents. The activities in the first quarter of the 2026 show a clear effort to give more livelihood options, support small businesses, and teach useful skills that can help people earn income and improve their daily lives.
              </div>

              <button id="see-more-btn" class="see-more-btn" aria-expanded="false" type="button">
                <span class="see-more-label">See More</span>
                <span class="see-more-caret" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" string="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6 6-6"></path></svg></span>
              </button>

              <div id="livelihood-full" style="display:none; margin-top:0.75rem;">
                <ul class="portal-intro" id="training-full">
                  <li>Livelihood Training on Dishwashing Soap Making & Meat Processing (February 21-27, 2026)</li>
                  <li>SumeCo Soap Making Training (January 24, 2026)</li>
                  <li>Livelihood Training on Meat Processing, Candle Making & Mushroom Production (March 16, 2026)</li>
                  <li>Special Livelihood Training – Nail Care & Massage, Siopao & Empanada Making (March 11 & 24, 2026)</li>
                </ul>

                <h6 class="mt-3">Narrative Report on Livelihood Trainings – First Quarter 2026</h6>
                <div class="small text-muted">
                  <p>The Livelihood Division continues to show its commitment to helping the community by conducting different skills training and small business programs for cooperative members and local residents. The activities in the first quarter of the 2026 show a clear effort to give more livelihood options, support small businesses, and teach useful skills that can help people earn income and improve their daily lives.</p>

                  <p>In January, the division conducted a soap-making training attended by 20 members of SUMECO at the Cabuyao Retail Plaza. The training focused on teaching the proper way of making soap, with attention to good quality and efficient production. This helped participants learn how to create soap products that they can sell to earn income.</p>

                  <p>In February, another training was held from February 21 to 27 for participants from Barangay Banay-banay and Barangay Bigaa. The training focused on dishwashing liquid making and meat processing. The goal was to teach participants how to make and sell these products, encouraging them to start small businesses. Proper hygiene, correct methods, and product quality were also emphasized to help them compete in the market.</p>

                  <p>March was a very busy month with several training activities. On March 11, a special livelihood training was held at Cabuyao Centro Mall with 42 participants. The training included nail care, massage, and making Filipino snacks such as siopao and empanada. This aimed to give participants more livelihood choices, especially in personal services and food businesses.</p>

                  <p>On the same day, another group of 42 participants attended a meat processing training. They learned how to preserve meat, use proper processing methods, and create different products. This training helped them to improve their skills and possibly start or grow their own small business.</p>

                  <p>In the middle of March, a large training session was conducted at the Cabuyao Integrated Learning Facility with 116 participants. The training covered meat processing, candle making, and mushroom production. The goal was to teach different skills that can help participants start various types of small businesses and earn steady income.</p>

                  <p>Overall, these livelihood programs show the division's effort to help people learn skills, start businesses, and improve their financial situation. By offering trainings based on the needs of the community, the Livelihood Division helps create more income opportunities and supports a stronger and more stable community.</p>
                </div>
              </div>

              <script>
                (function(){
                  const btn = document.getElementById('see-more-btn');
                  const label = btn.querySelector('.see-more-label');
                  const caret = btn.querySelector('.see-more-caret');
                  const full = document.getElementById('livelihood-full');
                  const preview = document.getElementById('training-preview');
                  const narrativePreview = document.getElementById('narrative-preview');
                  let expanded = false;
                  btn.addEventListener('click', function(){
                    expanded = !expanded;
                    if (expanded) {
                      full.style.display = 'block';
                      btn.setAttribute('aria-expanded','true');
                      label.textContent = 'See Less';
                      caret.classList.add('expanded');
                      preview.style.display = 'none';
                      narrativePreview.style.display = 'none';
                    } else {
                      full.style.display = 'none';
                      btn.setAttribute('aria-expanded','false');
                      label.textContent = 'See More';
                      caret.classList.remove('expanded');
                      preview.style.display = '';
                      narrativePreview.style.display = '';
                    }
                  });
                })();
              </script>
            </div>
          </div>

          <!-- Programs & Services moved to sidebar dropdowns -->
          
          <div class="mt-4">
            <div class="card">
              <div class="card-body">
                <h5 class="card-title">CabStop</h5>
                <p class="small text-muted">Choose the CabStop type below. (List will be populated later.)</p>
                @if(!empty($cabstops) && count($cabstops))
                  <select id="cabstopSelect" class="form-select" aria-label="CabStop select">
                    <option value="" selected disabled>Select CabStop</option>
                    @foreach($cabstops as $opt)
                        <option value="{{ $opt->key ?? $opt->label }}">{{ $opt->label }}</option>
                    @endforeach
                  </select>
                @else
                  <select id="cabstopSelect" class="form-select" aria-label="CabStop select">
                    <option selected disabled>Select CabStop</option>
                    <option value="cabstop_bayan">CabStop Bayan</option>
                    <option value="cabstop_cabs">CabStop CABS</option>
                    <option value="cabstop_municipal">CabStop Municipal</option>
                  </select>
                @endif

                <div class="mt-3" id="cabstopControls">
                  <label class="form-label small mb-1">Store Type</label>
                  <select id="storeTypeSelect" class="form-select">
                    <option value="">All</option>
                    <option value="food">Food</option>
                    <option value="non_food">Non-food</option>
                  </select>
                </div>

                <div id="cabstopStores" class="mt-3">
                  {{-- Stores for selected CabStop will be loaded here via AJAX --}}
                </div>
              </div>
            </div>
          </div>
          
          <div class="mt-4">
            <h4 class="mb-3">SLPA</h4>
            <div class="row">
              @if(!empty($slpas) && count($slpas))
                @foreach($slpas as $slpa)
                  <div class="col-12 col-md-6 col-lg-4 mb-3">
                    <div class="slpa-card">
                      <div class="slpa-media">
                        @if(!empty($slpa->image_url))
                          <img src="{{ $slpa->image_url }}" alt="{{ $slpa->name }}">
                        @else
                          <svg width="56" height="56" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" class="text-muted">
                            <circle cx="12" cy="8" r="3" stroke="#cbd5e1" stroke-width="1.5"/>
                            <path d="M4 20c0-3.314 4-5 8-5s8 1.686 8 5" stroke="#e2e8f0" stroke-width="1.5" stroke-linecap="round"/>
                          </svg>
                        @endif
                      </div>
                      <div class="slpa-title"><a href="#" class="slpa-name-link" data-modal-url="{{ url('/slpas/'.$slpa->id.'/modal') }}">{{ $slpa->name }}</a></div>
                      <div class="slpa-meta">
                        @if(!empty($slpa->business)) {{ $slpa->business }} @else SLPA @endif
                        @if(!empty($slpa->members_count)) &middot; {{ $slpa->members_count }} members @endif
                      </div>
                      @if(!empty($slpa->description))
                        <div class="slpa-desc small">{{ Str::limit($slpa->description,140) }}</div>
                      @endif
                      @if(!empty($slpa->products))
                        @php
                          $p = $slpa->products;
                          $firstName = null; $firstDesc = null; $countMore = 0; $names = [];
                          if (is_array($p)) {
                              foreach ($p as $it) {
                                  if (is_array($it) || is_object($it)) {
                                      $n = trim((string)data_get($it,'name',''));
                                      $d = trim((string)data_get($it,'description',''));
                                  } else {
                                      $n = trim((string)$it);
                                      $d = '';
                                  }
                                  if ($n === '') continue;
                                  $names[] = $n;
                                  if ($firstName === null) { $firstName = $n; $firstDesc = $d; }
                              }
                              $countMore = max(0, count($names) - 1);
                          } else {
                              $firstName = (string)$p;
                              $firstDesc = '';
                          }
                        @endphp
                        @if($firstName)
                          <div class="mt-2 small text-muted"><strong>Products:</strong> {{ $firstName }}@if($firstDesc) — {{ Str::limit($firstDesc,80) }}@endif @if($countMore) <span class="text-muted">(+{{ $countMore }} more)</span>@endif</div>
                        @endif
                      @endif
                    </div>
                  </div>
                @endforeach
              @else
                <div class="col-12"><div class="small text-muted">No SLPA entries yet.</div></div>
              @endif
            </div>
          
                  <!-- SLPA modal container (AJAX-loaded content) -->
                  <div class="modal fade" id="slpaModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-dialog-scrollable">
                      <div class="modal-content">
                        <div class="modal-header">
                          <h5 class="modal-title">SLPA</h5>
                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body" id="slpaModalBody">
                          <div class="text-center text-muted py-3">Loading...</div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <script>
                  document.addEventListener('DOMContentLoaded', function(){
                    document.querySelectorAll('.slpa-name-link').forEach(el=>{
                      el.addEventListener('click', function(ev){
                        ev.preventDefault();
                        const modalUrl = el.dataset.modalUrl;
                        if(!modalUrl) return;
                        const modalEl = document.getElementById('slpaModal');
                        const modalBody = document.getElementById('slpaModalBody');
                        if(!modalEl || !modalBody) { return; }
                        modalBody.innerHTML = '<div class="text-center text-muted py-3">Loading...</div>';
                        fetch(modalUrl, {headers:{'X-Requested-With':'XMLHttpRequest'}})
                        .then(r=>{ if(!r.ok) throw r; return r.text(); })
                        .then(html=>{
                          modalBody.innerHTML = html;
                          try { const bsModal = new bootstrap.Modal(modalEl); bsModal.show(); } catch(e) { console.warn('Bootstrap modal unavailable', e); }
                        }).catch(()=>{
                          console.warn('Failed to load SLPA modal, doing nothing');
                        });
                      });
                    });

                    // Gallery thumbnails in sidebar / listing open modal via AJAX
                    document.querySelectorAll('.gallery-thumb').forEach(el=>{
                      el.addEventListener('click', function(ev){
                        ev.preventDefault();
                        const modalUrl = el.dataset.modalUrl;
                        if(!modalUrl) return;
                        const modalEl = document.getElementById('galleryModal');
                        const modalBody = document.getElementById('galleryModalBody');
                        if(!modalEl || !modalBody) return;
                        modalBody.innerHTML = '<div class="text-center text-muted py-3">Loading...</div>';
                        fetch(modalUrl, {headers:{'X-Requested-With':'XMLHttpRequest'}})
                        .then(r=>{ if(!r.ok) throw r; return r.text(); })
                        .then(html=>{
                          modalBody.innerHTML = html;
                          try {
                            const img = modalBody.querySelector('[data-gallery-modal-image]');
                            if(img){ img.addEventListener('click', function(){ img.classList.toggle('zoomed'); }); }
                          } catch(e) { console.warn('Attach zoom handler failed', e); }
                          try { const bsModal = new bootstrap.Modal(modalEl); bsModal.show(); } catch(e) { console.warn('Bootstrap modal unavailable', e); }
                        }).catch(()=>{
                          console.warn('Failed to load gallery modal');
                        });
                      });
                    });
                  });
                  </script>
                  <!-- Gallery modal (AJAX-loaded) -->
                  <div class="modal fade" id="galleryModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                      <div class="modal-content">
                        <div class="modal-header">
                          <h5 class="modal-title">Photo</h5>
                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body" id="galleryModalBody">
                          <div class="text-center text-muted py-3">Loading...</div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <script>
                    (function(){
                      var sel = document.getElementById('cabstopSelect');
                      var container = document.getElementById('cabstopStores');
                      if(!sel || !container) return;

                      function renderStores(list){
                        if(!list || !list.length){
                          container.innerHTML = '<div class="small text-muted">No stores found for this place.</div>';
                          return;
                        }
                        var html = '<div class="card"><div class="card-body"><h6 class="mb-3">Available Stalls</h6><div class="available-stalls-scroll"><div class="list-group list-group-flush">';
                        list.forEach(function(it){
                          var label = it.name || it.title || '';
                          var storeTypeLabel = '';
                          var storeBadgeClass = '';
                          
                          if(it.store_type){
                            if(it.store_type === 'food'){
                              storeTypeLabel = 'Food Stall';
                              storeBadgeClass = 'bg-success';
                            } else if(it.store_type === 'non_food'){
                              storeTypeLabel = 'Non-food Stall';
                              storeBadgeClass = 'bg-secondary';
                            } else {
                              storeTypeLabel = it.store_type;
                              storeBadgeClass = 'bg-warning';
                            }
                          }
                          
                          var addressInfo = '';
                          if(it.address){
                            addressInfo = '<div class="small text-muted mt-1"><i class="bi bi-geo-alt"></i> ' + it.address + '</div>';
                          }
                          
                          var coordsInfo = '';
                          if(it.lat && it.lng){
                            coordsInfo = '<div class="small text-muted mt-1"><i class="bi bi-map"></i> ' + parseFloat(it.lat).toFixed(4) + ', ' + parseFloat(it.lng).toFixed(4) + '</div>';
                          }
                          
                          var descInfo = '';
                          if(it.description){
                            descInfo = '<div class="small mt-2">' + it.description + '</div>';
                          }
                          
                          var badge = storeTypeLabel ? '<span class="badge ' + storeBadgeClass + ' ms-2">'+ storeTypeLabel + '</span>' : '';
                          
                          html += '<div class="list-group-item py-2">';
                          html += '<div class="d-flex align-items-start justify-content-between">';
                          html += '<div class="fw-bold">' + label + badge + '</div>';
                          html += '</div>';
                          html += addressInfo + coordsInfo + descInfo;
                          html += '</div>';
                        });
                        html += '</div></div></div></div>';
                        container.innerHTML = html;
                      }

                      // helper to fetch with optional type
                      function loadStoresFor(place, type){
                        if(!place) { container.innerHTML = ''; return; }
                        var url = '/api/store-locations?place=' + encodeURIComponent(place);
                        if(type) url += '&store_type=' + encodeURIComponent(type);
                        // Only return stores intended for the Livelihood listings (no coordinates)
                        url += '&map=livelihood';
                        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                          .then(function(r){ if(!r.ok) throw r; return r.json(); })
                          .then(function(json){ renderStores(json || []); })
                          .catch(function(){ container.innerHTML = '<div class="small text-muted">Failed to load stores.</div>'; });
                      }

                      var storeTypeSel = document.getElementById('storeTypeSelect');
                      // When place changes, reload stores with selected type
                      sel.addEventListener('change', function(){
                        var v = sel.value;
                        // enable store type control
                        if(storeTypeSel) storeTypeSel.disabled = !v;
                        var t = storeTypeSel ? storeTypeSel.value : '';
                        loadStoresFor(v, t || '');
                      });

                      if(storeTypeSel){
                        // When store type changes, reload for current place
                        storeTypeSel.addEventListener('change', function(){
                          var place = sel.value;
                          var t = storeTypeSel.value;
                          loadStoresFor(place, t || '');
                        });
                        // default disabled until a place chosen
                        storeTypeSel.disabled = !sel.value;
                      }

                      // If select has a preselected value, trigger change
                      if(sel.value){ var ev = new Event('change'); sel.dispatchEvent(ev); }
                    })();
                  </script>
          </div>
        </div>
      </div>
    </main>

    <aside class="col-12 col-lg-4">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title mb-2">Programs</h5>
          <p class="text-muted small">Select a program below.</p>
          @if(!empty($programs) && count($programs))
            <select class="form-select" aria-label="Programs select">
              <option selected disabled>Select Program</option>
              @foreach($programs as $opt)
                <option value="{{ $opt->key ?? $opt->label }}">{{ $opt->label }}</option>
              @endforeach
            </select>
          </if(!empty($programs) && count($programs))
            <select class="form-select" aria-label="Programs select">

            </select>
          @endif
        </div>
      </div>
      <div class="card mt-3">
        <div class="card-body">
          <h5 class="card-title mb-2">Gallery</h5>
          <p class="text-muted small">Latest photos</p>
          <div class="row g-2">
            @if(!empty($galleries) && count($galleries))
              @foreach($galleries as $g)
                <div class="col-4">
                  <a href="#" class="d-block gallery-thumb" data-modal-url="{{ url('/galleries/'.$g->id.'/modal') }}" data-id="{{ $g->id }}" title="{{ $g->title }}">
                    @if($g->image_url)
                      <img src="{{ $g->image_url }}" alt="{{ $g->alt_text ?: $g->title }}" class="img-fluid rounded" style="height:72px; width:100%; object-fit:cover;">
                    @else
                      <div class="bg-light rounded d-flex align-items-center justify-content-center" style="height:72px">No image</div>
                    @endif
                  </a>
                </div>
              @endforeach
            @else
              <div class="col-12"><div class="small text-muted">No gallery photos yet.</div></div>
            @endif
          </div>
          <div class="mt-2 text-end">
            <a href="{{ route('gallery.index', ['section' => 'livelihood']) }}" class="see-more-btn">See all photos in gallery <span class="see-more-caret" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" string="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6 6-6"></path></svg></span></a>
          </div>
        </div>
      </div>

      <div class="card mt-3">
        <div class="card-body">
          <h5 class="card-title mb-2">Services</h5>
          <p class="text-muted small">Select a service below.</p>
          @if(!empty($services) && count($services))
            <select class="form-select" aria-label="Services select">
              <option selected disabled>Select Service</option>
              @foreach($services as $opt)
                <option value="{{ $opt->key ?? $opt->label }}">{{ $opt->label }}</option>
              @endforeach
            </select>
          @else
            <select class="form-select" aria-label="Services select">
              <option selected disabled>Select Service</option>
              <option value="service_training">Training & Capacity Building</option>
              <option value="service_business_planning">Business Planning Assistance</option>
              <option value="service_market_linkages">Market Linkages & Referrals</option>
              <option value="service_registration">Registration & Legal Support</option>
            </select>
          @endif
        </div>
      </div>
    </aside>
  </div>
</div>
@endsection

