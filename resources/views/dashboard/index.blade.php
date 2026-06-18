@extends('layouts.app')

@section('title', __('common.clan_title'))
@section('page_title', __('common.dashboard'))

@section('content_header')
    <h1>
        <i class="fas fa-tachometer-alt"></i> {{ __('common.dashboard') }}
        <small>{{ __('common.clans') }}</small>
    </h1>
@stop

@section('content')
    @if(auth()->user()->member_id === null && !auth()->user()->isAdmin())
        <div class="alert alert-warning alert-dismissible fade show mb-4 pr-5" role="alert">
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between">
                <div class="mb-2 mb-md-0">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    {{ __('common.unlinked_profile_warning') }}
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('profile.claim.search') }}" class="btn btn-sm btn-primary mr-2 font-weight-bold">
                        <i class="fas fa-search mr-1"></i> {{ __('common.find_my_profile') }}
                    </a>
                    <a href="{{ route('members.create') }}" class="btn btn-sm btn-success font-weight-bold">
                        <i class="fas fa-user-plus mr-1"></i> {{ __('common.register_new_profile') }}
                    </a>
                </div>
            </div>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    {{-- Statistics Cards --}}
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $stats['total_members'] }}</h3>
                    <p>{{ __('common.all_members') }}</p>
                </div>
                <div class="icon">
                    <i class="fas fa-users"></i>
                </div>
                <a href="{{ route('members.index') }}" class="small-box-footer">
                    {{ __('common.view_list') }} <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $stats['alive_members'] }}</h3>
                    <p>{{ __('common.alive_members') }}</p>
                </div>
                <div class="icon">
                    <i class="fas fa-heartbeat"></i>
                </div>
                <a href="{{ route('members.index', ['status' => 'alive']) }}" class="small-box-footer">
                    {{ __('common.view_list') }} <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $stats['total_generations'] }}</h3>
                    <p>{{ __('common.generations') }}</p>
                </div>
                <div class="icon">
                    <i class="fas fa-layer-group"></i>
                </div>
                <a href="{{ route('families.index') }}" class="small-box-footer">
                    {{ __('common.view_generations') }} <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ $stats['deceased_members'] }}</h3>
                    <p>{{ __('common.deceased_members') }}</p>
                </div>
                <div class="icon">
                    <i class="fas fa-dove"></i>
                </div>
                <a href="{{ route('members.index', ['status' => 'deceased']) }}" class="small-box-footer">
                    {{ __('common.view_list') }} <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
    </div>
    
    <h5 class="mb-2 mt-4">{{ __('common.positions_in_clan') }}</h5>
    <div class="row">
        <div class="col-lg-2 col-md-6 col-sm-6 col-6">
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3>{{ $totalParents }}</h3>
                    <p>{{ __('common.parents') }}</p>
                </div>
                <div class="icon">
                    <i class="fas fa-user-friends"></i>
                </div>
                <a href="{{ route('members.index', ['category' => 'parents']) }}" class="small-box-footer">
                    {{ __('common.view_list') }} <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        <div class="col-lg-2 col-md-6 col-sm-6 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $totalChildren }}</h3>
                    <p>{{ __('common.children') }}</p>
                </div>
                <div class="icon">
                    <i class="fas fa-baby"></i>
                </div>
                <a href="{{ route('members.index', ['category' => 'children']) }}" class="small-box-footer">
                    {{ __('common.view_list') }} <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        <div class="col-lg-2 col-md-6 col-sm-6 col-6">
            <div class="small-box bg-purple">
                <div class="inner">
                    <h3>{{ $totalGrandchildren }}</h3>
                    <p>{{ __('common.grandchildren') }}</p>
                </div>
                <div class="icon">
                    <i class="fas fa-child"></i>
                </div>
                <a href="{{ route('members.index', ['category' => 'grandchildren']) }}" class="small-box-footer">
                    {{ __('common.view_list') }} <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        <div class="col-lg-2 col-md-6 col-sm-6 col-6">
            <div class="small-box bg-teal">
                <div class="inner">
                    <h3>{{ $totalGreatGrandchildren }}</h3>
                    <p>{{ __('common.great_grandchildren') }}</p>
                </div>
                <div class="icon">
                    <i class="fas fa-leaf"></i>
                </div>
                <a href="{{ route('members.index', ['category' => 'great_grandchildren']) }}" class="small-box-footer">
                    {{ __('common.view_list') }} <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        <div class="col-lg-2 col-md-6 col-sm-6 col-6">
            <div class="small-box bg-maroon">
                <div class="inner">
                    <h3>{{ $totalGreatGreatGrandchildren }}</h3>
                    <p>{{ __('common.great_great_grandchildren') }}</p>
                </div>
                <div class="icon">
                    <i class="fas fa-seedling"></i>
                </div>
                <a href="{{ route('members.index', ['category' => 'great_great_grandchildren']) }}" class="small-box-footer">
                    {{ __('common.view_list') }} <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        <div class="col-lg-2 col-md-6 col-sm-6 col-6">
            <div class="small-box bg-orange">
                <div class="inner">
                    <h3>{{ $totalGreatGreatGreatGrandchildren }}</h3>
                    <p>{{ __('common.great_great_great_grandchildren') }}</p>
                </div>
                <div class="icon">
                    <i class="fas fa-tree"></i>
                </div>
                <a href="{{ route('members.index', ['category' => 'great_great_great_grandchildren']) }}" class="small-box-footer">
                    {{ __('common.view_list') }} <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
    </div>

    @can('admin-only')
    <div class="row">
        {{-- Gender Distribution --}}
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-venus-mars"></i> {{ __('common.gender_distribution') }}</h3>
                </div>
                <div class="card-body">
                    <canvas id="genderChart" style="height: 250px;"></canvas>
                </div>
            </div>
        </div>

        {{-- Age Distribution --}}
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-chart-bar"></i> {{ __('common.age_distribution') }}</h3>
                </div>
                <div class="card-body">
                    <canvas id="ageChart" style="height: 250px;"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- Recent Members --}}
        <div class="col-md-8 col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-user-plus"></i> {{ __('common.recently_added_members') }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('members.create') }}" class="btn btn-sm btn-primary">
                            <i class="fas fa-plus"></i> {{ __('common.add_member') }}
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>{{ __('common.name') }}</th>
                                <th>{{ __('common.gender') ?? __('common.photo') }}</th>
                                <th>{{ __('common.generation') }}</th>
                                <th>{{ __('common.family') }}</th>
                                <th>{{ __('common.status') }}</th>
                                <th>{{ __('common.added') }}</th>
                                <th>{{ __('common.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentMembers as $member)
                                <tr>
                                    <td>
                                        <strong>{{ $member->full_name }}</strong>
                                    </td>
                                    <td>
                                        @if($member->gender == 'male')
                                            <i class="fas fa-mars text-primary"></i> {{ __('common.male') }}
                                        @elseif($member->gender == 'female')
                                            <i class="fas fa-venus text-danger"></i> {{ __('common.female') }}
                                        @else
                                            <i class="fas fa-genderless"></i> {{ __('common.other_gender') }}
                                        @endif
                                    </td>
                                    <td><span class="badge badge-info">{{ __('common.generation') }} {{ $member->generation_number }}</span></td>
                                    <td>{{ $member->family->name ?? 'N/A' }}</td>
                                    <td>
                                        @if($member->status == 'alive')
                                            <span class="badge badge-success">{{ __('common.alive') }}</span>
                                        @else
                                            <span class="badge badge-secondary">{{ __('common.deceased') }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $member->created_at->diffForHumans() }}</td>
                                    <td>
                                        <a href="{{ route('members.dashboard', $member) }}" class="btn btn-xs btn-info" title="{{ __('common.view') }}">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @can('update', $member)
                                            <a href="{{ route('members.edit', $member) }}" class="btn btn-xs btn-warning" title="{{ __('common.edit') }}">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">{{ __('common.no_members_added_yet') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Quick Actions --}}
        <div class="col-md-4 col-12">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-bolt"></i> {{ __('common.quick_actions') }}</h3>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('members.create') }}" class="btn btn-success btn-block mb-2">
                            <i class="fas fa-user-plus"></i> {{ __('common.add_new_member') }}
                        </a>
                        <a href="{{ route('members.index') }}" class="btn btn-primary btn-block mb-2">
                            <i class="fas fa-users"></i> {{ __('common.view_all_members') }}
                        </a>
                        <a href="{{ route('clans.index') }}" class="btn btn-info btn-block mb-2">
                            <i class="fas fa-sitemap"></i> {{ __('common.manage_clan_families') }}
                        </a>
                        <a href="#" class="btn btn-warning btn-block mb-2">
                            <i class="fas fa-file-export"></i> {{ __('common.export_report') }}
                        </a>
                    </div>
                </div>
            </div>

            @if(isset($families) && $families->count() > 0)
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-home"></i> {{ __('common.families') }}</h3>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @foreach($families as $family)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                {{ $family->name }}
                                <span class="badge badge-primary badge-pill">{{ __('common.members_count', ['count' => $family->members_count]) }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @endif
        </div>
    </div>
    @endcan

    {{-- Category Members Modal --}}
    <div class="modal fade" id="categoryMembersModal" tabindex="-1" role="dialog" aria-labelledby="categoryMembersModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="categoryMembersModalLabel">{{ __('common.members_list') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="categoryMembersModalBody">
                    <div class="text-center">
                        <i class="fas fa-spinner fa-spin fa-3x"></i>
                        <p class="mt-2">{{ __('common.loading_members') }}</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('common.close') }}</button>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    
    <style>
        .small-box h3 {
            font-size: 2.5rem;
        }
        
        /* Additional mobile optimizations */
        @media (max-width: 767px) {
            .small-box h3 {
                font-size: 2rem;
            }
            .small-box p {
                font-size: 13px;
            }
        }
    </style>
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    <script>
        // Gender Distribution Chart
        const genderCtx = document.getElementById('genderChart').getContext('2d');
        new Chart(genderCtx, {
            type: 'pie',
            data: {
                labels: ['{{ __('common.male') }}', '{{ __('common.female') }}'],
                datasets: [{
                    data: [{{ $stats['male_count'] }}, {{ $stats['female_count'] }}],
                    backgroundColor: ['#3498db', '#e74c3c'],
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                    }
                }
            }
        });

        // Age Distribution Chart
        const ageCtx = document.getElementById('ageChart').getContext('2d');
        new Chart(ageCtx, {
            type: 'bar',
            data: {
                labels: @json($stats['age_distribution']->keys()),
                datasets: [{
                    label: '{{ __('common.members') }}',
                    data: @json($stats['age_distribution']->values()),
                    backgroundColor: '#3498db',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });

        // Category Members Modal
        $(document).ready(function() {
            $('.view-category').click(function(e) {
                e.preventDefault();
                var category = $(this).data('category');
                var title = $(this).data('title');
                
                $('#categoryMembersModalLabel').text('Orodha ya ' + title);
                $('#categoryMembersModalBody').html('<div class="text-center"><i class="fas fa-spinner fa-spin fa-3x"></i><p class="mt-2">{{ __('common.loading_members') }}</p></div>');
                $('#categoryMembersModal').modal('show');
                
                $.ajax({
                    url: '{{ route("dashboard.members") }}',
                    type: 'GET',
                    data: { category: category },
                    success: function(response) {
                        $('#categoryMembersModalBody').html(response.html);
                    },
                    error: function() {
                        $('#categoryMembersModalBody').html('<div class="alert alert-danger">Itilafu imetokea wakati wa kupakia wanachama. Tafadhali jaribu tena.</div>');
                    }
                });
            });
        });
    </script>
    
    {{-- Mobile Enhancements --}}
    <script src="{{ asset('js/mobile-enhancements.js') }}"></script>
@stop
