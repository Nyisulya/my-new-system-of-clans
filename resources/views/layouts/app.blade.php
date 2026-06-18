<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'Clan System'))</title>
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-XX1F84DR2E"></script>
    <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', 'G-XX1F84DR2E');
    </script>


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

        {{-- Main --}}
        <div class="sidebar-section-title">{{ __('common.main') }}</div>

        <a href="{{ route('dashboard') }}" class="sidebar-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="fas fa-tachometer-alt"></i>
            {{ __('common.dashboard') }}
        </a>

        {{-- Members --}}
        <div class="sidebar-section-title">{{ __('common.people') }}</div>

        <a href="{{ route('members.index') }}" class="sidebar-item {{ request()->routeIs('members.*') && !request()->routeIs('members.create') ? 'active' : '' }}">
            <i class="fas fa-users"></i>
            {{ __('common.all_members') }}
        </a>

        @if(auth()->user()->isAdmin())
            <a href="{{ route('members.create') }}" class="sidebar-item {{ request()->routeIs('members.create') ? 'active' : '' }}">
                <i class="fas fa-user-plus"></i>
                {{ __('common.add_member') }}
            </a>
            <a href="{{ route('admin.users') }}" class="sidebar-item {{ request()->routeIs('admin.users') ? 'active' : '' }}">
                <i class="fas fa-users-cog"></i>
                {{ __('common.system_users') }}
            </a>
        @elseif(auth()->user()->member_id === null)
            <a href="{{ route('members.create') }}" class="sidebar-item {{ request()->routeIs('members.create') ? 'active' : '' }}">
                <i class="fas fa-user-plus text-warning"></i>
                <span class="text-warning font-weight-bold">{{ __('common.join_family_tree') }}</span>
            </a>
        @endif

        <a href="{{ route('parents.index') }}" class="sidebar-item {{ request()->routeIs('parents.*') ? 'active' : '' }}">
            <i class="fas fa-user-friends"></i>
            {{ __('common.parents') }}
        </a>

        {{-- Clans & Families --}}
        <div class="sidebar-section-title">{{ __('common.structure') }}</div>

        <a href="{{ route('clans.index') }}" class="sidebar-item {{ request()->routeIs('clans.*') ? 'active' : '' }}">
            <i class="fas fa-shield-alt"></i>
            {{ __('common.clans') }}
        </a>

        <a href="{{ route('families.index') }}" class="sidebar-item {{ request()->routeIs('families.*') ? 'active' : '' }}">
            <i class="fas fa-home"></i>
            {{ __('common.families') }}
        </a>

        <a href="{{ route('branches.index') }}" class="sidebar-item {{ request()->routeIs('branches.*') ? 'active' : '' }}">
            <i class="fas fa-code-branch"></i>
            {{ __('common.branches') }}
        </a>

        {{-- Community --}}
        <div class="sidebar-section-title">{{ __('common.community') }}</div>

        <a href="{{ route('announcements.index') }}" class="sidebar-item {{ request()->routeIs('announcements.*') ? 'active' : '' }}">
            <i class="fas fa-bullhorn"></i>
            {{ __('common.announcements') }}
        </a>

        <a href="{{ route('campaigns.index') }}" class="sidebar-item {{ request()->routeIs('campaigns.*') ? 'active' : '' }}">
            <i class="fas fa-hand-holding-heart"></i>
            {{ __('common.campaigns') }}
        </a>

        <a href="{{ route('calendar.index') }}" class="sidebar-item {{ request()->routeIs('calendar.*') ? 'active' : '' }}">
            <i class="fas fa-calendar-alt"></i>
            {{ __('common.calendar') }}
        </a>

        {{-- Media & Records --}}
        <div class="sidebar-section-title">{{ __('common.records') }}</div>

        <a href="{{ route('galleries.index') }}" class="sidebar-item {{ request()->routeIs('galleries.*') ? 'active' : '' }}">
            <i class="fas fa-images"></i>
            {{ __('common.galleries') }}
        </a>

        <a href="{{ route('timeline.index') }}" class="sidebar-item {{ request()->routeIs('timeline.*') ? 'active' : '' }}">
            <i class="fas fa-stream"></i>
            {{ __('common.timeline') }}
        </a>

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
        <span><i class="fas fa-phone-alt mr-1"></i> +255 756 670 798</span>
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

</body>
</html>
