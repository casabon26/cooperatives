<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Cooperative Portal') }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary:       #b91c1c;
            --primary-dark:  #991b1b;
            --primary-light: #fee2e2;
            --secondary:     #dc2626;
            --accent:        #fbbf24;
            --text:          #333;
            --heading-font:  'Poppins', sans-serif;
            --body-font:     'Poppins', sans-serif;
        }

        body {
            font-family: var(--body-font);
            color: var(--text);
            background: linear-gradient(135deg, #fff5f5 0%, #fee2e2 100%);
            background-attachment: fixed;
            padding-top: 80px;
            min-height: 100vh;
        }

        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background:
                radial-gradient(circle at top left, rgba(255,255,255,0.5), transparent 70%),
                radial-gradient(circle at bottom right, rgba(255,255,255,0.5), transparent 70%);
            pointer-events: none;
            z-index: -1;
        }

        h1, h2, h3, h4, h5, h6 {
            font-family: var(--heading-font);
            color: var(--primary-dark);
        }

        a { transition: color 0.2s ease; }
        a:hover { color: var(--primary); }

        .card {
            border: none;
            border-radius: 0.75rem;
            transition: transform 0.18s ease, box-shadow 0.18s ease;
        }
        .card:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 36px rgba(0,0,0,0.08);
        }

        .btn-primary {
            background-color: var(--primary);
            border-color: var(--primary);
        }
        .btn-primary:hover,
        .btn-primary:focus-visible {
            background-color: var(--primary-dark);
            border-color: var(--primary-dark);
        }

        /* ── Header with animated nav hover ──────────────────────────────────── */
        .yahoo-header {
            background: rgba(255,255,255,0.92);
            backdrop-filter: blur(6px);
            box-shadow: 0 2px 12px rgba(185,28,28,0.09);
            border-bottom: 2px solid rgba(220,38,38,0.14);
            position: fixed;
            top: 0; left: 0; right: 0;
            z-index: 1030;
        }

        .yahoo-container {
            max-width: 1180px;
            margin: 0 auto;
            padding: 12px 16px;
            display: flex;
            align-items: center;
            gap: 16px;
            flex-wrap: nowrap;
        }

        .yahoo-logo {
            font-weight: 900;
            color: var(--primary);
            font-size: 28px;
            letter-spacing: -0.5px;
        }

        .header-nav {
            flex: 1;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .nav-item {
            position: relative;
            display: flex;
            align-items: center;
            padding: 6px 12px;
            border-radius: 10px;
            color: inherit;
            text-decoration: none;
            transition: background 0.18s ease;
            white-space: nowrap;
        }

        .nav-item:hover {
            background: rgba(185,28,28,0.06);
        }

        .nav-text {
            font-weight: 700;
            color: var(--primary-dark);
            font-size: 0.95rem;
            transition: color 0.14s ease;
        }

        .nav-item:hover .nav-text {
            color: var(--primary);
        }

        /* Animated underline – grows from center with gradient */
        .nav-item::after {
            content: '';
            position: absolute;
            bottom: -4px;
            left: 50%;
            width: 0;
            height: 2px;
            background: linear-gradient(90deg, #b91c1c, #dc2626, #b91c1c);
            transition: width 0.3s ease;
            transform: translateX(-50%);
            border-radius: 2px;
        }

        .nav-item:hover::after {
            width: 70%;
        }

        .nav-item:focus-visible {
            outline: 2px solid rgba(185,28,28,0.3);
            outline-offset: 4px;
            border-radius: 10px;
        }

        .nav-item:focus-visible .nav-text {
            color: var(--primary);
        }

        .more-btn {
            background: transparent;
            border: 0;
            padding: 6px 10px;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.2s ease, color 0.2s ease;
            color: var(--primary);
        }

        .more-btn:hover {
            background: rgba(185,28,28,0.08);
        }

        .more-btn svg {
            stroke: var(--primary);
            transition: stroke 0.2s ease;
        }

        .more-btn:hover svg {
            stroke: var(--primary-dark);
        }

        /* Dropdown menu */
        .dropdown-menu {
            border-radius: 12px;
            border: 1px solid rgba(185,28,28,0.15);
            box-shadow: 0 12px 32px rgba(185,28,28,0.12);
            padding: 0.45rem 0;
            background: #fff;
            margin-top: 12px !important;
        }

        .dropdown-item {
            color: #3b1a1a;
            padding: 0.65rem 1rem;
            font-weight: 700;
            border-left: 3px solid transparent;
            transition: all 0.15s ease;
        }

        .dropdown-item:hover,
        .dropdown-item:focus {
            background: rgba(239,68,68,0.06);
            color: #991b1b;
            border-left-color: #dc2626;
            padding-left: calc(1rem + 2px);
        }

        .dropdown-header {
            color: #b91c1c;
            font-weight: 800;
            padding: 0.55rem 1rem;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }

        /* Profile button */
        .profile-btn {
            width: 42px;
            height: 42px;
            border-radius: 9999px;
            background: linear-gradient(135deg, rgba(255,235,238,0.6), rgba(255,225,228,0.6));
            border: 2px solid rgba(185,28,28,0.15);
            color: var(--primary-dark);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
        }

        .profile-btn:hover {
            background: linear-gradient(135deg, #fdd2d2, #fcc5c5);
            border-color: rgba(185,28,28,0.3);
            box-shadow: 0 4px 12px rgba(185,28,28,0.15);
        }

        .profile-btn svg {
            stroke: var(--primary);
        }

        .header-icons {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-left: auto;
        }

        /* Flash messages */
        #flash-success,
        #flash-error {
            position: fixed;
            top: 72px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 1060;
            max-width: 760px;
            width: calc(100% - 48px);
            box-shadow: 0 12px 40px rgba(0,0,0,0.14);
            border-radius: 10px;
            opacity: 0;
            transition: all 0.22s cubic-bezier(0.2, 0.9, 0.2, 1);
        }

        .flash-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.06);
            opacity: 0;
            transition: opacity 0.18s ease;
            z-index: 1059;
            pointer-events: none;
        }
        .flash-backdrop.visible { opacity: 1; }

        /* .btn-back styling moved to assets/css/theme.css for global consistency */

        /* Disable card hover in admin area */
        .admin-area .card:hover {
            transform: none !important;
            box-shadow: none !important;
        }

        @media (max-width: 640px) {
            body { padding-top: 120px; }
            .yahoo-logo { font-size: 22px; }
            .nav-text { font-size: 0.85rem; }
        }
    </style>

    @php
        $v = fn($path) => file_exists(public_path($path)) ? filemtime(public_path($path)) : time();
    @endphp
    <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}?v={{ $v('assets/css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/extra.css') }}?v={{ $v('assets/css/extra.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/theme.css') }}?v={{ $v('assets/css/theme.css') }}">

    @hasSection('styles')
        <style>@yield('styles')</style>
    @endif
