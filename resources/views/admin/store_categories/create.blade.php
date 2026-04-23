@extends('layouts.app')
  
@section('content')
<div class="container py-4">
  <h3>Add Category</h3>
  @if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
  @endif
  @if($errors->any())
    <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach</ul></div>
  @endif

  <form method="POST" action="{{ route('admin.store_categories.store') }}" target="_self">
    @csrf
    <div class="mb-3">
      <label class="form-label">Category Name</label>
      <input name="category" class="form-control" required placeholder="e.g. Food & Beverages">
    </div>
    <button class="btn btn-primary">Create Category</button>
    @include('partials.back-button', ['url' => route('admin.store_locations.index'), 'label' => 'Cancel', 'class' => 'btn-link'])
  </form>
</div>
@endsection

