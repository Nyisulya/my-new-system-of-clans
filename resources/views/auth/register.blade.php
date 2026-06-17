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

@section('auth_header', 'Sajili Mwanachama Mpya')

@section('auth_body')
    <form action="{{ $register_url }}" method="post">
        @csrf

        {{-- Name field --}}
        <div class="input-group mb-3">
            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                   value="{{ old('name') }}" placeholder="Jina Kamili" autofocus required>
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
            <input id="password" type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                   placeholder="Nenosiri (Password)" required>
            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-lock"></span>
                </div>
                <button type="button" class="btn btn-outline-secondary password-toggle" data-target="password" aria-label="Onyesha nenosiri">
                    <span class="fas fa-eye"></span>
                </button>
            </div>
            @error('password')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        {{-- Confirm password field --}}
        <div class="input-group mb-3">
            <input id="password_confirmation" type="password" name="password_confirmation" class="form-control"
                   placeholder="Thibitisha Nenosiri" required>
            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-lock"></span>
                </div>
                <button type="button" class="btn btn-outline-secondary password-toggle" data-target="password_confirmation" aria-label="Onyesha nenosiri">
                    <span class="fas fa-eye"></span>
                </button>
            </div>
        </div>

        {{-- Info message --}}
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> 
            <small>Jina lako litatumika kama jina la kuingilia kwenye mfumo. Unaweza kusasisha maelezo ya wasifu wako baada ya usajili.</small>
        </div>

        {{-- Register button --}}
        <button type="submit" class="btn btn-primary btn-block">
            <span class="fas fa-user-plus"></span>
            Sajili
        </button>

    </form>
@stop

@section('auth_footer')
    <p class="mb-0">
        <a href="{{ route('login') }}">
            Tayari nina akaunti
        </a>
    </p>
@stop

@section('adminlte_js')
    {{-- Mobile Enhancements --}}
    <script src="{{ asset('js/mobile-enhancements.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.password-toggle').forEach(function (button) {
                button.addEventListener('click', function () {
                    var targetId = button.getAttribute('data-target');
                    var input = document.getElementById(targetId);
                    if (!input) return;
                    input.type = input.type === 'password' ? 'text' : 'password';
                    button.setAttribute('aria-label', input.type === 'password' ? 'Onyesha nenosiri' : 'Ficha nenosiri');
                });
            });
        });
    </script>
@stop