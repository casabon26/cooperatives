<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'CCLDO - Cabuyao') }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <style>
        :root {
            --primary:       #C8102E;
            --primary-dark:  #9B0F23;
            --secondary:     #E30613;
            --accent:        #FBBF24;
        }

        body {
            font-family: 'Poppins', sans-serif;
            color: #222;
            background: linear-gradient(135deg, #fff5f5 0%, #ffebeb 100%);
            background-attachment: fixed;
            padding-top: 78px;           /* Shorter navbar height */
            min-height: 100vh;
        }

        /* ==================== LIVELY MODERN NAVBAR ==================== */
        .modern-header {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(20px);
            box-shadow: 0 8px 30px rgba(200, 16, 46, 0.16);
            border-bottom: 5px solid var(--primary);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1030;
            transition: all 0.3s ease;
        }

        .header-container {
            max-width: 1340px;
            margin: 0 auto;
            padding: 0 28px;
        }

        .header-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 13px 0;           /* Reduced vertical padding */
            gap: 28px;
        }

        /* Logo + Title */
        .logo-wrapper {
            color: var(--primary-dark);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 14px;
            min-width: 275px;
            transition: transform 0.3s ease;
        }

        .logo-wrapper:hover {
            transform: scale(1.03);
        }

        .logo-img {
            height: 50px;               /* Slightly smaller logo */
            object-fit: contain;
            filter: drop-shadow(0 2px 4px rgba(200, 16, 46, 0.15));
        }

        .logo-text {
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .logo-main {
            font-size: 30px;
            font-weight: 800;
            letter-spacing: -1.5px;
            color: var(--primary);
            line-height: 1;
        }

        .logo-sub {
            font-size: 14.5px;
            font-weight: 700;
            letter-spacing: 3.6px;
            color: var(--primary-dark);
            margin-top: -3px;
        }

        /* Navigation Links */
        .main-nav {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .nav-link {
            color: #2c2c2c;
            font-weight: 600;
            font-size: 1.02rem;
            padding: 12px 22px;
            border-radius: 14px;
            text-decoration: none;
            position: relative;
            transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .nav-link:hover,
        .nav-link.active {
            background: linear-gradient(90deg, rgba(200,16,46,0.08), rgba(227,6,19,0.06));
            color: var(--primary);
            transform: translateY(-2px);
            box-shadow: 0 5px 14px rgba(200, 16, 46, 0.12);
        }

        .nav-link::after {
            content: '';
            position: absolute;
            bottom: 8px;
            left: 50%;
            width: 0;
            height: 3.5px;
            background: linear-gradient(to right, var(--primary), var(--secondary));
            transition: width 0.4s ease;
            transform: translateX(-50%);
            border-radius: 4px;
        }

        .nav-link:hover::after,
        .nav-link.active::after {
            width: 68%;
        }

        /* Dropdown */
        .dropdown-toggle::after { display: none; }

        .dropdown-menu {
            border: none;
            border-radius: 16px;
            box-shadow: 0 18px 45px rgba(200, 16, 46, 0.20);
            padding: 12px 8px;
            margin-top: 8px;
            background: white;
        }

        .dropdown-header {
            color: var(--primary);
            font-weight: 800;
            font-size: 0.84rem;
            text-transform: uppercase;
            letter-spacing: 1.1px;
            padding: 8px 18px;
        }

        .dropdown-item {
            padding: 11px 20px;
            font-weight: 500;
            border-radius: 10px;
            transition: all 0.25s ease;
        }

        .dropdown-item:hover {
            background: rgba(200, 16, 46, 0.09);
            color: var(--primary-dark);
            transform: translateX(5px);
        }

        /* Profile Button */
        .profile-button {
            width: 46px;
            height: 46px;
            border-radius: 50%;
            background: linear-gradient(135deg, #fff0f0, #ffe0e0);
            border: 3px solid var(--primary);
            color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.35s ease;
            box-shadow: 0 4px 15px rgba(200, 16, 46, 0.18);
        }

        .profile-button:hover {
            background: var(--primary);
            color: white;
            transform: scale(1.1);
        }

        /* Accent Line */
        .header-accent {
            position: absolute;
            bottom: -5px;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, transparent, var(--secondary), var(--primary), transparent);
            opacity: 0.8;
        }

        /* Card Enhancement */
        .card {
            border: none;
            border-radius: 18px;
            transition: all 0.4s ease;
        }
        
        .card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(200, 16, 46, 0.13);
        }

        /* Responsive */
        @media (max-width: 992px) {
            .logo-wrapper { min-width: auto; }
            .nav-link { padding: 10px 16px; font-size: 0.97rem; }
        }

        @media (max-width: 768px) {
            .header-inner { 
                flex-wrap: wrap; 
                gap: 14px; 
            }
            .logo-main { font-size: 26px; }
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

    <!-- Modern Navbar -->
    <header class="modern-header" role="banner">
        <div class="header-container">
            <div class="header-inner">

                <!-- Logo + Title -->
                <a href="/" class="logo-wrapper">
                    @if(file_exists(public_path('assets/images/logo/CCLDO.png')))
                        <img src="{{ asset('assets/images/logo/CCLDO.png') }}" alt="CCLDO Cabuyao" class="logo-img">
                    @endif
                    <div class="logo-text">
                        <span class="logo-main">CCLDO</span>
                        <span class="logo-sub">CABUYAO</span>
                    </div>
                </a>

                <!-- Navigation -->
                <nav class="main-nav" aria-label="Main navigation">
                    <a href="/" class="nav-link">HOME</a>
                    <a href="/about" class="nav-link">ABOUT</a>
                    <a href="/cooperatives?per_page=34" class="nav-link">COOPERATIVE</a>
                    <a href="/livelihood" class="nav-link">LIVELIHOOD</a>
                    <a href="/enterprise-portal" class="nav-link">ENTERPRISE DEVELOPMENT</a>

                    <div class="dropdown">
                        <button class="nav-link dropdown-toggle d-flex align-items-center gap-1" 
                                id="moreDropdown" 
                                data-bs-toggle="dropdown" 
                                aria-expanded="false">
                            MORE 
                            <i class="bi bi-chevron-down"></i>
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

                <!-- Profile -->
                <div class="header-actions">
                    <div class="dropdown">
                        <button class="profile-button dropdown-toggle" 
                                id="profileDropdown" 
                                data-bs-toggle="dropdown" 
                                aria-expanded="false">
                            <i class="bi bi-person-circle" style="font-size: 1.45rem;"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
                            @auth
                                <li><a class="dropdown-item" href="/profile">My Profile</a></li>
                                <li><a class="dropdown-item" href="/settings">Settings</a></li>
                                <li><hr class="dropdown-divider"></li>
                                @if(session('admin_authenticated') && (Auth::user()->role ?? '') === 'gov_admin')
                                    <li><a class="dropdown-item" href="/admin/panel">Admin Panel</a></li>
                                @endif
                                <li>
                                    <form method="POST" action="/logout">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger">Logout</button>
                                    </form>
                                </li>
                            @else
                                <li><a class="dropdown-item" href="/admin/login">Admin Login</a></li>
                                <li><a class="dropdown-item" href="/user/login">User Login</a></li>
                            @endauth
                        </ul>
                    </div>
                </div>

            </div>
        </div>

        <!-- Subtle accent line -->
        <div class="header-accent"></div>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('assets/js/theme.js') }}"></script>

    @yield('scripts')

</body>
</html>