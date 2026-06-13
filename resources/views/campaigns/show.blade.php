@extends('adminlte::page')

@section('title', $campaign->title)

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>{{ $campaign->title }}</h1>
        <div>
            <a href="{{ route('campaigns.edit', $campaign) }}" class="btn btn-info btn-sm">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="{{ route('campaigns.index') }}" class="btn btn-default btn-sm">Back</a>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-md-4">
            {{-- Progress Card --}}
            <div class="card card-widget widget-user-2">
                <div class="widget-user-header bg-primary">
                    <div class="widget-user-image">
                        <i class="fas fa-hand-holding-usd fa-3x text-white-50"></i>
                    </div>
                    <h3 class="widget-user-username">Progress</h3>
                    <h5 class="widget-user-desc">Target: {{ number_format($campaign->target_amount) }}</h5>
                </div>
                <div class="card-footer p-0">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <span class="nav-link">
                                Raised <span class="float-right badge bg-success">{{ number_format($totalRaised) }}</span>
                            </span>
                        </li>
                        <li class="nav-item">
                            <span class="nav-link">
                                Remaining <span class="float-right badge bg-danger">{{ number_format(max(0, $campaign->target_amount - $totalRaised)) }}</span>
                            </span>
                        </li>
                        <li class="nav-item">
                            <div class="p-3">
                                <div class="progress progress-sm">
                                    <div class="progress-bar bg-success" style="width: {{ $progress }}%"></div>
                                </div>
                                <small class="text-muted">{{ $progress }}% Complete</small>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>

            {{-- Add Contribution Button --}}
            @if($campaign->status == 'active')
                <a href="{{ route('contributions.create', ['campaign_id' => $campaign->id]) }}" class="btn btn-success btn-block btn-lg mb-3">
                    <i class="fas fa-plus-circle"></i> Record Contribution
                </a>
            @endif
        </div>

        <div class="col-md-8">
            {{-- Contributions List --}}
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Contributions</h3>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Member</th>
                                <th>Method</th>
                                <th>Amount</th>
                                <th>Notes</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($campaign->contributions as $contribution)
                                <tr>
                                    <td>{{ $contribution->date->format('M d, Y') }}</td>
                                    <td>
                                        @if($contribution->member)
                                            <a href="{{ route('members.show', $contribution->member_id) }}">
                                                {{ $contribution->member->full_name }}
                                            </a>
                                        @else
                                            <span class="text-muted">Unknown</span>
                                        @endif
                                    </td>
                                    <td>{{ $contribution->method }}</td>
                                    <td class="text-success font-weight-bold">{{ number_format($contribution->amount) }}</td>
                                    <td>{{ $contribution->notes }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">No contributions yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@stop
