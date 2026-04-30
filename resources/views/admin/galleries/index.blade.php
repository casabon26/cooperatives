@extends('layouts.app')

@section('content')
  <div class="py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h1 class="m-0">Manage Gallery</h1>
      <a href="{{ route('admin.galleries.create') }}" class="btn btn-primary">Add to Gallery</a>
    </div>

    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($galleries->count())
      <div class="row g-3">
        @foreach($galleries as $g)
          <div class="col-12 col-sm-6 col-md-4 col-lg-3">
            <div class="card h-100">
              @if($g->image_url)
                <img src="{{ $g->image_url }}" class="card-img-top" style="height:160px; object-fit:cover;">
              @endif
              <div class="card-body">
                <h6 class="card-title">{{ $g->title ?: 'Untitled' }}</h6>
                <p class="small text-muted">{{ Str::limit($g->description, 100) }}</p>
                <form method="post" action="{{ route('admin.galleries.destroy', $g) }}" onsubmit="return confirm('Remove this image?');">
                  @csrf
                  @method('DELETE')
                  <button class="btn btn-sm btn-danger">Remove</button>
                </form>
              </div>
            </div>
          </div>
        @endforeach
      </div>

      <div class="mt-4">{{ $galleries->links() }}</div>
    @else
      <div class="alert alert-info">No gallery images yet.</div>
    @endif
  </div>
@endsection
