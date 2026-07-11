<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@hasSection('title') @yield('title') | @endif{{ config('app.name', 'Nyahende') }} - Mfumo wa Ukoo</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}?v={{ time() }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('favicon.png') }}?v={{ time() }}">
    <link rel="apple-touch-icon" href="{{ asset('favicon.png') }}?v={{ time() }}">
    
    <!-- PWA Settings -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#0d1b2a">
    @if(config('services.google.analytics_id'))
        <!-- Google tag (gtag.js) -->
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ config('services.google.analytics_id') }}"></script>
        <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', '{{ config('services.google.analytics_id') }}');
        </script>
    @endif


    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Bootstrap 4 (kept for AdminLTE component compat) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">

    <!-- AdminLTE 3 CSS (components only compat: small-box, card, badges) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">

    <!-- Select2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@x.x.x/dist/select2-bootstrap4.min.css">

    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap4.min.css">

    <!-- Leaflet CSS (for map pages) -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">

    <!-- Our Modern Layout CSS (loaded last to override) -->
    <link rel="stylesheet" href="{{ asset('css/layout.css') }}?v={{ filemtime(public_path('css/layout.css')) }}">
    <link rel="stylesheet" href="{{ asset('css/mobile-responsive.css') }}?v={{ filemtime(public_path('css/mobile-responsive.css')) }}">

    <!-- Page-specific styles -->
    @yield('css')
    @stack('styles')

    <!-- Vite Assets (Includes Laravel Echo) -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>

<body class="@auth logged-in @endauth">

{{-- ======================================
     SIDEBAR
     ====================================== --}}
