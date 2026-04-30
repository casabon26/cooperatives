@extends('layouts.app')

@section('content')
<div class="container py-4">
  <h3>Add Store</h3>
  @if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
  @endif
  @if($errors->any())
    <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach</ul></div>
  @endif
  <form method="POST" action="{{ route('admin.store_locations.store') }}" target="_self">
    @csrf
    <div class="row">
      <div class="col-md-6">
        @php $storeCats = @json_decode(@file_get_contents(resource_path('data/store_categories.json')), true) ?: []; @endphp
        <div id="categoryWrapper" class="mb-3">
          <label class="form-label">Category (optional)</label>
          <select name="category" id="categorySelect" class="form-select">
            <option value="">-- No category --</option>
            @foreach($storeCats as $cname => $items)
              <option value="{{ $cname }}" {{ old('category') == $cname ? 'selected' : '' }}>{{ $cname }}</option>
            @endforeach
          </select>
        </div>

        <div class="mb-3">
          <label class="form-label">Display Type</label>
          <div class="btn-group" role="group" aria-label="Display type">
            <button type="button" class="btn btn-outline-secondary division-btn active" data-division="livelihood">Livelihood</button>
            <button type="button" class="btn btn-outline-secondary division-btn" data-division="enterprise">Enterprise</button>
          </div>
          <input type="hidden" name="division" id="divisionInput" value="{{ old('division', 'livelihood') }}">
          <div class="small text-muted mt-2">Choose how this store appears. Enterprise stores should include Latitude and Longitude.</div>
        </div>

        <div class="mb-3">
          <label class="form-label">Store Name *</label>
          <input name="name" class="form-control" required>
        </div>

        <div class="mb-3">
          <label class="form-label">Owner's Name <small class="text-muted">(admin only)</small></label>
          <input name="owner_name" class="form-control" value="{{ old('owner_name', '') }}" placeholder="Owner's full name">
        </div>

        <div class="mb-3">
          <label class="form-label">Status</label>
          <select name="status" class="form-select">
            <option value="">-- Select status (optional) --</option>
            <option value="regular" {{ old('status') === 'regular' ? 'selected' : '' }}>Regular</option>
            <option value="ongoing" {{ old('status') === 'ongoing' ? 'selected' : '' }}>Ongoing</option>
            <option value="seasonal" {{ old('status') === 'seasonal' ? 'selected' : '' }}>Seasonal</option>
          </select>
          <div class="small text-muted">Regular (green) · Ongoing (blue) · Seasonal (red)</div>
        </div>

        <div class="mb-3">
          <label class="form-label">Store Type *</label>
          <select name="store_type" class="form-select" required>
            <option value="">-- Select type --</option>
            <option value="food" {{ request()->get('store_type') === 'food' ? 'selected' : '' }}>Food</option>
            <option value="non_food" {{ request()->get('store_type') === 'non_food' ? 'selected' : '' }}>Non-food</option>
          </select>
        </div>

        <div class="mb-3">
          <label class="form-label">Place (CabStop)</label>
          <select name="place" class="form-select">
            <option value="">-- Select place (optional) --</option>
            @if(!empty($cabstops) && count($cabstops))
                @foreach($cabstops as $c)
                    <option value="{{ $c->key ?? $c->label }}" {{ (request()->get('place') == ($c->key ?? $c->label)) ? 'selected' : '' }}>{{ $c->label }}</option>
                @endforeach
            @endif
          </select>
        </div>
      </div>

      <div class="col-md-6">
        <div class="mb-3">
          <label class="form-label">Address</label>
          <input name="address" class="form-control" placeholder="e.g., 123 Main Street">
        </div>

        <div id="mapUrlWrapper" class="mb-3">
          <label class="form-label">Google Maps Link (optional)</label>
          <input name="map_url" class="form-control" placeholder="https://www.google.com/maps/...">
          <div class="small text-muted">Paste a Google Maps link to auto-extract coordinates.</div>
        </div>

        <div class="mb-3">
          <label class="form-label">Description</label>
          <textarea name="description" class="form-control" rows="3" placeholder="Add details about this store..."></textarea>
        </div>
      </div>
    </div>

    <div id="coordsGroup" class="row" style="{{ old('division') === 'enterprise' ? '' : 'display:none' }}">
      <div class="col-md-6 mb-3">
        <label class="form-label">Latitude (optional)</label>
        <input name="lat" class="form-control" placeholder="e.g. 14.333" value="{{ old('lat', '') }}">
      </div>
      <div class="col-md-6 mb-3">
        <label class="form-label">Longitude (optional)</label>
        <input name="lng" class="form-control" placeholder="e.g. 121.133" value="{{ old('lng', '') }}">
      </div>
    </div>
    <input type="hidden" name="item" value="">
    <div class="small text-muted mb-3">Note: If both Latitude and Longitude are left blank this store will appear in the Livelihood CabStop listings. If coordinates are provided it will appear on the Enterprise Map instead.</div>
    
    <div class="mb-3">
      <button type="submit" class="btn btn-primary btn-lg">Save Store</button>
      @include('partials.back-button', ['url' => route('admin.store_locations.index'), 'label' => 'Cancel', 'class' => 'btn-link ms-2'])
    </div>
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
      // initialize
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
