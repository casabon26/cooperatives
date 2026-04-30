@extends('layouts.app')

@section('content')
<div class="py-4">
  <div class="container">
    <h1 class="h3">Enterprise Map</h1>
    <p class="text-muted">Below is the map for the provided location. Interact with the map to zoom or open in Google Maps.</p>

    <div class="card">
      <div class="card-body">
        <style>
          /* Theme-aligned buttons for categories/items */
          .category-btn {
            border-radius: 9999px;
            padding: 6px 12px;
            color: #7f1d1d;
            border-color: rgba(var(--primary-r), 0.18);
            background: linear-gradient(180deg, rgba(var(--primary-r), 0.04), rgba(255,255,255,0));
            font-weight:600;
          }
          .category-btn:hover, .category-btn:focus { background: linear-gradient(180deg, rgba(var(--primary-r), 0.09), rgba(var(--primary-r), 0.02)); color:#7f1d1d; }
          .category-btn.active {
            background: linear-gradient(180deg,var(--primary),var(--primary-2));
            color: #fff; border-color: rgba(var(--primary-r), 0.28);
            box-shadow: 0 8px 20px rgba(var(--primary-r), 0.06);
          }

          .item-btn {
            border-radius: 9999px;
            padding: 6px 14px;
            color: #7f1d1d;
            border-color: rgba(var(--primary-r), 0.12);
            background: rgba(249,246,246,0.6);
            font-weight:600;
          }
          .item-btn:hover, .item-btn:focus { background: linear-gradient(180deg, rgba(var(--primary-r), 0.06), rgba(255,255,255,0.98)); color:#7f1d1d; }
        </style>
        @php
          $storeCats = @json_decode(@file_get_contents(resource_path('data/store_categories.json')), true) ?: [];
        @endphp
        <div class="mb-3">
          <label class="form-label">Categories</label>
          <div class="d-flex flex-wrap gap-2">
            @foreach($storeCats as $cname => $items)
              <button class="btn btn-outline-danger btn-sm category-btn" data-category="{{ $cname }}">{{ $cname }}</button>
            @endforeach
          </div>
        </div>

        <div id="categoryItems" class="mb-3" style="display:none">
          <label class="form-label d-block">Items</label>
          <div id="itemsList" class="d-flex flex-wrap gap-2"></div>
        </div>

        <div id="mapWrap">
          <div id="map" style="width:100%;height:420px;border-radius:8px;overflow:hidden"></div>
        </div>

      </div>
    </div>
  </div>
</div>
@endsection
@section('scripts')
@if(!empty($mapsKey ?? env('GOOGLE_MAPS_API_KEY')))
<script src="https://cdn.jsdelivr.net/npm/open-location-code@1.0.4/open-location-code.min.js"></script>
<script>
  let map, geocoder, mainMarker;
  // Custom SVG icon (red pin) as data URL (shared)
  const svg = `<?xml version="1.0" encoding="UTF-8"?><svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 32' width='36' height='48'><path fill='#B82132' d='M12 2C8 2 5 5 5 9c0 6.6 7 13 7 13s7-6.4 7-13c0-4-3-7-7-7z'/><circle cx='12' cy='9' r='3' fill='#fff'/></svg>`;
  const iconUrl = 'data:image/svg+xml;charset=UTF-8,' + encodeURIComponent(svg);

  function initMap(){
    geocoder = new google.maps.Geocoder();
    const defaultCenter = { lat: 14.333, lng: 121.133 };
    map = new google.maps.Map(document.getElementById('map'), { center: defaultCenter, zoom: 14 });
    mainMarker = new google.maps.Marker({ map: map });

    // If a search input exists (legacy), wire autocomplete — otherwise skip
    const input = document.getElementById('mapSearch');
    if(input){
      try{
        const autocomplete = new google.maps.places.Autocomplete(input);
        autocomplete.bindTo('bounds', map);
        autocomplete.addListener('place_changed', ()=>{
          const place = autocomplete.getPlace();
          if(place.geometry && place.geometry.location){
            map.panTo(place.geometry.location);
            map.setZoom(15);
            mainMarker.setPosition(place.geometry.location);
          } else if(place.name){
            geocodeQuery(place.name);
          }
        });

        input.addEventListener('keydown', (e)=>{ if(e.key === 'Enter'){ e.preventDefault(); const q = e.target.value.trim(); if(q) geocodeQuery(q); }});
      }catch(e){ console.warn('Autocomplete not available', e); }
    }

    // Load persistent markers from server (initially all)
    loadMarkers();


  // Marker management
  const MAP_MARKERS = [];
  function clearMapMarkers(){
    MAP_MARKERS.forEach(m => { try{ m.setMap(null); }catch(_){} });
    MAP_MARKERS.length = 0;
  }

  function addMarkersFromData(data){
    clearMapMarkers();
    if(!Array.isArray(data) || !data.length) return;
    const bounds = new google.maps.LatLngBounds();
    data.forEach(s => {
      try{
        const lat = parseFloat(s.lat), lng = parseFloat(s.lng);
        if(isNaN(lat) || isNaN(lng)) return;
        const pos = new google.maps.LatLng(lat, lng);
        const marker = new google.maps.Marker({ position: pos, map: map, title: s.name, icon: { url: iconUrl, scaledSize: new google.maps.Size(36,48) } });
        const infow = new google.maps.InfoWindow({ content: `<div style="min-width:180px"><strong>${escapeHtml(s.name)}</strong><div class=\"small text-muted\">${escapeHtml(s.address)}</div><div class=\"mt-2\">${escapeHtml(s.description||'')}</div></div>` });
        marker.addListener('click', ()=> infow.open(map, marker));
        MAP_MARKERS.push(marker);
        bounds.extend(pos);
      }catch(e){ console.warn('Invalid store entry', s, e); }
    });
    try{ map.fitBounds(bounds); }catch(e){ console.warn(e); }
  }

    function loadMarkers(filter){
    let url = '/api/store-locations';
    if(filter && filter.category) url += '?category=' + encodeURIComponent(filter.category) + (filter.tag ? '&tag=' + encodeURIComponent(filter.tag) : '');
    // Only show markers with coordinates on the Enterprise Map
    url += (url.indexOf('?') === -1 ? '?' : '&') + 'map=enterprise';
    fetch(url).then(r=> r.ok ? r.json() : Promise.reject(r)).then(data=>{ addMarkersFromData(data); }).catch(err=>{ console.warn('Could not load store markers', err); });
  }
    // Try to locate the plus code on load using local decode first to avoid geocoder calls
    const initial = '74GF+XH Cabuyao City, Laguna';
    const decoded = tryDecodePlus(initial);
    if(decoded){
      const loc = new google.maps.LatLng(decoded.lat, decoded.lng);
      map.panTo(loc); map.setZoom(15); mainMarker.setPosition(loc);
    } else {
      geocodeQuery(initial);
    }
  }

  // Simple HTML escape for popup content
  function escapeHtml(s){ return String(s||'').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c])); }

  // Try decode plus code using Open Location Code (no network)
  function tryDecodePlus(q){
    try{
      if(!q || q.indexOf('+') === -1) return null;
      const c = OpenLocationCode.decode(q);
      return { lat: c.latitudeCenter, lng: c.longitudeCenter };
    }catch(e){ return null; }
  }

  // Geocode with client-side caching (TTL 7 days) and Nominatim fallback
  const G_CACHE_KEY = 'gmaps_geocode_cache_v1';
  const G_TTL = 7 * 24 * 60 * 60 * 1000;
  function geocodeQuery(q){
    if(!q) return;
    // decode plus code locally first
    const dec = tryDecodePlus(q);
    if(dec){ const loc = new google.maps.LatLng(dec.lat, dec.lng); map.panTo(loc); map.setZoom(15); mainMarker.setPosition(loc); return; }

    try{
      const cache = JSON.parse(localStorage.getItem(G_CACHE_KEY) || '{}');
      const now = Date.now();
      if(cache[q] && (now - cache[q].t) < G_TTL){
        const p = cache[q].p;
        const loc = new google.maps.LatLng(p.lat, p.lng);
        map.panTo(loc); map.setZoom(15); mainMarker.setPosition(loc); return;
      }
    }catch(e){}

    geocoder.geocode({ address: q }, function(results, status){
      if(status === 'OK' && results[0]){
        const loc = results[0].geometry.location;
        map.panTo(loc);
        map.setZoom(15);
        mainMarker.setPosition(loc);
        try{ const cache = JSON.parse(localStorage.getItem(G_CACHE_KEY) || '{}'); cache[q] = { t: Date.now(), p: { lat: loc.lat(), lng: loc.lng() } }; localStorage.setItem(G_CACHE_KEY, JSON.stringify(cache)); }catch(e){}
      } else {
        console.warn('Geocode failed', status, results);
        // If Google geocoding failed (billing/API), fallback to Nominatim
        nominatimSearch(q);
      }
    });
  }

  function nominatimSearch(q){
    const url = 'https://nominatim.openstreetmap.org/search?format=json&limit=1&q=' + encodeURIComponent(q);
    fetch(url, { headers: { 'Accept': 'application/json' } })
      .then(r => r.json())
      .then(data => {
        if(data && data.length){
          const d = data[0];
          const lat = parseFloat(d.lat), lon = parseFloat(d.lon);
          const loc = new google.maps.LatLng(lat, lon);
          map.panTo(loc); map.setZoom(15); mainMarker.setPosition(loc);
          try{ const cache = JSON.parse(localStorage.getItem(G_CACHE_KEY) || '{}'); cache[q] = { t: Date.now(), p: { lat: lat, lng: lon } }; localStorage.setItem(G_CACHE_KEY, JSON.stringify(cache)); }catch(e){}
        } else {
          alert('Location not found or geocoding failed.');
        }
      }).catch(err=>{ console.warn('Nominatim fallback failed', err); alert('Location not found or geocoding failed.'); });
  }