</head>

<body class="{{ request()->is('admin*') ? 'admin-area' : '' }}">

    <div class="page-polygon-overlay" aria-hidden="true"></div>

    <header class="yahoo-header" role="banner">
        <div class="yahoo-container">

            <a href="/" class="d-flex align-items-center gap-3 text-decoration-none" style="color:inherit">
                @if(file_exists(public_path('assets/images/logo/CCLDO.png')))
                    <img src="{{ asset('assets/images/logo/CCLDO.png') }}" alt="CCLDO logo" style="height:38px;object-fit:contain">
                @endif
                <div class="yahoo-logo">CCLDO - CABUYAO</div>
            </a>

            <nav class="header-nav" aria-label="Main navigation">
                <a href="/" class="nav-item"><span class="nav-text">HOME</span></a>
                <a href="/about" class="nav-item"><span class="nav-text">ABOUT</span></a>
                <a href="/cooperatives?per_page=34" class="nav-item"><span class="nav-text">COOPERATIVE</span></a>
                <a href="/livelihood" class="nav-item"><span class="nav-text">LIVELIHOOD</span></a>
                <a href="/enterprise-portal" class="nav-item"><span class="nav-text">ENTERPRISE DEVELOPMENT</span></a>

                <div class="dropdown">
                    <button class="more-btn nav-item dropdown-toggle" id="moreDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="3" y1="6" x2="21" y2="6"/>
                            <line x1="3" y1="12" x2="21" y2="12"/>
                            <line x1="3" y1="18" x2="21" y2="18"/>
                        </svg>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="moreDropdown">
                        <li><h6 class="dropdown-header">Resources</h6></li>
                        <li><a class="dropdown-item" href="/faqs">FAQs</a></li>
                        <li><a class="dropdown-item" href="/videos">Videos</a></li>
                        <li><a class="dropdown-item" href="/news">News</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><h6 class="dropdown-header">Portals</h6></li>
                        <li><a class="dropdown-item" href="/store-locations">Enterprise Map</a></li>
                        <li><a class="dropdown-item" href="/training">Training</a></li>
                    </ul>
                </div>
            </nav>

            <div class="header-icons">
                <div class="dropdown">
                    <button class="profile-btn dropdown-toggle" id="profileDropdown" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Account">
                        <svg width="20" height="20" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.3">
                            <circle cx="8" cy="5" r="3"/>
                            <path d="M2 14s1.5-3 6-3 6 3 6 3"/>
                        </svg>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end profile-menu" aria-labelledby="profileDropdown">
                        @auth
                            <li><a class="dropdown-item" href="/profile">Profile</a></li>
                            <li><a class="dropdown-item" href="/settings">Settings</a></li>
                            <li><hr class="dropdown-divider"></li>

                            @if(session('admin_authenticated') && (Auth::user()->role ?? '') === 'gov_admin')
                                <li><h6 class="dropdown-header">Admin</h6></li>
                                <li><a class="dropdown-item" href="{{ route('admin.profile.show') }}">Admin Profile</a></li>
                                <li><a class="dropdown-item" href="/admin/panel">Admin Panel</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="/admin/logout" class="logout-form" data-confirm="Are you sure you want to logout?">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-danger w-100">Logout</button>
                                    </form>
                                </li>
                            @else
                                <li>
                                    <form method="POST" action="/logout" class="logout-form" data-confirm="Are you sure you want to logout?">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-danger w-100">Logout</button>
                                    </form>
                                </li>
                            @endif
                        @else
                            <li><h6 class="dropdown-header">Admin</h6></li>
                            <li><a class="dropdown-item" href="/admin/login">Login</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><h6 class="dropdown-header">User</h6></li>
                            <li><a class="dropdown-item" href="/user/login">User Login</a></li>
                        @endauth
                    </ul>
                </div>
            </div>

        </div>
    </header>

    @yield('hero')

    @if(request()->is('admin/*') && !request()->is('admin/panel'))
        <div class="container mt-3">
            @hasSection('back-button')
                @yield('back-button')
            @else
                @include('partials.back-button')
            @endif
        </div>
    @endif

    <main class="container main-container" role="main">
        @if(session('success'))
            <div id="flash-success" class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>Success:</strong> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div id="flash-error" class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Error:</strong> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @yield('content')
    </main>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
        <script src="{{ asset('assets/js/theme.js') }}"></script>

        <!-- Session toast (uses Bootstrap Toast when available, falls back to alert) -->
        <div aria-live="polite" aria-atomic="true" class="position-fixed top-0 end-0 p-3" style="z-index: 2000;">
            <div id="sessionToast" class="toast align-items-center border-0" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body" id="sessionToastBody"></div>
                </div>
            </div>
        </div>

        <script>
            (function(){
                try{
                    var success = @json(session('success'));
                    var error = @json(session('error'));

                        // If the centered flash alerts are present, don't show the top-right session toast
                        var hasCenteredFlash = document.getElementById('flash-success') || document.getElementById('flash-error');
                        if(window.bootstrap && (success || error) && !hasCenteredFlash){
                            var toastEl = document.getElementById('sessionToast');
                            var toastBody = document.getElementById('sessionToastBody');
                            // apply contextual classes
                            toastEl.className = 'toast align-items-center border-0 ' + (success ? 'text-bg-success' : 'text-bg-danger');
                            toastBody.textContent = (success ? 'Success: ' + success : 'Error: ' + error);
                            var toast = new bootstrap.Toast(toastEl, { autohide: true, delay: 2000 });
                            toast.show();
                        } else {
                            // fallback to native alert only if there is no centered flash
                            if(!hasCenteredFlash){
                                if(success){ alert('Success: ' + success); }
                                if(error){ alert('Error: ' + error); }
                            }
                        }
                }catch(e){ /* ignore */ }
            })();
        </script>

        <script>
            // Ensure the alert blocks with ids #flash-success and #flash-error are visible
            (function(){
                try{
                    var success = @json(session('success'));
                    var error = @json(session('error'));
                    if(success || error){
                        ['flash-success','flash-error'].forEach(function(id){
                            var el = document.getElementById(id);
                            if(!el) return;
                            // make visible (CSS sets opacity:0 by default)
                            el.style.opacity = 1;
                            el.style.transform = 'translateX(-50%) translateY(0)';
                            // auto-hide after 3s if bootstrap isn't used for toast
                            setTimeout(function(){
                                try{ if(el.classList.contains('show')) el.classList.remove('show'); el.style.opacity = 0; }catch(e){}
                            }, 3200);
                        });
                    }
                }catch(e){ /* ignore */ }
            })();
        </script>

        <!-- Centralized confirm modal used by window.appConfirm(message) -->
        <div class="modal fade" id="appConfirmModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Confirm</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p id="appConfirmMessage" class="mb-0"></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" id="appConfirmCancel" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-danger" id="appConfirmOk">Confirm</button>
                    </div>
                </div>
            </div>
        </div>

        <script>
            // Promise-based confirm dialog for uniform confirmations
            window.appConfirm = function(message){
                return new Promise(function(resolve){
                    try{
                        var modalEl = document.getElementById('appConfirmModal');
                        var msg = document.getElementById('appConfirmMessage');
                        var btnOk = document.getElementById('appConfirmOk');
                        var btnCancel = document.getElementById('appConfirmCancel');
                        if(!modalEl || !msg || !btnOk || !btnCancel){
                            // fallback to native confirm
                            resolve(confirm(message));
                            return;
                        }
                        msg.textContent = message || 'Are you sure?';
                        var modal = new bootstrap.Modal(modalEl, { backdrop: 'static' });

                        var cleanup = function(){
                            btnOk.removeEventListener('click', okHandler);
                            btnCancel.removeEventListener('click', cancelHandler);
                        };

                        var okHandler = function(){ cleanup(); modal.hide(); resolve(true); };
                        var cancelHandler = function(){ cleanup(); modal.hide(); resolve(false); };

                        btnOk.addEventListener('click', okHandler);
                        btnCancel.addEventListener('click', cancelHandler);

                        modal.show();
                    }catch(e){ resolve(confirm(message)); }
                });
            };

            // Intercept forms with `data-confirm` attribute and show appConfirm
            document.addEventListener('submit', function(e){
                try{
                    var form = e.target;
                    if(!(form instanceof HTMLFormElement)) return;
                    var msg = form.getAttribute('data-confirm');
                    if(!msg) return;
                    e.preventDefault();
                    window.appConfirm(msg).then(function(ok){ if(ok) form.submit(); });
                }catch(err){ /* ignore and allow default */ }
            }, true);

            // Intercept clicks on elements with data-confirm (buttons/links)
            document.addEventListener('click', function(e){
                var el = e.target;
                // climb up until button/link/form element
                while(el && el !== document.documentElement){
                    var msg = el.getAttribute && el.getAttribute('data-confirm');
                    if(msg){
                        // If it's a button inside a form, let the submit handler handle it.
                        if(el.tagName === 'A'){
                            e.preventDefault();
                            window.appConfirm(msg).then(function(ok){ if(ok) window.location = el.href; });
                        } else if(el.tagName === 'BUTTON' && el.type === 'button'){
                            e.preventDefault();
                            window.appConfirm(msg).then(function(ok){ if(ok){ var form = el.closest('form'); if(form) form.submit(); } });
                        }
                        break;
                    }
                    el = el.parentNode;
                }
            }, true);
        </script>

        @yield('scripts')

</body>
</html>