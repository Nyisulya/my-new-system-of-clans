@extends('layouts.app')



@section('title', 'Dashboard')

@section('content_header')
    <h1>Dashboard</h1>
@stop

@section('content')
    {{-- Announcements Widget --}}
    @if($activeAnnouncements->count() > 0)
        <div class="row">
            <div class="col-12">
                <div class="card card-outline card-primary">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-bullhorn text-primary mr-1"></i> Announcements</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        @foreach($activeAnnouncements as $announcement)
                            <div class="callout callout-{{ $announcement->type }}">
                                <h5>{{ $announcement->title }}</h5>
                                <p class="text-dark">{{ $announcement->content }}</p>
                                <small class="text-muted">Posted {{ $announcement->start_date->diffForHumans() }}</small>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Info Boxes --}}
    <div class="row">
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-info elevation-1"><i class="fas fa-users"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Members</span>
                    <span class="info-box-number">{{ number_format($totalMembers) }}</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box mb-3">
                <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-sitemap"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Clans</span>
                    <span class="info-box-number">{{ number_format($totalClans) }}</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box mb-3">
                <span class="info-box-icon bg-success elevation-1"><i class="fas fa-house-user"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Families</span>
                    <span class="info-box-number">{{ number_format($totalFamilies) }}</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box mb-3">
                <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-rings"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Marriages</span>
                    <span class="info-box-number">{{ number_format($totalMarriages) }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Family Hierarchy Stats --}}
    <h5 class="mb-2">Family Hierarchy</h5>
    <div class="row">
        <div class="col-12 col-sm-4 col-md-4">
            <div class="info-box mb-3">
                <span class="info-box-icon bg-primary elevation-1"><i class="fas fa-user-friends"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Parents</span>
                    <span class="info-box-number">{{ number_format($totalParents) }}</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-4 col-md-4">
            <div class="info-box mb-3">
                <span class="info-box-icon bg-info elevation-1"><i class="fas fa-baby"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Children</span>
                    <span class="info-box-number">{{ number_format($totalChildren) }}</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-4 col-md-4">
            <div class="info-box mb-3">
                <span class="info-box-icon bg-purple elevation-1"><i class="fas fa-child"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Grandchildren</span>
                    <span class="info-box-number">{{ number_format($totalGrandchildren) }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- Recent Members --}}
        <div class="col-md-8">
            <div class="card">
                <div class="card-header border-transparent">
                    <h3 class="card-title">Latest Members</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table m-0">
                            <thead>
                            <tr>
                                <th>Name</th>
                                <th>Gender</th>
                                <th>Family</th>
                                <th>Status</th>
                                <th>Added</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($recentMembers as $member)
                                <tr>
                                    <td>
                                        <a href="{{ route('members.show', $member->id) }}">
                                            {{ $member->full_name }}
                                        </a>
                                    </td>
                                    <td>
                                        @if($member->gender == 'male')
                                            <i class="fas fa-mars text-primary"></i> Male
                                        @else
                                            <i class="fas fa-venus text-danger"></i> Female
                                        @endif
                                    </td>
                                    <td>{{ $member->family->name ?? 'N/A' }}</td>
                                    <td>
                                        @if($member->status == 'alive')
                                            <span class="badge badge-success">Alive</span>
                                        @else
                                            <span class="badge badge-secondary">Deceased</span>
                                        @endif
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $member->created_at->diffForHumans() }}</small>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">No members found.</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer clearfix">
                    <a href="{{ route('members.create') }}" class="btn btn-sm btn-info float-left">Add New Member</a>
                    <a href="{{ route('members.index') }}" class="btn btn-sm btn-secondary float-right">View All Members</a>
                </div>
            </div>
        </div>

        {{-- Statistics --}}
        <div class="col-md-4">
            {{-- Gender Stats --}}
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Demographics</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="progress-group">
                        Male
                        <span class="float-right"><b>{{ $maleCount }}</b>/{{ $totalMembers }}</span>
                        <div class="progress progress-sm">
                            <div class="progress-bar bg-primary" style="width: {{ $totalMembers > 0 ? ($maleCount / $totalMembers) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                    <div class="progress-group">
                        Female
                        <span class="float-right"><b>{{ $femaleCount }}</b>/{{ $totalMembers }}</span>
                        <div class="progress progress-sm">
                            <div class="progress-bar bg-danger" style="width: {{ $totalMembers > 0 ? ($femaleCount / $totalMembers) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Status Stats --}}
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Living Status</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="progress-group">
                        Alive
                        <span class="float-right"><b>{{ $aliveCount }}</b>/{{ $totalMembers }}</span>
                        <div class="progress progress-sm">
                            <div class="progress-bar bg-success" style="width: {{ $totalMembers > 0 ? ($aliveCount / $totalMembers) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                    <div class="progress-group">
                        Deceased
                        <span class="float-right"><b>{{ $deceasedCount }}</b>/{{ $totalMembers }}</span>
                        <div class="progress progress-sm">
                            <div class="progress-bar bg-secondary" style="width: {{ $totalMembers > 0 ? ($deceasedCount / $totalMembers) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('footer')
    <div class="float-right d-none d-sm-inline">
        <strong>Developed by:</strong> Felician Joseph Nyisulya | <i class="fas fa-phone"></i> +255 756 670 798 | <i class="fas fa-envelope"></i> felicianjoseph575@gmail.com
    </div>
    <strong>Copyright © 2025 <a href="#">Felician Joseph Nyisulya</a>.</strong> All rights reserved.
@endsection
