@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Manage Store</h3>
    <div>
      <a href="{{ route('admin.store_locations.create') }}" class="btn btn-primary me-2" target="_self">Add Location</a>
      <button class="btn btn-outline-secondary me-2" type="button" data-bs-toggle="modal" data-bs-target="#viewItemsModal">View Items</button>
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
              <div class="mb-2 d-flex justify-content-between align-items-center">
                <div class="fw-bold">{{ $cat }}</div>
                <div class="d-flex gap-2">
                  <form method="POST" action="{{ route('admin.store_categories.update') }}" class="d-flex" onsubmit="return confirm('Rename category?')">
                    @csrf
                    <input type="hidden" name="old_category" value="{{ $cat }}">
                    <input name="new_category" class="form-control form-control-sm" placeholder="Rename" style="max-width:220px">
                    <button class="btn btn-sm btn-outline-primary" type="submit">Rename</button>
                  </form>
                  <form method="POST" action="{{ route('admin.store_categories.delete') }}" onsubmit="return confirm('Delete entire category & its items?')">
                    @csrf
                    <input type="hidden" name="category" value="{{ $cat }}">
                    <button class="btn btn-sm btn-outline-danger">Delete Category</button>
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
<!-- View Items modal (separate from Add Category modal) -->
          <div class="modal fade" id="viewItemsModal" tabindex="-1" aria-labelledby="viewItemsModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-xl">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="viewItemsModalLabel">View Items</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                  @php
                    $cats = @json_decode(@file_get_contents(resource_path('data/store_categories.json')), true) ?: [];
                  @endphp

                  {{-- Add Item form (only here) --}}
                  <form method="POST" action="{{ route('admin.store_categories.items.store') }}" class="mb-3">
                    @csrf
                    <div class="row g-2">
                      <div class="col-auto">
                        <select name="category" class="form-select form-select-sm" required>
                          @foreach(array_keys($cats) as $cat)
                            <option value="{{ $cat }}">{{ $cat }}</option>
                          @endforeach
                        </select>
                      </div>
                      <div class="col">
                        <input name="item" class="form-control form-control-sm" placeholder="Item name" required>
                      </div>
                      <div class="col-4">
                        <input name="map_url" class="form-control form-control-sm" placeholder="Optional map URL or query">
                      </div>
                      <div class="col-auto">
                        <button class="btn btn-sm btn-primary">Add Item</button>
                      </div>
                    </div>
                  </form>

                  {{-- Items grouped by category --}}
                  @foreach($cats as $cat => $items)
                    <div class="mb-3">
                      <div class="fw-bold mb-2">{{ $cat }}</div>
                      <div class="row g-2">
                        @foreach($items as $it)
                          @php
                            $label = is_array($it) && array_key_exists('label',$it) ? $it['label'] : $it;
                            $imap = is_array($it) && array_key_exists('map_url',$it) ? $it['map_url'] : '';
                          @endphp
                          <div class="col-md-4">
                            <div class="card p-2">
                              <div class="d-flex justify-content-between align-items-start">
                                <div>
                                  <div class="fw-semibold">{{ $label }}</div>
                                  <div class="small text-muted">Category: {{ $cat }}</div>
                                </div>
                                <div class="d-flex flex-column gap-1">
                                  <button class="btn btn-sm btn-outline-secondary btn-edit-item" data-cat="{{ $cat }}" data-label="{{ $label }}" data-map="{{ $imap }}">Edit</button>
                                  <a href="{{ route('admin.store_locations.create') }}?category={{ urlencode($cat) }}&item={{ urlencode($label) }}&map_url={{ urlencode($imap) }}" class="btn btn-sm btn-success" style="white-space:nowrap">Add Location</a>
                                  <form method="POST" action="{{ route('admin.store_categories.items.delete') }}">
                                    @csrf
                                    <input type="hidden" name="category" value="{{ $cat }}">
                                    <button name="item" value="{{ $label }}" class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
                                  </form>
                                </div>
                              </div>
                              <form class="edit-form mt-2" method="POST" action="{{ route('admin.store_categories.items.update') }}" style="display:none">
                                @csrf
                                <input type="hidden" name="category" value="{{ $cat }}">
                                <input type="hidden" name="old_item" value="{{ $label }}">
                                <div class="input-group input-group-sm">
                                  <input name="new_item" class="form-control" value="{{ $label }}">
                                  <input name="map_url" class="form-control" value="{{ $imap }}">
                                  <button class="btn btn-sm btn-primary">Save</button>
                                </div>
                              </form>
                            </div>
                          </div>
                        @endforeach
                      </div>
                    </div>
                  @endforeach

                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
              </div>
            </div>
          </div>

          <script>
          document.addEventListener('DOMContentLoaded', function(){
            document.querySelectorAll('.btn-edit-item').forEach(function(b){
              b.addEventListener('click', function(){
                const card = b.closest('.card');
                const form = card.querySelector('.edit-form');
                form.style.display = form.style.display === 'none' ? '' : 'none';
              });
            });
          });
          </script>

</div>
@endsection