@auth
<aside class="app-sidebar" id="appSidebar">

    {{-- Brand --}}
    <a href="{{ route('dashboard') }}" class="sidebar-brand">
        <div class="sidebar-brand-icon">
            <i class="fas fa-shield-alt"></i>
        </div>
        <div class="sidebar-brand-text">
            {{ config('app.name', 'Nyahende') }}
            <small>{{ __('common.family_tree_management') }}</small>
        </div>
    </a>

    {{-- Navigation --}}
    <nav class="sidebar-nav">

        {{-- Ukurasa wa Nyumbani --}}
        <a href="{{ route('dashboard') }}" class="sidebar-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="fas fa-home"></i>
            Ukurasa wa Nyumbani
        </a>

        {{-- Wanachama --}}
        <div class="sidebar-section-title">{{ __('common.people') }}</div>

        <a href="{{ route('members.index') }}" class="sidebar-item {{ request()->routeIs('members.*') && !request()->routeIs('members.create') ? 'active' : '' }}">
            <i class="fas fa-users"></i>
            {{ __('common.all_members') }}
        </a>

        @if(auth()->user()->isAdmin() || auth()->user()->member_id !== null)
            <a href="{{ route('members.create') }}" class="sidebar-item {{ request()->routeIs('members.create') ? 'active' : '' }}">
                <i class="fas fa-user-plus"></i>
                Ongeza Mwanachama
            </a>
        @else
            <a href="{{ route('members.create') }}" class="sidebar-item {{ request()->routeIs('members.create') ? 'active' : '' }}">
                <i class="fas fa-user-plus text-warning"></i>
                <span class="text-warning font-weight-bold">{{ __('common.join_family_tree') }}</span>
            </a>
        @endif

        {{-- Simulizi na Picha --}}
        <a href="{{ route('posts.index') }}" class="sidebar-item {{ request()->routeIs('posts.*') ? 'active' : '' }}">
            <i class="fas fa-rss"></i>
            Simulizi / Habari
        </a>

        <a href="{{ route('galleries.index') }}" class="sidebar-item {{ request()->routeIs('galleries.*') ? 'active' : '' }}">
            <i class="fas fa-images"></i>
            Picha za Ukoo
        </a>

        {{-- Jamii --}}
        <div class="sidebar-section-title">{{ __('common.community') }}</div>

        <a href="{{ route('announcements.feed') }}" class="sidebar-item {{ request()->routeIs('announcements.*') ? 'active' : '' }}">
            <i class="fas fa-bullhorn"></i>
            {{ __('common.announcements') }}
        </a>

        <a href="{{ route('campaigns.index') }}" class="sidebar-item {{ request()->routeIs('campaigns.*') ? 'active' : '' }}">
            <i class="fas fa-hand-holding-heart"></i>
            {{ __('common.campaigns') }}
        </a>

        {{-- Admin-Only Items --}}
        @if(auth()->user()->isAdmin())

        <div class="sidebar-section-title">{{ __('common.structure') }}</div>

        <a href="{{ route('clans.index') }}" class="sidebar-item {{ request()->routeIs('clans.*') ? 'active' : '' }}">
            <i class="fas fa-shield-alt"></i>
            {{ __('common.clans') }}
        </a>

        <a href="{{ route('parents.index') }}" class="sidebar-item {{ request()->routeIs('parents.*') ? 'active' : '' }}">
            <i class="fas fa-user-friends"></i>
            {{ __('common.parents') }}
        </a>

        <div class="sidebar-section-title">{{ __('common.records') }}</div>

        <a href="{{ route('timeline.index') }}" class="sidebar-item {{ request()->routeIs('timeline.*') ? 'active' : '' }}">
            <i class="fas fa-stream"></i>
            {{ __('common.timeline') }}
        </a>

        <a href="{{ route('calendar.index') }}" class="sidebar-item {{ request()->routeIs('calendar.*') ? 'active' : '' }}">
            <i class="fas fa-calendar-alt"></i>
            {{ __('common.calendar') }}
        </a>

        <a href="{{ route('admin.users') }}" class="sidebar-item {{ request()->routeIs('admin.users') ? 'active' : '' }}">
            <i class="fas fa-users-cog"></i>
            {{ __('common.system_users') }}
        </a>

        @endif

        <div class="sidebar-divider"></div>

        <a href="{{ route('notifications.index') }}" class="sidebar-item {{ request()->routeIs('notifications.*') ? 'active' : '' }}">
            <i class="fas fa-bell"></i>
            {{ __('common.notifications') }}
        </a>

    </nav>

    {{-- User Footer --}}
    <div class="sidebar-footer">
        <div class="sidebar-user-card">
            <div class="sidebar-user-avatar">
                {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 2)) }}
            </div>
            <div class="sidebar-user-info">
                <div class="sidebar-user-name">{{ Auth::user()->name ?? __('common.user') }}</div>
                <div class="sidebar-user-role">{{ Auth::user()->email ?? '' }}</div>
            </div>
            <a href="{{ route('logout') }}"
               class="sidebar-logout-btn"
               title="{{ __('common.logout') }}"
               onclick="event.preventDefault(); document.getElementById('sidebar-logout-form').submit();">
                <i class="fas fa-sign-out-alt"></i>
            </a>
        </div>
        <form id="sidebar-logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
            @csrf
        </form>
    </div>

</aside>

{{-- Sidebar Overlay (mobile) --}}
<div class="sidebar-overlay" id="sidebarOverlay"></div>
@endauth

{{-- ======================================
     TOPBAR
     ====================================== --}}
