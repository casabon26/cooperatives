@extends('layouts.app')

@section('content')
<div class="container py-4">
  <h3>Edit Store Location</h3>
  @if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
  @endif
  @if($errors->any())
    <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach</ul></div>
  @endif
  <form method="POST" action="{{ route('admin.store_locations.update', $location) }}" target="_self">
    @csrf
    @method('PUT')
    @php $storeCats = @json_decode(@file_get_contents(resource_path('data/store_categories.json')), true) ?: []; @endphp
    <div class="mb-3">
      <label class="form-label">Category</label>
      <select name="category" id="categorySelect" class="form-select">
        <option value="">-- Select category --</option>
        @foreach($storeCats as $cname => $items)
          <option value="{{ $cname }}" {{ (old('category', $location->category) == $cname) ? 'selected' : '' }}>{{ $cname }}</option>
        @endforeach
      </select>
    </div>
    <div class="mb-3">
      <label class="form-label">Item (choose from category)</label>
      <select name="item" id="itemSelect" class="form-select">
        <option value="">-- Select item --</option>
      </select>
      <div class="small text-muted">Choose the item this location corresponds to so it appears when the item is clicked on the public map.</div>
    </div>

    <div class="mb-3">
      <label class="form-label">Name</label>
      <input name="name" class="form-control" value="{{ old('name', $location->name) }}" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Address</label>
      <input name="address" class="form-control" value="{{ old('address', $location->address) }}">
    </div>
    <div class="row">
      <div class="col-md-6 mb-3">
        <label class="form-label">Latitude</label>
        <input name="lat" class="form-control" value="{{ old('lat', $location->lat) }}">
      </div>
      <div class="col-md-6 mb-3">
        <label class="form-label">Longitude</label>
        <input name="lng" class="form-control" value="{{ old('lng', $location->lng) }}">
      </div>
    </div>
    
    <div class="mb-3">
      <label class="form-label">Google Maps Link (optional)</label>
      <input name="map_url" class="form-control" value="{{ old('map_url', $location->map_url) }}" placeholder="https://www.google.com/maps/...">
      <div class="small text-muted">Paste a Google Maps link and the system will try to extract coordinates.</div>
    </div>
    <div class="mb-3">
      <label class="form-label">Icon URL (optional)</label>
      <input name="icon_url" class="form-control" value="{{ old('icon_url', $location->icon_url) }}">
    </div>
    <div class="mb-3">
      <label class="form-label">Description</label>
      <textarea name="description" class="form-control" rows="3">{{ old('description', $location->description) }}</textarea>
    </div>
    <button type="submit" class="btn btn-primary">Save</button>
    <a href="{{ route('admin.store_locations.index') }}" class="btn btn-link" target="_self">Cancel</a>
  </form>
  </div>

  @section('scripts')
  <script>
    (function(){
      const storeCats = {!! json_encode($storeCats ?? [], JSON_UNESCAPED_UNICODE) !!};
      const latEl = document.querySelector('input[name="lat"]');
      const lngEl = document.querySelector('input[name="lng"]');
      const catSel = document.getElementById('categorySelect');
      const itemSel = document.getElementById('itemSelect');
      const nameInput = document.querySelector('input[name="name"]');
      const preSelectedItem = {!! json_encode(old('item', $location->tags ?? request()->get('item') ?: ''), JSON_UNESCAPED_UNICODE) !!};

      function populateItemsFor(cat){
        itemSel.innerHTML = '<option value="">-- Select item --</option>';
        const items = storeCats[cat] || [];
        items.forEach(it =>{
          const label = (typeof it === 'string') ? it : (it.label || '');
          const opt = document.createElement('option'); opt.value = label; opt.textContent = label;
          itemSel.appendChild(opt);
        });
        if(preSelectedItem){
          for(const o of itemSel.options){ if(o.value === preSelectedItem){ o.selected = true; nameInput.value = o.value; break; } }
        }
      }
      if(catSel) catSel.addEventListener('change', function(){ populateItemsFor(this.value); });
      if(itemSel) itemSel.addEventListener('change', function(){ const o = this.selectedOptions[0]; if(o){ nameInput.value = o.value; } });
      // initialize items for current category
      if(catSel && catSel.value) populateItemsFor(catSel.value);
    })();
  </script>
  @endsection
</div>
@endsection
