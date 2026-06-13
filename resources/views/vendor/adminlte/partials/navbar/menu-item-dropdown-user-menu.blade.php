@php( $logout_url = View::hasSection('logout_url') ? View::getSection('logout_url') : config('adminlte.logout_url', 'logout') )
@php( $profile_url = View::hasSection('profile_url') ? View::getSection('profile_url') : config('adminlte.profile_url', 'logout') )

@if (config('adminlte.use_route_url', false))
    @php( $logout_url = $logout_url ? route($logout_url) : '' )
    @php( $profile_url = $profile_url ? route($profile_url) : '' )
@else
    @php( $logout_url = $logout_url ? url($logout_url) : '' )
    @php( $profile_url = $profile_url ? url($profile_url) : '' )
@endif

<li class="nav-item dropdown user-menu">
    <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">
        @if(config('adminlte.usermenu_image'))
            <img src="{{ Auth::user()->adminlte_image() }}" class="user-image img-circle elevation-2" alt="{{ Auth::user()->name }}">
        @endif
        <span class="d-none d-md-inline text-truncate" style="max-width: 150px; display: inline-block; vertical-align: middle;">
            {{ Auth::user()->name }}
        </span>
    </a>
    <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
        @if(!config('adminlte.usermenu_header_class', 'bg-primary'))
            @php($header_class = 'bg-primary')
        @else
            @php($header_class = config('adminlte.usermenu_header_class', 'bg-primary'))
        @endif

        <!-- User image -->
        <li class="user-header {{ $header_class }}">
            @if(config('adminlte.usermenu_image'))
                <img src="{{ Auth::user()->adminlte_image() }}" class="img-circle elevation-2" alt="{{ Auth::user()->name }}">
            @endif
            <p>
                {{ Auth::user()->name }}
                <small>Member since {{ Auth::user()->created_at->format('M. Y') }}</small>
            </p>
        </li>

        <!-- Menu Footer-->
        <li class="user-footer">
            @if($profile_url)
                <a href="{{ $profile_url }}" class="btn btn-default btn-flat">Profile</a>
            @endif
            <a href="#" class="btn btn-default btn-flat float-right"
               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                Log Out
            </a>
            <form id="logout-form" action="{{ $logout_url }}" method="POST" style="display: none;">
                @if(config('adminlte.logout_method'))
                    {{ method_field(config('adminlte.logout_method')) }}
                @endif
                {{ csrf_field() }}
            </form>
        </li>
    </ul>
</li>
