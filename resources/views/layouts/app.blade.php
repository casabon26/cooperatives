<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name','Cooperative Portal') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
      body{padding-top:80px}
      /* Yahoo-like header (muted red theme) */
      .yahoo-header{background:linear-gradient(90deg,#fff5f5,#fff7f7);border-bottom:1px solid #f5d7d7;position:fixed;top:0;left:0;right:0;z-index:1030}
      .yahoo-container{max-width:1100px;margin:0 auto;padding:10px 16px;display:flex;align-items:center;gap:16px;flex-wrap:wrap}
      .yahoo-logo{font-weight:700;color:#b91c1c;font-size:28px}
      .yahoo-logo-text{display:inline}
      .search-box{flex:1 1 40%;display:flex;align-items:center;min-width:180px}
      /* Header nav replacing search: icon links and dropdown */
      .header-nav{flex:1 1 40%;display:flex;align-items:center;gap:10px;min-width:180px}
      .header-nav .nav-item{display:flex;align-items:center;gap:8px;padding:6px 8px;border-radius:8px;color:inherit;text-decoration:none}
      .header-nav .nav-item .nav-text{font-weight:600;color:#5b1b1b;transition:color .14s ease}
      .header-nav .nav-item:hover .nav-text,
      .header-nav .nav-item:focus .nav-text{color:var(--danger)}
      .header-nav .more-btn{background:transparent;border:0;padding:6px}
      /* Themed dropdown toggles and menus */
      .dropdown-toggle.nav-item,
      .header-nav .more-btn {
        background: linear-gradient(180deg, rgba(249,115,22,0.02), rgba(185,28,28,0.02));
        border: 1px solid transparent;
        padding: 6px 10px;
        border-radius: 10px;
        color: #6b1616;
        transition: background-color .12s ease, border-color .12s ease, box-shadow .12s ease;
      }
      .dropdown-toggle.nav-item:focus,
      .dropdown-toggle.nav-item:hover,
      .header-nav .more-btn:focus,
      .header-nav .more-btn:hover {
        background: linear-gradient(180deg, rgba(185,28,28,0.06), rgba(185,28,28,0.04));
        border-color: rgba(185,28,28,0.12);
        box-shadow: 0 6px 20px rgba(185,28,28,0.06);
        color: #7f1d1d;
      }
      .dropdown-menu {
        min-width: 200px;
        border-radius: 10px;
        border: 1px solid rgba(0,0,0,0.06);
        box-shadow: 0 10px 30px rgba(15,23,42,0.06);
        padding: .35rem 0;
        background: linear-gradient(180deg,#fff,#fff); /* subtle base */
      }
      .dropdown-item{
        color:#3b1a1a;
        padding:.5rem .9rem;
        font-weight:600;
      }
      .dropdown-item:hover, .dropdown-item:focus {
        background: rgba(239,68,68,0.06);
        color: #b91c1c;
      }
      .dropdown-menu .active, .dropdown-item.active {
        background: rgba(239,68,68,0.08);
        color: #b91c1c;
        font-weight:700;
      }
      .search-box input{display:none}
      .search-btn{display:none}
      .header-icons{display:flex;gap:8px;align-items:center}
      /* Profile dropdown button to consolidate account/admin actions */
      .profile-btn{width:40px;height:40px;border-radius:9999px;background:#fee2e2;display:inline-flex;align-items:center;justify-content:center;color:#991b1b;border:0}
      .profile-btn:focus{outline:2px solid rgba(185,28,28,0.12);outline-offset:2px}
      .profile-menu .dropdown-item{color:#3b1a1a}
      .profile-menu .dropdown-item:hover{background:#fff1f1}
      .profile-menu .logout-form{padding:0.35rem 1rem}
      .icon-circle{width:36px;height:36px;border-radius:9999px;background:#fee2e2;display:inline-flex;align-items:center;justify-content:center;color:#991b1b;font-weight:600}

      /* Responsive header stacking */
      @media (max-width:640px){
        .yahoo-container{padding:8px;gap:8px}
        .yahoo-logo{font-size:20px}
        .yahoo-logo-text{display:none}
        .header-nav{order:2;flex-basis:100%;width:100%;margin-top:6px}
        .header-icons{order:3;margin-top:6px}
        .icon-circle{width:32px;height:32px}
        body{padding-top:120px}
      }
    </style>
    @php
      $appCssVer = file_exists(public_path('css/app.css')) ? filemtime(public_path('css/app.css')) : time();
      $extraCssVer = file_exists(public_path('css/extra.css')) ? filemtime(public_path('css/extra.css')) : time();
      $themeCssVer = file_exists(public_path('css/theme.css')) ? filemtime(public_path('css/theme.css')) : time();
    @endphp
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ $appCssVer }}">
    <link rel="stylesheet" href="{{ asset('css/extra.css') }}?v={{ $extraCssVer }}">
    <link rel="stylesheet" href="{{ asset('css/theme.css') }}?v={{ $themeCssVer }}">
  <style>
    /* Disable hover effects in admin area */
    .admin-area .card:hover,
    .admin-area .card .card-body:hover {
      transform: none !important;
      box-shadow: none !important;
      filter: none !important;
    }
    .admin-area .card {
      transition: none !important;
    }
    /* Success flash popup styling (top-center) */
    #flash-success{
      position:fixed;
      top:72px;
      left:50%;
      transform:translateX(-50%);
      z-index:1060;
      max-width:760px;
      width:calc(100% - 48px);
      box-shadow:0 12px 40px rgba(0,0,0,0.14);
      border-radius:10px;
      opacity:0;
      transition:transform .18s cubic-bezier( .2,.9,.2,1), opacity .18s ease;
      background-clip:padding-box;
    }
    #flash-success .btn-close{margin-left:8px}
    /* Error flash styling */
    #flash-error{
      position:fixed;
      top:72px;
      left:50%;
      transform:translateX(-50%);
      z-index:1060;
      max-width:760px;
      width:calc(100% - 48px);
      box-shadow:0 12px 40px rgba(0,0,0,0.14);
      border-radius:10px;
      opacity:0;
      transition:transform .18s cubic-bezier( .2,.9,.2,1), opacity .18s ease;
      background-clip:padding-box;
    }
    #flash-error .btn-close{margin-left:8px}
    #flash-error.flash-init{opacity:0; transform:translateX(-50%) translateY(-8px) scale(.98)}
    #flash-error.flash-visible{opacity:1; transform:translateX(-50%) translateY(0) scale(1)}
    /* initial hidden state (slightly above top position) */
    #flash-success.flash-init{opacity:0; transform:translateX(-50%) translateY(-8px) scale(.98)}
    /* visible state (at top-center) */
    #flash-success.flash-visible{opacity:1; transform:translateX(-50%) translateY(0) scale(1)}

    /* Backdrop (no blur) that slightly dims the background while flash visible */
    .flash-backdrop{
      position:fixed;inset:0;background:rgba(0,0,0,0.06);opacity:0;transition:opacity .18s ease;z-index:1059;
    }
    .flash-backdrop.visible{opacity:1}
  </style>
</head>
<body class="{{ request()->is('admin*') ? 'admin-area' : '' }}">
<header class="yahoo-header" role="banner">
  <div class="yahoo-container">
    <div style="flex:0 0 auto;display:flex;align-items:center;gap:12px">
      <a href="/" style="display:flex;align-items:center;gap:12px;text-decoration:none;color:inherit">
        @if(file_exists(public_path('Logo/CCLDO.png')))
          <img src="{{ asset('Logo/CCLDO.png') }}" alt="CCLDO logo" style="height:36px;object-fit:contain">
        @endif
        <div class="yahoo-logo"><span class="yahoo-logo-text">Cooperative</span></div>
      </a>
    </div>

    <nav class="header-nav" aria-label="Main navigation">
      <a href="/" class="nav-item" title="Home">
        <span class="nav-text">Home</span>
      </a>

      <a href="/about" class="nav-item" title="About" aria-label="About">
        <span class="nav-text">About</span>
      </a>

      

      <a href="/cooperatives?per_page=34" class="nav-item" title="Cooperatives Portal">
        <span class="nav-text">Cooperatives Portal</span>
      </a>

      <div class="dropdown ms-2">
        <button class="more-btn nav-item dropdown-toggle" id="moreDropdown" data-bs-toggle="dropdown" aria-expanded="false" aria-label="More">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M3 6h18M3 12h18M3 18h18" stroke="#b91c1c" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </button>
        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="moreDropdown">
          <li><a class="dropdown-item" href="/faqs">FAQs</a></li>
          <li><a class="dropdown-item" href="/videos">Videos</a></li>
          <li><a class="dropdown-item" href="/news">News</a></li>
          <li><a class="dropdown-item" href="/store-locations">Store Locations</a></li>
        </ul>
      </div>
    </nav>

    <div class="header-icons">
      <div class="dropdown">
        <button class="profile-btn dropdown-toggle" id="profileDropdown" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Account">
          <span class="visually-hidden">Account</span>
          <svg width="18" height="18" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M8 8a3 3 0 100-6 3 3 0 000 6z" stroke="#b91c1c" stroke-width="1.2" fill="none"/><path d="M2 14s1.5-3 6-3 6 3 6 3" stroke="#b91c1c" stroke-width="1.2" fill="none" stroke-linecap="round"/></svg>
        </button>
        <ul class="dropdown-menu dropdown-menu-end profile-menu" aria-labelledby="profileDropdown">
          <li><a class="dropdown-item" href="/profile">Profile</a></li>
          <li><a class="dropdown-item" href="/settings">Settings</a></li>
          <li><hr class="dropdown-divider"></li>
          @if(session('admin_authenticated'))
            <li><a class="dropdown-item" href="/admin/panel">Admin Panel</a></li>
            <li>
              <form method="POST" action="/admin/logout" class="logout-form">
                @csrf
                <button type="submit" class="btn btn-sm btn-danger w-100">Logout</button>
              </form>
            </li>
          @else
            <li><a class="dropdown-item" href="/admin/login">Admin Login</a></li>
          @endif
        </ul>
      </div>
    </div>
  </div>
</header>
<main class="container" role="main">
  @if(session('success'))
    <div id="flash-success" class="alert alert-success alert-dismissible fade show global-flash" role="alert">
      <div class="alert-icon" aria-hidden="true">✓</div>
      <div class="alert-body">
        <span class="alert-title">Success</span>
        <span class="alert-message">{{ session('success') }}</span>
      </div>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif
  @if(session('error'))
    <div id="flash-error" class="alert alert-danger alert-dismissible fade show global-flash" role="alert">
      <div class="alert-icon" aria-hidden="true">!</div>
      <div class="alert-body">
        <span class="alert-title">Error</span>
        <span class="alert-message">{{ session('error') }}</span>
      </div>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif
  @yield('content')
</main>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/theme.js') }}"></script>

<!-- App dialog modal (used to replace native alert/confirm with styled dialog) -->
<div class="modal fade" id="appDialog" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-body d-flex gap-3 align-items-center">
            <div id="appDialogIcon" style="width:56px;height:56px;border-radius:10px;display:flex;align-items:center;justify-content:center;flex:0 0 auto;font-size:20px">✓</div>
            <div>
              <div id="appDialogTitle" style="font-weight:700;margin-bottom:4px">Notice</div>
              <div id="appDialogMessage" class="text-muted"></div>
            </div>
          </div>
          <div class="modal-footer justify-content-center" style="gap:.75rem">
            <button type="button" class="btn btn-secondary" id="appDialogCancel" style="display:none">Cancel</button>
            <button type="button" class="btn btn-primary" id="appDialogOk">OK</button>
          </div>
    </div>
  </div>
</div>
<script>
  // Styled alert/confirm replacement using the above modal
  (function(){
    const modalEl = document.getElementById('appDialog');
    if(!modalEl) return;
    const bsModal = new bootstrap.Modal(modalEl, {backdrop:true});
    const titleEl = document.getElementById('appDialogTitle');
    const msgEl = document.getElementById('appDialogMessage');
    const iconEl = document.getElementById('appDialogIcon');
    const okBtn = document.getElementById('appDialogOk');
    const cancelBtn = document.getElementById('appDialogCancel');

    function showDialog(opts){
      return new Promise((resolve)=>{
        titleEl.textContent = opts.title || 'Notice';
        msgEl.textContent = opts.message || '';
        iconEl.textContent = opts.icon || '✓';
        // style by type
        modalEl.querySelector('.modal-content').style.border = '';
        okBtn.className = 'btn btn-primary';
        cancelBtn.style.display = 'none';
        if(opts.type === 'success'){
          modalEl.querySelector('.modal-content').style.border = '1px solid rgba(34,197,94,0.12)';
        } else if(opts.type === 'danger'){
          modalEl.querySelector('.modal-content').style.border = '1px solid rgba(236,36,38,0.08)';
          okBtn.className = 'btn btn-danger';
        } else if(opts.type === 'confirm'){
          cancelBtn.style.display = '';
        }

        function cleanup(result){
          okBtn.removeEventListener('click', onOk);
          cancelBtn.removeEventListener('click', onCancel);
          bsModal.hide();
          resolve(result);
        }
        function onOk(){ cleanup(true); }
        function onCancel(){ cleanup(false); }

        okBtn.addEventListener('click', onOk);
        cancelBtn.addEventListener('click', onCancel);
        bsModal.show();
      });
    }

    // Expose helper functions
    window.appAlert = function(message, title){ return showDialog({type:'success', title:title||'Notice', message:message}); };
    window.appConfirm = function(message, title){ return showDialog({type:'confirm', title:title||'Please confirm', message:message}); };

    // Intercept forms with data-confirm attribute and show modal before submit
    document.addEventListener('submit', function(e){
      const form = e.target;
      if(!(form && form.dataset && form.dataset.confirm)) return;
      // prevent immediate submit
      e.preventDefault();
      window.appConfirm(form.dataset.confirm).then(function(ok){ if(ok){ form.submit(); } });
    }, true);
  })();

  // Override native alert to use styled modal when available
  const _nativeAlert = window.alert;
  window.alert = function(msg){ if(window.appAlert){ window.appAlert(String(msg)); } else { _nativeAlert(String(msg)); } };
</script>

<script>
// Live client-side cooperative search (debounced)
(function(){
  const selectors = [
    'input[placeholder*="Search"]',
    'input[placeholder*="search"]',
    'input[type="search"]',
    'input[name="q"]',
    'input[id*="search"]'
  ];

  const debounce = (fn, ms=180) => {
    let t; return (...args)=>{ clearTimeout(t); t = setTimeout(()=>fn.apply(this,args), ms); };
  };

  function findCards(container){
    // prefer marked coop items
    let cards = Array.from(container.querySelectorAll('.coop-item, .cooperative-card, .cooperative, .card.coop-item'));
    if(cards.length) return cards;
    // fallback: find cards inside common listing containers
    cards = Array.from(container.querySelectorAll('.card'));
    // if many cards found, assume these are the items
    return cards.length ? cards : [];
  }

  function setupInput(input){
    const form = input.closest('form');
    const container = form ? (form.nextElementSibling || document.body) : (input.closest('.container') || document.body);

    // try to find the results container (directory grid)
    let resultsContainer = container.querySelector('.row.row-cols-1.row-cols-md-3') || container.querySelector('.row-cols-md-3') || container.querySelector('.row');

    // fallback to document-wide listing
    if(!resultsContainer) resultsContainer = document.querySelector('.row.row-cols-1.row-cols-md-3') || document.querySelector('.row-cols-md-3') || document.querySelector('.row');

    const renderResults = (items) => {
      if(!resultsContainer) return;
      if(!items || !items.length){
        resultsContainer.innerHTML = '<div class="col-12"><div class="alert alert-info">No cooperatives found.</div></div>';
        return;
      }
      const cols = items.map(coop=>{
        const desc = coop.description ? (coop.description.length>120 ? coop.description.substr(0,120)+'...' : coop.description) : '';
        const href = coop.link ? coop.link : ('/cooperatives/'+coop.id);
        return `<div class="col"><article class="card h-100"><div class="card-body"><h3 class="h6"><a href="${href}">${escapeHtml(coop.name)}</a></h3><p class="small text-muted">${escapeHtml(coop.sector||'')} · ${escapeHtml(coop.region||'')}</p><p class="mb-0">${escapeHtml(desc)}</p></div></article></div>`;
      }).join('');
      resultsContainer.innerHTML = cols;
    };

    const ajaxSearch = debounce((value)=>{
      const q = value||'';
      const regionInput = form ? form.querySelector('input[name="region"]') : document.querySelector('input[name="region"]');
      const region = regionInput ? regionInput.value : '';
      const url = `/cooperatives/search?q=${encodeURIComponent(q)}&region=${encodeURIComponent(region)}`;
      fetch(url, {headers:{'X-Requested-With':'XMLHttpRequest'}})
        .then(r=> r.ok ? r.json() : Promise.reject(r))
        .then(data=>{
          renderResults(data.data || []);
        }).catch(err=>{
          console.warn('Search error', err);
        });
    }, 220);

    // If we're on the directory page (resultsContainer exists), use AJAX search; otherwise fallback to client filter
    if(resultsContainer){
      input.addEventListener('input', (e)=> ajaxSearch(e.target.value));
      // clear on escape
      input.addEventListener('keydown', e=>{ if(e.key==='Escape'){ input.value=''; ajaxSearch(''); }});
      return;
    }

    // fallback client-side behavior
    let cards = findCards(container);
    if(!cards.length) cards = findCards(document);
    const filterFn = (value) => {
      const q = (value||'').trim().toLowerCase();
      if(!q){ cards.forEach(c=> c.style.display=''); return; }
      cards.forEach(card=>{
        const text = (card.innerText||card.textContent||'').toLowerCase();
        card.style.display = text.indexOf(q) !== -1 ? '' : 'none';
      });
    };
    const handler = debounce((e)=>{ filterFn(e.target.value); }, 180);
    input.addEventListener('input', handler);
    input.addEventListener('keydown', e=>{ if(e.key==='Escape'){ input.value=''; filterFn(''); }});
  }

  function escapeHtml(s){ return String(s||'').replace(/[&<>\"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c])); }

  // locate inputs
  const inputs = selectors.flatMap(sel=>Array.from(document.querySelectorAll(sel)));
  // de-dupe
  const uniq = Array.from(new Set(inputs));
  uniq.forEach(i=> setupInput(i));
})();
</script>
<script>
  (function(){
    const el = document.getElementById('flash-success');
    if(!el) return;

    // create backdrop
    const backdrop = document.createElement('div');
    backdrop.className = 'flash-backdrop';
    backdrop.id = 'flash-backdrop';
    document.body.appendChild(backdrop);

    // start in init state (hidden, slightly up)
    el.classList.add('flash-init');
    // force reflow then show (pop-in) and show backdrop
    void el.offsetWidth;
    el.classList.add('flash-visible');
    backdrop.classList.add('visible');

    // auto-dismiss after 1s: pop out then remove backdrop and element
    setTimeout(() => {
      el.classList.remove('flash-visible');
      backdrop.classList.remove('visible');
      // give transition time before removing
      setTimeout(() => { try{ backdrop.remove(); el.remove(); }catch(e){} }, 220);
    }, 1000);

    // also remove backdrop immediately if user clicks close
    const closeBtn = el.querySelector('.btn-close');
    if(closeBtn){
      closeBtn.addEventListener('click', () => {
        try{ el.remove(); }catch(e){}
        try{ backdrop.remove(); }catch(e){}
      });
    }
  })();
</script>
  @yield('scripts')
</body>
</html>
