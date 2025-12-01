@extends('adminlte::page')

@section('title', 'Campaigns')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-hand-holding-usd"></i> Campaigns</h1>
        <a href="{{ route('campaigns.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> New Campaign
        </a>
    </div>
@stop

@section('content')
    <div class="row">
        @forelse($campaigns as $campaign)
            <div class="col-md-4">
                <div class="card card-widget widget-user">
                    <div class="widget-user-header bg-info">
                        <h3 class="widget-user-username">{{ $campaign->title }}</h3>
                        <h5 class="widget-user-desc">Target: {{ number_format($campaign->target_amount) }}</h5>
                    </div>
                    <div class="card-footer p-0">
                        <ul class="nav flex-column">
                            <li class="nav-item">
                                <span class="nav-link">
                                    Raised <span class="float-right badge bg-primary">{{ number_format($campaign->contributions_sum_amount ?? 0) }}</span>
                                </span>
                            </li>
                            <li class="nav-item">
                                <div class="progress progress-xs mt-2 mb-2 ml-3 mr-3">
                                    <div class="progress-bar bg-success" style="width: {{ $campaign->progress_percentage }}%"></div>
                                </div>
                                <span class="text-center d-block text-muted text-sm mb-2">{{ $campaign->progress_percentage }}% Complete</span>
                            </li>
                            <li class="nav-item">
                                <span class="nav-link">
                                    Status 
                                    <span class="float-right badge badge-{{ $campaign->status == 'active' ? 'success' : 'secondary' }}">
                                        {{ ucfirst($campaign->status) }}
                                    </span>
                                </span>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('campaigns.show', $campaign) }}" class="nav-link text-center bg-light">
                                    View Details <i class="fas fa-arrow-circle-right"></i>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info">
                    No campaigns found. Create one to start collecting contributions!
                </div>
            </div>
        @endforelse
    </div>
@stop
