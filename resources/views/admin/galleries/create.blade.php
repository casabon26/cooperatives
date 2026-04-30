@extends('layouts.app')

@section('content')
  <div class="py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h1 class="m-0">Add Gallery Image</h1>
      <a href="{{ route('admin.galleries.index') }}" class="btn btn-secondary">Back</a>
    </div>

    @if($errors->any())
      <div class="alert alert-danger">
        <ul class="mb-0">
          @foreach($errors->all() as $e)
            <li>{{ $e }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form method="post" action="{{ route('admin.galleries.store') }}" enctype="multipart/form-data">
      @csrf
      <div class="mb-3">
        <label class="form-label">Section</label>
        <select name="section" class="form-select">
          <option value="livelihood" selected>Livelihood (site gallery)</option>
          <option value="cooperative">Cooperative (per-cooperative gallery)</option>
        </select>
      </div>
      <div class="mb-3">
        <label class="form-label">Image file</label>
        <input type="file" name="image" class="form-control" accept="image/*" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Title</label>
        <input type="text" name="title" class="form-control" placeholder="Title for this image">
      </div>
      <div class="mb-3">
        <label class="form-label">Description (optional)</label>
        <textarea name="description" class="form-control" rows="3"></textarea>
      </div>
      <div class="mb-3">
        <label class="form-label">Alt text (optional)</label>
        <input type="text" name="alt_text" class="form-control">
      </div>
      <button class="btn btn-primary">Upload</button>
    </form>
  </div>
@endsection
