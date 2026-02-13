@extends('layouts.app')

@section('content')
<div class="container py-4">
  <h3>Add Store Location</h3>
  @if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
  @endif
  @if($errors->any())
    <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach</ul></div>
  @endif
  <form method="POST" action="{{ route('admin.store_locations.store') }}" target="_self">
    @csrf
    @php $storeCats = @json_decode(@file_get_contents(resource_path('data/store_categories.json')), true) ?: []; @endphp
    <div class="mb-3">
      <label class="form-label">Category</label>
      <select name="category" id="categorySelect" class="form-select">
        <option value="">-- Select category --</option>
        @foreach($storeCats as $cname => $items)
          <option value="{{ $cname }}" {{ (request()->get('category') == $cname) ? 'selected' : '' }}>{{ $cname }}</option>
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
      <input name="name" class="form-control" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Address</label>
      <input name="address" class="form-control">
    </div>
    <input type="hidden" name="lat">
    <input type="hidden" name="lng">
    <div class="mb-3">
      <label class="form-label">Google Maps Link (optional)</label>
      <input name="map_url" class="form-control" placeholder="https://www.google.com/maps/...">
      <div class="small text-muted">Paste a Google Maps link and the system will try to extract coordinates.</div>
    </div>
    
    <div class="mb-3">
      <label class="form-label">Description</label>
      <textarea name="description" class="form-control" rows="3"></textarea>
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
      const preSelectedItem = {!! json_encode(request()->get('item') ?: old('item', ''), JSON_UNESCAPED_UNICODE) !!};

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
      // Preselect from query string if provided
      if(!catSel.value && (new URLSearchParams(window.location.search)).get('category')){
        const pre = (new URLSearchParams(window.location.search)).get('category');
        catSel.value = pre; populateItemsFor(pre);
      }
      // if item chosen, fill name
      if(itemSel) itemSel.addEventListener('change', function(){ const o = this.selectedOptions[0]; if(o){ nameInput.value = o.value; } });
    })();
  </script>
  @endsection
</div>
@endsection
