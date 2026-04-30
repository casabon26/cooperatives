@extends('layouts.app')

@section('content')
  <div class="py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <div class="d-flex align-items-center">
        @if(request()->query('section') === 'livelihood')
          <a href="{{ route('livelihood') }}" class="me-3 text-decoration-none">Back</a>
        @endif
        <h1 class="m-0">Gallery</h1>
      </div>
      <small class="text-muted">All photos</small>
    </div>

    @can('access-admin')
      <div class="mb-3">
        <form method="post" action="{{ route('admin.galleries.store') }}" enctype="multipart/form-data">
          @csrf
          <input type="hidden" name="section" value="livelihood">
          <div class="row g-2 align-items-end">
            <div class="col-auto">
              <label class="form-label visually-hidden">Image</label>
              <input type="file" name="image" accept="image/*" class="form-control" required>
            </div>
            <div class="col">
              <input name="title" class="form-control" placeholder="Title">
            </div>
            <div class="col">
              <input name="description" class="form-control" placeholder="Description">
            </div>
            <div class="col-auto">
              <div class="d-flex">
                <button class="btn btn-primary">Add to Gallery</button>
                <button type="button" class="btn btn-outline-danger ms-2" data-bs-toggle="modal" data-bs-target="#galleryDeleteModal">Delete Items</button>
              </div>
            </div>
          </div>
        </form>
      </div>
    @endcan

    @if($galleries->count())
      <div class="row g-3">
        @foreach($galleries as $g)
          <div class="col-12 col-sm-6 col-md-4 col-lg-3">
            <div class="card h-100">
              <a href="#" class="gallery-thumb d-block" data-modal-url="{{ url('/galleries/'.$g->id.'/modal') }}">
                @if($g->image_url)
                  <img src="{{ $g->image_url }}" alt="{{ $g->alt_text ?: $g->title }}" class="card-img-top" style="height:180px; object-fit:cover;">
                @else
                  <div class="d-flex align-items-center justify-content-center p-3">No image</div>
                @endif
              </a>
              <div class="card-body">
                @if($g->title)<h6 class="card-title mb-1">{{ $g->title }}</h6>@endif
                @if($g->description)<p class="card-text text-muted small">{{ Str::limit($g->description, 140) }}</p>@endif
              </div>
            </div>
          </div>
        @endforeach
      </div>

      <div class="mt-4">
        {{ $galleries->links() }}
      </div>
    @else
      <div class="alert alert-info">No photos in the gallery yet.</div>
    @endif

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
    
    <!-- Admin: Delete gallery modal -->
    @can('access-admin')
    <div class="modal fade" id="galleryDeleteModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Delete Gallery Images</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            @if($galleries->count())
              <div class="row g-3">
                @foreach($galleries as $g)
                  <div class="col-6 col-sm-4 col-md-3">
                    <div class="card h-100">
                      @if($g->image_url)
                        <img src="{{ $g->image_url }}" class="card-img-top" style="height:140px; object-fit:cover;" alt="{{ $g->alt_text ?: $g->title }}">
                      @else
                        <div class="d-flex align-items-center justify-content-center p-3">No image</div>
                      @endif
                      <div class="card-body p-2">
                        <div class="small mb-2">@if($g->title){{ Str::limit($g->title,40) }}@endif</div>
                        <form method="post" action="{{ route('admin.galleries.destroy', $g->id) }}" onsubmit="return confirm('Delete this image?');">
                          @csrf
                          @method('DELETE')
                          <button class="btn btn-sm btn-danger w-100">Delete</button>
                        </form>
                      </div>
                    </div>
                  </div>
                @endforeach
              </div>
              <div class="mt-3 small text-muted">If you don't see an image here, use the main Gallery page to locate it and delete from its card.</div>
            @else
              <div class="alert alert-info">No photos available to delete.</div>
            @endif
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>
    @endcan

    <script>
    document.addEventListener('DOMContentLoaded', function(){
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
  </div>
@endsection
