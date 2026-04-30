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
    <div id="categoryWrapper" class="mb-3">
      <label class="form-label">Category (optional)</label>
      <select name="category" id="categorySelect" class="form-select">
        <option value="">-- No category --</option>
        @foreach($storeCats as $cname => $items)
          <option value="{{ $cname }}" {{ (old('category', $location->category) == $cname) ? 'selected' : '' }}>{{ $cname }}</option>
        @endforeach
      </select>
    </div>
    <input type="hidden" name="item" value="{{ old('item', $location->tags ?? '') }}">
    <div class="mb-3">
      <label class="form-label">Display Type</label>
      <div class="btn-group" role="group" aria-label="Display type">
        <button type="button" class="btn btn-outline-secondary division-btn" data-division="livelihood">Livelihood</button>
        <button type="button" class="btn btn-outline-secondary division-btn" data-division="enterprise">Enterprise</button>
      </div>
      <input type="hidden" name="division" id="divisionInput" value="{{ old('division', ($location->lat && $location->lng) ? 'enterprise' : 'livelihood') }}">
      <div class="small text-muted mt-2">Choose how this store appears. Enterprise stores should include Latitude and Longitude.</div>
    </div>
    <div class="mb-3">
      <label class="form-label">Place (CabStop)</label>
      <select name="place" class="form-select">
        <option value="">-- Select place (optional) --</option>
        @if(!empty($cabstops) && count($cabstops))
            @foreach($cabstops as $c)
                <option value="{{ $c->key ?? $c->label }}" {{ (old('place', $location->place ?? '') == ($c->key ?? $c->label)) ? 'selected' : '' }}>{{ $c->label }}</option>
            @endforeach
        @endif
      </select>
      <div class="small text-muted">Associate this store with a CabStop place to show it when the place is selected.</div>
    </div>

    <div class="mb-3">
      <label class="form-label">Store Type</label>
      <select name="store_type" class="form-select">
        <option value="">-- Select type --</option>
        <option value="food" {{ (old('store_type', $location->store_type ?? '') == 'food') ? 'selected' : '' }}>Food</option>
        <option value="non_food" {{ (old('store_type', $location->store_type ?? '') == 'non_food') ? 'selected' : '' }}>Non-food</option>
      </select>
    </div>
    

    <div class="mb-3">
      <label class="form-label">Name</label>
      <input name="name" class="form-control" value="{{ old('name', $location->name) }}" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Owner's Name <small class="text-muted">(admin only)</small></label>
      <input name="owner_name" class="form-control" value="{{ old('owner_name', $location->owner_name ?? '') }}" placeholder="Owner's full name">
    </div>
    <div class="mb-3">
      <label class="form-label">Status</label>
      <select name="status" class="form-select">
        <option value="">-- Select status (optional) --</option>
        <option value="regular" {{ (old('status', $location->status ?? '') == 'regular') ? 'selected' : '' }}>Regular</option>
        <option value="ongoing" {{ (old('status', $location->status ?? '') == 'ongoing') ? 'selected' : '' }}>Ongoing</option>
        <option value="seasonal" {{ (old('status', $location->status ?? '') == 'seasonal') ? 'selected' : '' }}>Seasonal</option>
      </select>
      <div class="small text-muted">Regular (green) · Ongoing (blue) · Seasonal (red)</div>
    </div>
    <div class="mb-3">
      <label class="form-label">Address</label>
      <input name="address" class="form-control" value="{{ old('address', $location->address) }}">
    </div>
    <div id="coordsGroup" class="row" style="{{ old('division', ($location->lat && $location->lng) ? 'enterprise' : 'livelihood') === 'enterprise' ? '' : 'display:none' }}">
      <div class="col-md-6 mb-3">
        <label class="form-label">Latitude</label>
        <input name="lat" class="form-control" value="{{ old('lat', $location->lat) }}">
      </div>
      <div class="col-md-6 mb-3">
        <label class="form-label">Longitude</label>
        <input name="lng" class="form-control" value="{{ old('lng', $location->lng) }}">
      </div>
    </div>
    
    <div id="mapUrlWrapper" class="mb-3">
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
    @include('partials.back-button', ['url' => route('admin.store_locations.index'), 'label' => 'Cancel', 'class' => 'btn-link'])
  </form>
  </div>

  @section('scripts')
  <script>
    (function(){
      const divInput = document.getElementById('divisionInput');
      const buttons = document.querySelectorAll('.division-btn');
      const coordsGroup = document.getElementById('coordsGroup');
      const form = document.querySelector('form');

      function setDivision(div){
        if(!divInput) return;
        divInput.value = div;
        buttons.forEach(b => b.classList.toggle('active', b.dataset.division === div));
        if(coordsGroup) coordsGroup.style.display = (div === 'enterprise') ? '' : 'none';
        const catWrap = document.getElementById('categoryWrapper');
        const mapWrap = document.getElementById('mapUrlWrapper');
        if(catWrap) catWrap.style.display = (div === 'enterprise') ? '' : 'none';
        if(mapWrap) mapWrap.style.display = (div === 'enterprise') ? '' : 'none';
      }

      buttons.forEach(b => b.addEventListener('click', function(){ setDivision(this.dataset.division); }));
      // initialize using current value
      setDivision(divInput ? divInput.value || 'livelihood' : 'livelihood');

      if(form){
        form.addEventListener('submit', function(e){
          const isEnterprise = (divInput && divInput.value === 'enterprise');
          if(isEnterprise){
            const lat = (document.querySelector('input[name="lat"]') || {}).value || '';
            const lng = (document.querySelector('input[name="lng"]') || {}).value || '';
            if(!lat.trim() || !lng.trim()){
              e.preventDefault();
              alert('Enterprise stores must include Latitude and Longitude. Please enter coordinates or choose Livelihood.');
              return false;
            }
          }
        });
      }
    })();
  </script>
  @endsection
</div>
@endsection