@auth
<header class="app-topbar" id="appTopbar">
    <div class="topbar-left">
        <button class="topbar-toggle" id="sidebarToggle" type="button" aria-label="Toggle sidebar">
            <i class="fas fa-bars"></i>
        </button>
        <h1 class="topbar-page-title">@yield('page_title', config('app.name', 'Clan System'))</h1>
    </div>

    <div class="topbar-right">
        {{-- Language Selector --}}
        <div class="dropdown">
            <a href="#" class="topbar-icon-btn" data-toggle="dropdown" role="button" title="Badilisha Lugha / Change Language">
                <i class="fas fa-globe"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-right">
                <a class="dropdown-item {{ app()->getLocale() == 'en' ? 'active' : '' }}" href="{{ route('language.switch', 'en') }}">
                    🇬🇧 English
                </a>
                <a class="dropdown-item {{ app()->getLocale() == 'sw' ? 'active' : '' }}" href="{{ route('language.switch', 'sw') }}">
                    🇹🇿 Kiswahili
                </a>
            </div>
        </div>

        {{-- Notifications bell --}}
        <a href="{{ route('notifications.index') }}" class="topbar-icon-btn" title="Notifications">
            <i class="fas fa-bell"></i>
            @if(auth()->user() && auth()->user()->unreadNotifications && auth()->user()->unreadNotifications->count() > 0)
                <span class="topbar-notif-dot"></span>
            @endif
        </a>

        {{-- User dropdown --}}
        <div class="dropdown">
            <a href="#" class="topbar-user-btn" data-toggle="dropdown" role="button">
                <div class="topbar-avatar">
                    {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 2)) }}
                </div>
                <span class="topbar-name">{{ Str::limit(Auth::user()->name ?? 'User', 14) }}</span>
                <i class="fas fa-chevron-down" style="font-size:10px; color:#94a3b8;"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-right">
                <div class="dropdown-item-text">
                    <small class="text-muted">{{ __('common.signed_in_as') }}</small><br>
                    <strong>{{ Auth::user()->name }}</strong>
                </div>
                <div class="dropdown-divider"></div>
                @if(Auth::user()->member_id !== null)
                    <a class="dropdown-item" href="{{ route('members.dashboard', Auth::user()->member_id) }}">
                        <i class="fas fa-user-circle mr-2"></i> {{ __('common.my_profile') }}
                    </a>
                    <div class="dropdown-divider"></div>
                @endif
                <a class="dropdown-item" href="{{ route('logout') }}"
                   onclick="event.preventDefault(); document.getElementById('sidebar-logout-form').submit();">
                    <i class="fas fa-sign-out-alt mr-2"></i> {{ __('common.logout') }}
                </a>
            </div>
        </div>
    </div>
</header>
@endauth

{{-- ======================================
     MAIN CONTENT
     ====================================== --}}
