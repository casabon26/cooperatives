@extends('layouts.app')

@section('content')
<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Manage Store</h3>
    <div>
      <a href="{{ route('admin.store_locations.create') }}" class="btn btn-primary me-2" target="_self">Add Location</a>
      <button class="btn btn-outline-primary" type="button" data-bs-toggle="modal" data-bs-target="#categoriesModal">Add Category</button>
    </div>
  </div>

  <div class="row mb-3">
    <div class="col-12">

  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  <div class="card">
    <div class="card-body p-0">
      <table class="table mb-0">
        <thead>
          <tr>
            <th>Store Name</th>
            <th>Category / Tags</th>
            <th>Address</th>
            <th>Lat / Lng</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          @forelse($locations as $loc)
            <tr>
              <td>{{ $loc->name }}</td>
              <td>
                <div class="fw-bold">{{ $loc->category }}</div>
                <div class="small text-muted">{{ $loc->tags }}</div>
              </td>
              <td class="text-muted">{{ $loc->address }}</td>
              <td>{{ $loc->lat }} / {{ $loc->lng }}</td>
              <td class="text-end">
                <a href="{{ route('admin.store_locations.edit', $loc) }}" class="btn btn-sm btn-outline-secondary" target="_self">Edit</a>
                <form method="POST" action="{{ route('admin.store_locations.destroy', $loc) }}" style="display:inline-block" onsubmit="return confirm('Delete location?')">
                  @csrf
                  @method('DELETE')
                  <button class="btn btn-sm btn-danger">Delete</button>
                </form>
              </td>
            </tr>
          @empty
            <tr><td colspan="4" class="text-center text-muted">No store locations yet.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
  </div> <!-- /.col-md-8 -->
  </div> <!-- /.row -->
  </div> <!-- /.container -->
<!-- Categories modal (opens centered when clicking Add Category) -->
<div class="modal fade" id="categoriesModal" tabindex="-1" aria-labelledby="categoriesModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="categoriesModalLabel">Categories</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="card">
          <div class="card-body">
            {{-- Add Category inline form --}}
            <form id="modalAddCategoryForm" method="POST" action="{{ route('admin.store_categories.store') }}" style="margin-bottom:12px">
              @csrf
              <div class="input-group input-group-sm">
                <input name="category" class="form-control form-control-sm" placeholder="New category name" required>
                <button class="btn btn-sm btn-primary" type="submit">Create</button>
              </div>
            </form>

            @php
              $cats = @json_decode(@file_get_contents(resource_path('data/store_categories.json')), true) ?: [];
            @endphp

            @foreach($cats as $cat => $items)
              <div class="mb-2">
                <div class="d-flex justify-content-between align-items-start">
                  <div class="fw-bold">{{ $cat }}</div>
                  <form method="POST" action="{{ route('admin.store_categories.delete') }}" onsubmit="return confirm('Delete entire category & its items?')">
                    @csrf
                    <input type="hidden" name="category" value="{{ $cat }}">
                    <button class="btn btn-sm btn-outline-danger">Delete Category</button>
                  </form>
                </div>
                <div class="small text-muted mb-2">Items:</div>
                <div class="d-flex flex-wrap gap-2 mb-2">
                  @foreach($items as $it)
                    @php
                      $label = is_array($it) && array_key_exists('label',$it) ? $it['label'] : $it;
                      $imap = is_array($it) && array_key_exists('map_url',$it) ? $it['map_url'] : '';
                    @endphp
                    <form method="GET" action="{{ route('admin.store_locations.index') }}" style="display:inline">
                      <input type="hidden" name="category" value="{{ $cat }}">
                      <input type="hidden" name="tag" value="{{ $label }}">
                      <button class="btn btn-sm btn-outline-secondary">{{ $label }}</button>
                    </form>
                    <a href="{{ route('admin.store_locations.create') }}?category={{ urlencode($cat) }}&item={{ urlencode($label) }}&map_url={{ urlencode($imap) }}" class="btn btn-sm btn-success" style="margin-left:4px">Add Location</a>
                  @endforeach
                </div>
                <div class="d-flex gap-2 mb-2">
                  <form method="POST" action="{{ route('admin.store_categories.items.store') }}">
                    @csrf
                    <input type="hidden" name="category" value="{{ $cat }}">
                    <div class="input-group mb-0">
                      <input name="item" class="form-control form-control-sm" placeholder="Add item label" required>
                      <input name="map_url" class="form-control form-control-sm" placeholder="Optional map URL or query" style="max-width:260px">
                      <button class="btn btn-sm btn-primary">Add</button>
                    </div>
                  </form>
                  <form method="POST" action="{{ route('admin.store_categories.items.delete') }}">
                    @csrf
                    <input type="hidden" name="category" value="{{ $cat }}">
                    <div class="d-flex flex-wrap gap-2">
                      @foreach($items as $it)
                        <button name="item" value="{{ $it }}" class="btn btn-sm btn-outline-danger" type="submit">Remove {{ $it }}</button>
                      @endforeach
                    </div>
                  </form>
                </div>
              </div>
              <hr>
            @endforeach

          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
@endsection