</script>
<script src="https://maps.googleapis.com/maps/api/js?key={{ $mapsKey ?? env('GOOGLE_MAPS_API_KEY') }}&libraries=places&callback=initMap" async defer></script>
@else
<script>
  // No Google Maps API key: render a simple Google Maps iframe using the plus code as default.
  (function(){
    const defaultQuery = '74GF+XH Cabuyao City, Laguna';
    const q = encodeURIComponent(defaultQuery);
    const iframe = document.createElement('iframe');
    iframe.width = '100%';
    iframe.height = '420';
    iframe.style.border = '0';
    iframe.style.borderRadius = '8px';
    iframe.loading = 'lazy';
    iframe.referrerPolicy = 'no-referrer-when-downgrade';
    iframe.src = 'https://www.google.com/maps?q=' + q + '&z=15&output=embed';
    const mapEl = document.getElementById('map');
    if(mapEl){ mapEl.innerHTML = ''; mapEl.appendChild(iframe); }

    // When search is used, open Google Maps search in a new tab (no API key required)
    function openInMaps(query){
      if(!query) return;
      const url = 'https://www.google.com/maps/search/?api=1&query=' + encodeURIComponent(query);
      window.open(url, '_blank');
    }
    const _btn = document.getElementById('mapSearchBtn');
    const _inp = document.getElementById('mapSearch');
    if(_btn){ _btn.addEventListener('click', function(){ const qv = (_inp ? _inp.value.trim() : ''); openInMaps(qv || defaultQuery); }); }
    if(_inp){ _inp.addEventListener('keydown', function(e){ if(e.key === 'Enter'){ e.preventDefault(); const qv = e.target.value.trim(); openInMaps(qv || defaultQuery); }}); }
  })();
