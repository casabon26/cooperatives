@extends('layouts.app')

@section('content')
<div class="container py-4">
  <style>
    .status-dot { display:inline-block; width:12px; height:12px; border-radius:50%; margin-right:8px; vertical-align:middle; border:1px solid rgba(0,0,0,0.06); }
    .status-seasonal { background:#dc2626; }
    .status-regular { background:#16a34a; }
    .status-ongoing { background:#3b82f6; }
    .status-none { background:transparent; border:1px solid #e6e6e6; }
    .color-legend { position: fixed; right: 16px; left: auto; top: 120px; z-index: 1200; background: #fff; border: 1px solid #eee; padding: 8px; border-radius:8px; box-shadow: 0 6px 18px rgba(15,23,42,0.06); min-width:140px; transition: all .12s ease; overflow: hidden; }
    .color-legend.collapsed { padding: 6px 10px; min-width: 130px; max-height: 44px; }
    .color-legend .legend-header { font-weight:600; cursor:pointer; display:flex; align-items:center; justify-content:space-between; gap:8px; }
    .color-legend .legend-content { display:block; margin-top:8px; }
    .color-legend.collapsed .legend-content { display:none; }
    .color-legend .caret { transition: transform .12s ease; font-size: 0.85rem; color:#6b7280; margin-left:8px }
    .color-legend .item { cursor:pointer; padding:6px 6px; display:flex; align-items:center; gap:8px; border-radius:6px; }
    .color-legend .item:hover { background:#f8fafc; }
    .color-legend .item.active { background:#f1f5f9; }
    .color-legend .item small { color:#374151; }
    @media (max-width: 992px) { .color-legend { position: static; margin-bottom: .75rem; max-width:100%; } .color-legend.collapsed { max-height: none; padding:8px; } }
  </style>
    <div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Manage Cabstop</h3>
    <div>
      <a href="{{ route('admin.store_locations.create') }}" class="btn btn-primary me-2" target="_self">Add Store</a>
      <a href="{{ route('admin.select_lists.create', ['group' => 'cabstop']) }}" class="btn btn-outline-primary me-2" target="_self">Add CabStop</a>
    </div>
  </div>

  <div class="row mb-3">
    <div class="col-12">

  {{-- Inline success alert removed (use global popup/toast instead) --}}

  <!-- Filter Controls -->
  <div class="card mb-3">
    <div class="card-body">
      <form method="GET" action="{{ route('admin.store_locations.index') }}" class="row g-2">
        <div class="col-md-4">
          <label class="form-label small mb-1">Filter by CabStop</label>
          <select name="place" class="form-select form-select-sm">
            <option value="">All CabStops</option>
            @php
              try {
                if (\Schema::hasTable('select_list_items')) {
                  $cabstops = \App\Models\SelectListItem::where('group','cabstop')->where('active', true)->orderBy('label')->get();
                  foreach($cabstops as $c) {
                    $selected = (request()->query('place') == ($c->key ?? $c->label)) ? 'selected' : '';
                    echo '<option value="' . ($c->key ?? $c->label) . '" ' . $selected . '>' . $c->label . '</option>';
                  }
                }
              } catch(\Throwable $e) {}
            @endphp
          </select>
        </div>
        <div class="col-md-4">
          <label class="form-label small mb-1">Filter by Store Type</label>
          <select name="store_type" class="form-select form-select-sm">
            <option value="">All Types</option>
            <option value="food" {{ request()->query('store_type') === 'food' ? 'selected' : '' }}>Food</option>
            <option value="non_food" {{ request()->query('store_type') === 'non_food' ? 'selected' : '' }}>Non-food</option>
          </select>
        </div>
        <div class="col-md-4 d-flex align-items-end">
          <button type="submit" class="btn btn-sm btn-outline-primary me-2 w-100">Filter</button>
          <a href="{{ route('admin.store_locations.index') }}" class="btn btn-sm btn-outline-secondary w-100">Clear</a>
        </div>
      </form>
    </div>
  </div>

  <!-- Color legend (click header to expand; right side) -->
  <div id="colorLegend" class="color-legend collapsed" aria-expanded="false">
    <div class="legend-header" role="button" tabindex="0" aria-controls="colorLegendContent" aria-expanded="false">
      <span>Status
      </span>
      <span class="caret">▾</span>
    </div>
    <div id="colorLegendContent" class="legend-content" hidden>
      <div class="item active" data-filter="all"><span class="status-dot status-none"></span><small>All</small></div>
      <div class="item" data-filter="seasonal"><span class="status-dot status-seasonal"></span><small>Seasonal</small></div>
      <div class="item" data-filter="regular"><span class="status-dot status-regular"></span><small>Regular</small></div>
      <div class="item" data-filter="ongoing"><span class="status-dot status-ongoing"></span><small>Ongoing</small></div>
    </div>
  </div>

  <div class="card">
    <div class="card-body p-0">
      <table class="table mb-0">
        <thead>
          <tr>
            <th>Store Name</th>
            <th>CabStop</th>
            <th>Type</th>
            <th>Division</th>
            <th>Address</th>
            <th>Lat / Lng</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          @forelse($locations as $loc)
            <tr data-status="{{ strtolower($loc->status ?? '') }}">
              <td><span class="status-dot {{ $loc->status ? 'status-'.e($loc->status) : 'status-none' }}" aria-hidden="true"></span> {{ $loc->name }}</td>
              
              <td>
                <span class="badge bg-info text-dark">{{ $loc->place ?? 'N/A' }}</span>
              </td>
              <td>
                @if($loc->store_type)
                  @if($loc->store_type === 'food')
                    <span class="badge bg-success">Food</span>
                  @elseif($loc->store_type === 'non_food')
                    <span class="badge bg-secondary">Non-food</span>
                  @else
                    <span class="badge bg-warning">{{ $loc->store_type }}</span>
                  @endif
                @else
                  <span class="text-muted small">-</span>
                @endif
              </td>
              <td>
                @if($loc->lat && $loc->lng)
                  <span class="badge bg-danger">Enterprise Map</span>
                @else
                  <span class="badge bg-info text-dark">Livelihood</span>
                @endif
              </td>
              <td class="text-muted small">{{ $loc->address }}</td>
              <td class="small">{{ $loc->lat ? round($loc->lat, 4) : '-' }} / {{ $loc->lng ? round($loc->lng, 4) : '-' }}</td>
              <td class="text-end">
                <a href="{{ route('admin.store_locations.edit', $loc) }}" class="btn btn-sm btn-outline-secondary" target="_self">Edit</a>
                <button type="button" class="btn btn-sm btn-outline-info view-store-btn" data-store='@json($loc)' data-edit-url="{{ route('admin.store_locations.edit', $loc) }}">View</button>
                <form method="POST" action="{{ route('admin.store_locations.destroy', $loc) }}" style="display:inline-block" data-confirm="Delete location?">
                  @csrf
                  @method('DELETE')
                  <button class="btn btn-sm btn-danger">Delete</button>
                </form>
              </td>
            </tr>
          @empty
            <tr><td colspan="7" class="text-center text-muted py-3">No store locations found. <a href="{{ route('admin.store_locations.create') }}">Add one now</a></td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
  </div> <!-- /.col-md-8 -->
  </div> <!-- /.row -->
  </div> <!-- /.container -->
<!-- Add Store modal (opens centered when clicking Add Store) -->
 


</div>
<!-- Store details modal (admin) -->
<div class="modal fade" id="storeModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Store Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="storeModalBody"></div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
  function escapeHtml(str){
    if(str === null || str === undefined) return '';
    return String(str)
      .replace(/&/g,'&amp;')
      .replace(/</g,'&lt;')
      .replace(/>/g,'&gt;')
      .replace(/"/g,'&quot;')
      .replace(/'/g,'&#39;')
      .replace(/\n/g,'<br>');
  }

  document.querySelectorAll('.view-store-btn').forEach(function(btn){
    btn.addEventListener('click', function(){
      var json = btn.getAttribute('data-store') || '{}';
      var editUrl = btn.getAttribute('data-edit-url') || '#';
      var loc;
      try{ loc = JSON.parse(json); } catch(e){ loc = {}; }

      var statusDot = '<span class="status-dot ' + (loc.status ? 'status-'+ loc.status : 'status-none') + '" aria-hidden="true" style="margin-right:8px"></span>';
      var icon = loc.icon_url ? '<div style="max-width:110px"><img src="'+ escapeHtml(loc.icon_url) +'" alt="" style="max-width:100%;max-height:96px;object-fit:contain;border-radius:6px;border:1px solid #eee;padding:6px;background:#fff"></div>' : '';
      var division = (loc.lat && loc.lng) ? '<span class="badge bg-danger">Enterprise Map</span>' : '<span class="badge bg-info text-dark">Livelihood</span>';
      var typeLabel = loc.store_type ? (loc.store_type === 'food' ? '<span class="badge bg-success">Food</span>' : (loc.store_type === 'non_food' ? '<span class="badge bg-secondary">Non-food</span>' : '<span class="badge bg-warning">'+ escapeHtml(loc.store_type) +'</span>')) : '<span class="text-muted small">-</span>';

      var html = '<div class="d-flex gap-3 align-items-start">'
        + icon
        + '<div class="flex-grow-1">'
          + '<h4 class="mb-1">' + statusDot + escapeHtml(loc.name || '') + '</h4>'
          + '<div class="small text-muted mb-2">' + escapeHtml(loc.place || '') + ' &middot; ' + typeLabel + '</div>'
          + '<div>' + (loc.description ? escapeHtml(loc.description) : '<span class="text-muted">No description</span>') + '</div>'
          + '<div class="mt-2 small text-muted">Address: ' + (loc.address ? escapeHtml(loc.address) : '-') + '</div>'
          + ((loc.lat && loc.lng) ? ('<div class="small text-muted">Lat / Lng: ' + (loc.lat ? parseFloat(loc.lat).toFixed(6) : '-') + ' / ' + (loc.lng ? parseFloat(loc.lng).toFixed(6) : '-') + '</div>') : '')
          + ((loc.lat && loc.lng && loc.map_url) ? ('<div class="mt-2"><a href="'+ escapeHtml(loc.map_url) +'" target="_blank" rel="noopener">Open map</a></div>') : '')
          + (loc.owner_name ? '<div class="mt-2"><strong>Owner:</strong> ' + escapeHtml(loc.owner_name) + '</div>' : '')
        + '</div>'
      + '</div>';

      html += '<div class="mt-3 text-end"><a href="'+ escapeHtml(editUrl) +'" class="btn btn-sm btn-primary">Edit</a></div>';

      var body = document.getElementById('storeModalBody');
      if(body) body.innerHTML = html;
      try{ new bootstrap.Modal(document.getElementById('storeModal')).show(); } catch(e){}
    });
  });
  </script>

  <script>
  document.addEventListener('DOMContentLoaded', function(){
    var legend = document.getElementById('colorLegend');
    if(!legend) return;
    var header = legend.querySelector('.legend-header');
    var content = document.getElementById('colorLegendContent');
    var items = legend.querySelectorAll('.item');
    var rowsFn = function(){ return document.querySelectorAll('table tbody tr'); };

    function collapseLegend(){
      legend.classList.add('collapsed'); legend.classList.remove('expanded');
      if(content) content.hidden = true;
      header.setAttribute('aria-expanded','false');
      legend.setAttribute('aria-expanded','false');
      var caret = legend.querySelector('.caret'); if(caret) caret.textContent = '▾';
    }
    function expandLegend(){
      legend.classList.remove('collapsed'); legend.classList.add('expanded');
      if(content) content.hidden = false;
      header.setAttribute('aria-expanded','true');
      legend.setAttribute('aria-expanded','true');
      var caret = legend.querySelector('.caret'); if(caret) caret.textContent = '▴';
    }

    header.addEventListener('click', function(){ if(legend.classList.contains('expanded')) collapseLegend(); else expandLegend(); });
    header.addEventListener('keydown', function(e){ if(e.key === 'Enter' || e.key === ' '){ e.preventDefault(); header.click(); } });

    function initLegend(){ if(window.innerWidth < 992) expandLegend(); else collapseLegend(); }
    initLegend();
    window.addEventListener('resize', initLegend);

    if(!items || !items.length) return;
    items.forEach(function(it){
      it.addEventListener('click', function(){
        var filter = it.getAttribute('data-filter') || 'all';
        items.forEach(function(i){ i.classList.remove('active'); });
        it.classList.add('active');
        var rs = rowsFn();
        rs.forEach(function(r){
          var st = (r.getAttribute('data-status') || '').toLowerCase();
          if(filter === 'all' || (st && st === filter)) { r.style.display = ''; }
          else { r.style.display = 'none'; }
        });
        if(window.innerWidth >= 992) collapseLegend();
      });
    });
  });
  </script>
@endsection

@endsection
