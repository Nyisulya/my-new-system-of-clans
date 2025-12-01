@extends('adminlte::page')

@section('title', __('common.dashboard'))

@section('content_header')
    <h1>{{ __('common.dashboard') }}</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">{{ __('common.members') }}</h3>
        </div>
        <div class="card-body">
            <p><strong>{{ __('common.name') }}:</strong> {{ __('common.view') }}</p>
            <p><strong>{{ __('common.status') }}:</strong> {{ __('common.alive') }}</p>
            <button class="btn btn-primary">{{ __('common.add') }}</button>
            <button class="btn btn-success">{{ __('common.save') }}</button>
            <button class="btn btn-secondary">{{ __('common.cancel') }}</button>
        </div>
    </div>

    <div class="alert alert-info mt-3">
        <h4>Language Test</h4>
        <ul>
            <li>{{ __('common.dashboard') }}</li>
            <li>{{ __('common.members') }}</li>
            <li>{{ __('common.families') }}</li>
            <li>{{ __('common.notifications') }}</li>
            <li>{{ __('common.contributions') }}</li>
            <li>{{ __('common.gallery') }}</li>
        </ul>
    </div>
@stop
