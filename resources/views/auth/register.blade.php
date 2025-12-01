@extends('adminlte::auth.auth-page', ['auth_type' => 'register'])

@section('adminlte_css_pre')
    <link rel="stylesheet" href="{{ asset('vendor/icheck-bootstrap/icheck-bootstrap.min.css') }}">
    {{-- Mobile Responsive Styles --}}
    <link rel="stylesheet" href="{{ asset('css/mobile-responsive.css') }}">
@stop

@php( $register_url = View::hasSection('register_url') ? View::getSection('register_url') : config('adminlte.register_url', 'register') )
@php( $dashboard_url = View::hasSection('dashboard_url') ? View::getSection('dashboard_url') : config('adminlte.dashboard_url', 'home') )

@if (config('adminlte.use_route_url', false))
    @php( $register_url = $register_url ? route($register_url) : '' )
    @php( $dashboard_url = $dashboard_url ? route($dashboard_url) : '' )
@else
    @php( $register_url = $register_url ? url($register_url) : '' )
    @php( $dashboard_url = $dashboard_url ? url($dashboard_url) : '' )
@endif

@section('auth_header', __('Register New Member'))

@section('auth_body')
    <form action="{{ $register_url }}" method="post">
        @csrf

        {{-- Name field --}}
        <div class="input-group mb-3">
            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                   value="{{ old('name') }}" placeholder="{{ __('Full Name') }}" autofocus required>
            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-user"></span>
                </div>
            </div>
            @error('name')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        {{-- Password field --}}
        <div class="input-group mb-3">
            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                   placeholder="{{ __('Password') }}" required>
            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-lock"></span>
                </div>
            </div>
            @error('password')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        {{-- Confirm password field --}}
        <div class="input-group mb-3">
            <input type="password" name="password_confirmation" class="form-control"
                   placeholder="{{ __('Confirm Password') }}" required>
            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-lock"></span>
                </div>
            </div>
        </div>

        {{-- Info message --}}
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> 
            <small>Your name will be used as your username to login. You can update your profile details after registration.</small>
        </div>

        {{-- Register button --}}
        <button type="submit" class="btn btn-primary btn-block">
            <span class="fas fa-user-plus"></span>
            {{ __('Register') }}
        </button>

    </form>
@stop

@section('auth_footer')
    <p class="mb-0">
        <a href="{{ route('login') }}">
            {{ __('I already have an account') }}
        </a>
    </p>
@stop

@section('adminlte_js')
    {{-- Mobile Enhancements --}}
    <script src="{{ asset('js/mobile-enhancements.js') }}"></script>
@stop