</script>
@endif
<script>
  // Unified search handler: prefers internal geocode (if available), otherwise opens Google Maps search in a new tab.
  (function(){
    const btn = document.getElementById('mapSearchBtn');
    const input = document.getElementById('mapSearch');
    if(!btn || !input) return;
    const defaultQuery = '74GF+XH Cabuyao City, Laguna';

    function openInMaps(q){
      const query = q && q.trim() ? q.trim() : defaultQuery;
      const url = 'https://www.google.com/maps/search/?api=1&query=' + encodeURIComponent(query);
      window.open(url, '_blank');
    }

    function handleSearch(q){
      if(!q || !q.trim()) q = defaultQuery;
      // If our initMap and geocodeQuery functions exist, use them to update the inline map
      if(typeof geocodeQuery === 'function'){
        try{ geocodeQuery(q); return; }catch(e){ console.warn('geocodeQuery failed', e); }
      }
      // If Google Places Autocomplete is available and a map exists, try to use it
      if(window.google && window.google.maps && window.google.maps.places){
        try{
          // fallback to opening maps when we can't reliably geocode
          openInMaps(q);
          return;
        }catch(e){ console.warn('Places fallback failed', e); openInMaps(q); return; }
      }
      // Default: open Google Maps search in new tab
      openInMaps(q);
    }

    btn.addEventListener('click', function(){ handleSearch(input.value); });
    input.addEventListener('keydown', function(e){ if(e.key === 'Enter'){ e.preventDefault(); handleSearch(input.value); }});
  })();
