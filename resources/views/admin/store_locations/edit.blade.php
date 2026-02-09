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
    <div class="mb-3">
      <label class="form-label">Name</label>
      <input name="name" class="form-control" value="{{ old('name', $location->name) }}" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Address</label>
      <input name="address" class="form-control" value="{{ old('address', $location->address) }}">
    </div>
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
      <div class="d-flex gap-2">
        <select name="item" id="itemSelect" class="form-select">
          <option value="">-- Select item --</option>
        </select>
        <input name="item_map_url" id="itemMapUrl" class="form-control" placeholder="Optional item Google Maps URL" value="{{ old('item_map_url', $location->item_map_url ?? '') }}">
      </div>
      <div class="small text-muted">Choose the item this location corresponds to so it appears when the item is clicked on the public map.</div>
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
      <label class="form-label">Plus Code (optional)</label>
      <div class="input-group">
        <input id="plus_code" name="plus_code" class="form-control" value="{{ old('plus_code', $location->plus_code) }}">
        <button id="decodePlusBtn" type="button" class="btn btn-outline-secondary">Decode</button>
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
  <script src="https://cdn.jsdelivr.net/npm/open-location-code@1.0.4/open-location-code.min.js"></script>
  <script>
    (function(){
      const storeCats = {!! json_encode($storeCats ?? [], JSON_UNESCAPED_UNICODE) !!};
      const plusEl = document.getElementById('plus_code');
      const decodeBtn = document.getElementById('decodePlusBtn');
      const latEl = document.querySelector('input[name="lat"]');
      const lngEl = document.querySelector('input[name="lng"]');
      const catSel = document.getElementById('categorySelect');
      const itemSel = document.getElementById('itemSelect');
      const itemMapUrl = document.getElementById('itemMapUrl');

      function populateItemsFor(cat){
        itemSel.innerHTML = '<option value="">-- Select item --</option>';
        const items = storeCats[cat] || [];
        items.forEach(it =>{
          const label = (typeof it === 'string') ? it : (it.label || '');
          const opt = document.createElement('option'); opt.value = label; opt.textContent = label;
          opt.dataset.mapurl = (typeof it === 'object' && it.map_url) ? it.map_url : '';
          if(label === '{{ old('tags', $location->tags) }}') opt.selected = true;
          itemSel.appendChild(opt);
        });
      }
      if(catSel) catSel.addEventListener('change', function(){ populateItemsFor(this.value); });
      if(itemSel) itemSel.addEventListener('change', function(){ const o = this.selectedOptions[0]; if(o && o.dataset){ itemMapUrl.value = o.dataset.mapurl || ''; } });
      // initialize items for current category
      if(catSel && catSel.value) populateItemsFor(catSel.value);
      function decodePlus(){
        if(!plusEl) return;
        const v = (plusEl.value||'').trim();
        try{
          if(!v || v.indexOf('+')===-1) return alert('Enter a valid plus code first.');
          const c = OpenLocationCode.decode(v);
          if(c){
            if(latEl) latEl.value = c.latitudeCenter.toFixed(6);
            if(lngEl) lngEl.value = c.longitudeCenter.toFixed(6);
            plusEl.dispatchEvent(new Event('change'));
            alert('Plus code decoded — lat/lng populated.');
          }
        }catch(e){ console.warn(e); alert('Could not decode plus code.'); }
      }
      if(decodeBtn) decodeBtn.addEventListener('click', decodePlus);
      if(plusEl) plusEl.addEventListener('blur', function(){ if(this.value && this.value.indexOf('+')!==-1) decodePlus(); });
    })();
  </script>
  @endsection
</div>
@endsection