<div class="app-content" id="appContent">
    <div class="app-content-inner">

        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                <i class="fas fa-check-circle mr-2"></i>
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                <i class="fas fa-exclamation-circle mr-2"></i>
                {{ session('error') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        @if(session('warning'))
            <div class="alert alert-warning alert-dismissible fade show mb-4" role="alert">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                {{ session('warning') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        {{-- Content Header (AdminLTE compat) --}}
        @hasSection('content_header')
            <div class="content-page-header">
                @yield('content_header')
            </div>
        @endif

        {{-- Main Content --}}
        @yield('content')

    </div>

    {{-- Footer --}}
    <footer class="app-footer">
        <span>&copy; {{ date('Y') }} <strong>Felician Joseph Nyisulya</strong>. {{ __('common.all_rights_reserved') }}</span>
        <span>Kwa msaada zaidi <i class="fas fa-phone-alt mx-1"></i> 0787661560</span>
    </footer>
</div>

{{-- ======================================
     SCRIPTS
     ====================================== --}}

<!-- jQuery -->
<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>

<!-- Bootstrap 4 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- AdminLTE 3 JS (for component behaviours) -->
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>

<!-- Select2 -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<!-- DataTables -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap4.min.js"></script>

<!-- Leaflet JS (for map pages) -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<!-- Sidebar Toggle Script -->
<script>
(function () {
    var sidebar   = document.getElementById('appSidebar');
    var overlay   = document.getElementById('sidebarOverlay');
    var toggleBtn = document.getElementById('sidebarToggle');

    function openSidebar() {
        if (sidebar)  sidebar.classList.add('sidebar-open');
        if (overlay)  overlay.classList.add('active');
    }

    function closeSidebar() {
        if (sidebar)  sidebar.classList.remove('sidebar-open');
        if (overlay)  overlay.classList.remove('active');
    }

    if (toggleBtn) {
        toggleBtn.addEventListener('click', function () {
            if (sidebar && sidebar.classList.contains('sidebar-open')) {
                closeSidebar();
            } else {
                openSidebar();
            }
        });
    }

    if (overlay) {
        overlay.addEventListener('click', closeSidebar);
    }

    // Init select2 globally
    $(document).ready(function () {
        if ($.fn.select2) {
            $('.select2').select2({ theme: 'bootstrap4' });
        }

        // Banners will not auto-dismiss automatically to ensure they remain visible for review
    });
})();
</script>

<!-- Page-specific scripts -->
@yield('js')
@stack('scripts')

@auth
    <!-- FCM Config -->
    <script>
        window.fcmConfig = {
            apiKey: "{{ config('services.fcm.api_key') }}",
            authDomain: "{{ config('services.fcm.auth_domain') }}",
            projectId: "{{ config('services.fcm.project_id') }}",
            storageBucket: "{{ config('services.fcm.storage_bucket') }}",
            messagingSenderId: "{{ config('services.fcm.messaging_sender_id') }}",
            appId: "{{ config('services.fcm.app_id') }}",
            vapidKey: "{{ config('services.fcm.vapid_key') }}"
        };
    </script>

    <!-- Axios -->
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    
    <!-- Firebase SDK Compat -->
    <script src="https://www.gstatic.com/firebasejs/9.23.0/firebase-app-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/9.23.0/firebase-messaging-compat.js"></script>

    <!-- FCM Setup Script -->
    <script src="{{ asset('js/fcm-setup.js') }}"></script>

    <!-- Register Service Worker -->
    <script>
        if ('serviceWorker' in navigator) {
            const config = window.fcmConfig;
            if (config && config.apiKey) {
                const queryParams = new URLSearchParams({
                    apiKey: config.apiKey,
                    authDomain: config.authDomain,
                    projectId: config.projectId,
                    storageBucket: config.storageBucket,
                    messagingSenderId: config.messagingSenderId,
                    appId: config.appId
                }).toString();

                navigator.serviceWorker.register('/firebase-messaging-sw.js?' + queryParams)
                    .then((registration) => {
                        console.log('Firebase Service Worker registered: ', registration.scope);
                    })
                    .catch((err) => {
                        console.error('Firebase Service Worker registration failed: ', err);
                    });
            } else {
                console.warn('FCM configurations not found in layout window.fcmConfig');
            }
        }
    </script>
@endauth

<!-- PWA Install Banner -->
<div id="pwa-install-banner" class="alert alert-info shadow-lg" role="alert" style="display: none; position: fixed; bottom: 20px; left: 50%; transform: translateX(-50%); z-index: 9999; max-width: 400px; width: 90%; border-radius: 12px;">
    <div class="d-flex align-items-center">
        <img src="{{ asset('favicon.png') }}" width="45" height="45" class="rounded mr-3" alt="Logo">
        <div style="flex-grow: 1;">
            <strong style="font-size: 1.1rem; color: #0d1b2a;">Nyahende App</strong><br>
            <span style="font-size: 0.85rem; color: #1a202c;">Sakinisha mfumo kwenye simu yako kwa uzoefu bora na haraka!</span>
        </div>
    </div>
    <div class="mt-3 text-right">
        <button type="button" class="btn btn-sm btn-outline-secondary" id="pwa-dismiss-btn" style="border-radius: 20px; padding: 4px 15px;">Baadaye</button>
        <button type="button" class="btn btn-sm btn-primary ml-2" id="pwa-install-btn" style="border-radius: 20px; padding: 4px 15px;">Sakinisha Sasa</button>
    </div>
</div>

<script>
    let deferredPrompt;
    const installBanner = document.getElementById('pwa-install-banner');
    const installBtn = document.getElementById('pwa-install-btn');
    const dismissBtn = document.getElementById('pwa-dismiss-btn');

    // Mteja akiingia na inaruhusiwa ku-install
    window.addEventListener('beforeinstallprompt', (e) => {
        // Zuia default mini-infobar ya Chrome
        e.preventDefault();
        // Hifadhi event kwa matumizi ya baadae
        deferredPrompt = e;
        
        // Angalia kama alishakataa ndani ya masaa 24
        if(!localStorage.getItem('pwa_dismissed')) {
            installBanner.style.display = 'block';
        }
    });

    installBtn.addEventListener('click', async () => {
        installBanner.style.display = 'none';
        if (deferredPrompt) {
            deferredPrompt.prompt();
            const { outcome } = await deferredPrompt.userChoice;
            console.log(`Matokeo ya usakinishaji: ${outcome}`);
            deferredPrompt = null;
        }
    });

    dismissBtn.addEventListener('click', () => {
        installBanner.style.display = 'none';
        // Hifadhi uamuzi wa mteja ili asisumbuliwe kila sekunde (Saa 24)
        localStorage.setItem('pwa_dismissed', 'true');
        setTimeout(() => {
            localStorage.removeItem('pwa_dismissed');
        }, 86400000); // 24 hours
    });

    window.addEventListener('appinstalled', () => {
        installBanner.style.display = 'none';
        deferredPrompt = null;
    });
</script>

</body>
</html>