</script>

<script>
  // Category / items UI wiring
  (function(){
    const categories = {!! json_encode($storeCats ?? [], JSON_UNESCAPED_UNICODE) !!};

    const catBtns = Array.from(document.querySelectorAll('.category-btn'));
    const itemsWrap = document.getElementById('categoryItems');
    const itemsList = document.getElementById('itemsList');
    const defaultArea = 'Cabuyao City, Laguna';

    function esc(s){ return String(s||'').replace(/[&<>'"\u00A0]/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;','\u00A0':' '})[c] || ''); }
    function parseGoogleMapsUrl(u){
      try{
        if(!u) return null;
        let m = u.match(/@(-?\d+(?:\.\d+)?),\s*(-?\d+(?:\.\d+)?)/);
        if(m) return { lat: parseFloat(m[1]), lng: parseFloat(m[2]) };
        m = u.match(/[?&](?:q|ll)=(-?\d+(?:\.\d+)?),\s*(-?\d+(?:\.\d+)?)/);
        if(m) return { lat: parseFloat(m[1]), lng: parseFloat(m[2]) };
        m = u.match(/\/place\/.*\/@(-?\d+(?:\.\d+)?),(-?\d+(?:\.\d+)?)/);
        if(m) return { lat: parseFloat(m[1]), lng: parseFloat(m[2]) };
        m = u.match(/maps\/dir\/.*\/(-?\d+(?:\.\d+)?),(-?\d+(?:\.\d+)?)/);
        if(m) return { lat: parseFloat(m[1]), lng: parseFloat(m[2]) };
      }catch(e){}
      return null;
    }

    function showItemsFor(category){
      itemsList.innerHTML = '';
      const items = categories[category] || [];
      items.forEach(it =>{
        const label = (typeof it === 'string') ? it : (it.label || '');
        const mapUrl = (typeof it === 'object' && (it.map_url || it.mapUrl)) ? (it.map_url || it.mapUrl) : '';
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'btn btn-outline-danger btn-sm rounded-pill item-btn';
        btn.style.padding = '6px 12px';
        btn.dataset.category = category;
        btn.dataset.item = label;
        btn.dataset.mapurl = mapUrl;
        btn.textContent = label;
        itemsList.appendChild(btn);
      });
      itemsWrap.style.display = items.length ? '' : 'none';
    }

    catBtns.forEach(b => b.addEventListener('click', function(){
      catBtns.forEach(x=> x.classList.remove('active'));
      this.classList.add('active');
      const c = this.dataset.category;
      showItemsFor(c);
    }));

    // Delegate clicks on generated item buttons
    document.addEventListener('click', function(e){
      const btn = e.target.closest('.item-btn');
      if(!btn) return;
      const category = btn.dataset.category;
      const item = btn.dataset.item;
      const mapUrl = btn.dataset.mapurl || '';

      function showLocationOnMap(loc){
        if(!loc) return;
        const lat = parseFloat(loc.lat), lng = parseFloat(loc.lng);
        // If we have coordinates, prefer them
        if(!isNaN(lat) && !isNaN(lng)){
          try{
            if(typeof map !== 'undefined' && window.google && window.google.maps){
              try{ clearMapMarkers(); }catch(e){}
              const pos = new google.maps.LatLng(lat,lng);
              map.panTo(pos); map.setZoom(15);
              const focused = new google.maps.Marker({ position: pos, map: map, title: loc.name || item, icon: { url: iconUrl, scaledSize: new google.maps.Size(36,48) } });
              const infow = new google.maps.InfoWindow({ content: `<div style="min-width:180px"><strong>${escapeHtml(loc.name || item)}</strong><div class="small text-muted">${escapeHtml(loc.address||'')}</div></div>` });
              focused.addListener('click', ()=> infow.open(map, focused));
            } else {
              const iframe = document.querySelector('#map iframe');
              if(iframe) iframe.src = 'https://www.google.com/maps?q=' + encodeURIComponent(lat+','+lng) + '&z=15&output=embed';
            }
            return;
          }catch(e){ console.warn('Could not show location inline', e); }
        }

        // If no coords but item_map_url present, try parsing it or open
        if(loc.item_map_url){
          const parsed = parseGoogleMapsUrl(loc.item_map_url);
          if(parsed){ return showLocationOnMap(parsed); }
          if(typeof map !== 'undefined' && window.google && window.google.maps){
            try{ geocodeQuery(loc.item_map_url); return; }catch(e){}
          }
          const iframe = document.querySelector('#map iframe');
          if(iframe) { iframe.src = loc.item_map_url; return; }
          window.open(loc.item_map_url, '_blank');
          return;
        }

        // Fallback: if mapUrl provided in categories, use normal handling
        return null;
      }

      // First, try to fetch matching store location(s) for this category+item
      const apiUrl = '/api/store-locations?category=' + encodeURIComponent(category) + '&tag=' + encodeURIComponent(item);
      fetch(apiUrl).then(r => r.ok ? r.json() : Promise.reject(r)).then(list => {
        if(Array.isArray(list) && list.length){
          const loc = list[0];
          showLocationOnMap(loc);
          return;
        }

        // No location found in DB: fallback to using mapUrl from category item if provided
        if(mapUrl){
          const latlngMatch = mapUrl.match(/^\s*(-?\d+(?:\.\d+)?)\s*,\s*(-?\d+(?:\.\d+)?)\s*$/);
          if(latlngMatch){
            const lat = parseFloat(latlngMatch[1]), lng = parseFloat(latlngMatch[2]);
            if(typeof map !== 'undefined' && window.google && window.google.maps){ try{ map.panTo(new google.maps.LatLng(lat,lng)); map.setZoom(15); }catch(e){} return; }
            window.open('https://www.google.com/maps/search/?api=1&query=' + encodeURIComponent(lat+','+lng), '_blank');
            return;
          }

          if(mapUrl.indexOf('+') !== -1 && typeof geocodeQuery === 'function'){ try{ geocodeQuery(mapUrl); return; }catch(e){} }

          if(/^https?:\/\//i.test(mapUrl)){
            const parsed = parseGoogleMapsUrl(mapUrl);
            if(parsed){
              if(typeof map !== 'undefined' && window.google && window.google.maps){
                try{ map.panTo(new google.maps.LatLng(parsed.lat, parsed.lng)); map.setZoom(15); }catch(e){}
                return;
              }
              const iframe = document.querySelector('#map iframe');
              if(iframe){ iframe.src = 'https://www.google.com/maps?q=' + encodeURIComponent(parsed.lat+','+parsed.lng) + '&z=15&output=embed'; return; }
              window.open(mapUrl,'_blank');
              return;
            }
            window.open(mapUrl,'_blank');
            return;
          }

        }

        if(typeof loadMarkers === 'function'){ loadMarkers({ category: category, tag: item }); return; }
        const q = item + ' ' + defaultArea;
        window.open('https://www.google.com/maps/search/?api=1&query=' + encodeURIComponent(q), '_blank');
      }).catch(err => {
        console.warn('Lookup failed', err);
        if(mapUrl){ window.open(mapUrl, '_blank'); return; }
        if(typeof loadMarkers === 'function'){ loadMarkers({ category: category, tag: item }); return; }
        window.open('https://www.google.com/maps/search/?api=1&query=' + encodeURIComponent(item + ' ' + defaultArea), '_blank');
      });
    });

    // Show first category by default
    const first = catBtns[0]; if(first) { first.classList.add('active'); showItemsFor(first.dataset.category); }
  })();
</script>
@endsection
