@extends('adminlte::page')

@section('title', 'Dashboard - Family Tree')

@section('content_header')
    <h1>
        <i class="fas fa-tachometer-alt"></i> Dashboard
        <small>Family Tree Overview</small>
    </h1>
@stop

@section('content')
    {{-- Statistics Cards --}}
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $stats['total_members'] }}</h3>
                    <p>Total Members</p>
                </div>
                <div class="icon">
                    <i class="fas fa-users"></i>
                </div>
                <a href="{{ route('members.index') }}" class="small-box-footer">
                    View List <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $stats['alive_members'] }}</h3>
                    <p>Living Members</p>
                </div>
                <div class="icon">
                    <i class="fas fa-heartbeat"></i>
                </div>
                <a href="{{ route('members.index', ['status' => 'alive']) }}" class="small-box-footer">
                    View List <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $stats['total_generations'] }}</h3>
                    <p>Generations</p>
                </div>
                <div class="icon">
                    <i class="fas fa-layer-group"></i>
                </div>
                <a href="{{ route('families.index') }}" class="small-box-footer">
                    View Families <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ $stats['deceased_members'] }}</h3>
                    <p>Deceased Members</p>
                </div>
                <div class="icon">
                    <i class="fas fa-dove"></i>
                </div>
                <a href="{{ route('members.index', ['status' => 'deceased']) }}" class="small-box-footer">
                    View List <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
    </div>
    
    {{-- Family Hierarchy Stats --}}
    <h5 class="mb-2 mt-4">Family Hierarchy</h5>
    <div class="row">
        <div class="col-lg-2 col-md-6 col-sm-6 col-6">
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3>{{ $totalParents }}</h3>
                    <p>Parents</p>
                </div>
                <div class="icon">
                    <i class="fas fa-user-friends"></i>
                </div>
                <a href="{{ route('members.index', ['category' => 'parents']) }}" class="small-box-footer">
                    View List <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        <div class="col-lg-2 col-md-6 col-sm-6 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $totalChildren }}</h3>
                    <p>Children</p>
                </div>
                <div class="icon">
                    <i class="fas fa-baby"></i>
                </div>
                <a href="{{ route('members.index', ['category' => 'children']) }}" class="small-box-footer">
                    View List <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        <div class="col-lg-2 col-md-6 col-sm-6 col-6">
            <div class="small-box bg-purple">
                <div class="inner">
                    <h3>{{ $totalGrandchildren }}</h3>
                    <p>Grandchildren</p>
                </div>
                <div class="icon">
                    <i class="fas fa-child"></i>
                </div>
                <a href="{{ route('members.index', ['category' => 'grandchildren']) }}" class="small-box-footer">
                    View List <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        <div class="col-lg-2 col-md-6 col-sm-6 col-6">
            <div class="small-box bg-teal">
                <div class="inner">
                    <h3>{{ $totalGreatGrandchildren }}</h3>
                    <p>Great-Grandchildren</p>
                </div>
                <div class="icon">
                    <i class="fas fa-leaf"></i>
                </div>
                <a href="{{ route('members.index', ['category' => 'great_grandchildren']) }}" class="small-box-footer">
                    View List <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        <div class="col-lg-2 col-md-6 col-sm-6 col-6">
            <div class="small-box bg-maroon">
                <div class="inner">
                    <h3>{{ $totalGreatGreatGrandchildren }}</h3>
                    <p>Great-Great-Grandchildren</p>
                </div>
                <div class="icon">
                    <i class="fas fa-seedling"></i>
                </div>
                <a href="{{ route('members.index', ['category' => 'great_great_grandchildren']) }}" class="small-box-footer">
                    View List <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        <div class="col-lg-2 col-md-6 col-sm-6 col-6">
            <div class="small-box bg-orange">
                <div class="inner">
                    <h3>{{ $totalGreatGreatGreatGrandchildren }}</h3>
                    <p>3x Great-Grandchildren</p>
                </div>
                <div class="icon">
                    <i class="fas fa-tree"></i>
                </div>
                <a href="{{ route('members.index', ['category' => 'great_great_great_grandchildren']) }}" class="small-box-footer">
                    View List <i class="fas fa-arrow-circle-right"></i>
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
                    <h3 class="card-title"><i class="fas fa-venus-mars"></i> Gender Distribution</h3>
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
                    <h3 class="card-title"><i class="fas fa-chart-bar"></i> Age Distribution</h3>
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
                    <h3 class="card-title"><i class="fas fa-user-plus"></i> Recently Added Members</h3>
                    <div class="card-tools">
                        <a href="{{ route('members.create') }}" class="btn btn-sm btn-primary">
                            <i class="fas fa-plus"></i> Add Member
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Gender</th>
                                <th>Generation</th>
                                <th>Family</th>
                                <th>Status</th>
                                <th>Added</th>
                                <th>Actions</th>
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
                                            <i class="fas fa-mars text-primary"></i> Male
                                        @elseif($member->gender == 'female')
                                            <i class="fas fa-venus text-danger"></i> Female
                                        @else
                                            <i class="fas fa-genderless"></i> Other
                                        @endif
                                    </td>
                                    <td><span class="badge badge-info">Gen {{ $member->generation_number }}</span></td>
                                    <td>{{ $member->family->name ?? 'N/A' }}</td>
                                    <td>
                                        @if($member->status == 'alive')
                                            <span class="badge badge-success">Alive</span>
                                        @else
                                            <span class="badge badge-secondary">Deceased</span>
                                        @endif
                                    </td>
                                    <td>{{ $member->created_at->diffForHumans() }}</td>
                                    <td>
                                        <a href="{{ route('members.show', $member) }}" class="btn btn-xs btn-info" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @can('update', $member)
                                            <a href="{{ route('members.edit', $member) }}" class="btn btn-xs btn-warning" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">No members added yet</td>
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
                    <h3 class="card-title"><i class="fas fa-bolt"></i> Quick Actions</h3>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('members.create') }}" class="btn btn-success btn-block mb-2">
                            <i class="fas fa-user-plus"></i> Add New Member
                        </a>
                        <a href="{{ route('members.index') }}" class="btn btn-primary btn-block mb-2">
                            <i class="fas fa-users"></i> View All Members
                        </a>
                        <a href="{{ route('clans.index') }}" class="btn btn-info btn-block mb-2">
                            <i class="fas fa-sitemap"></i> Manage Clans & Families
                        </a>
                        <a href="#" class="btn btn-warning btn-block mb-2">
                            <i class="fas fa-file-export"></i> Export Reports
                        </a>
                    </div>
                </div>
            </div>

            @if(isset($families) && $families->count() > 0)
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-home"></i> Families</h3>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @foreach($families as $family)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                {{ $family->name }}
                                <span class="badge badge-primary badge-pill">{{ $family->members_count }} members</span>
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
                    <h5 class="modal-title" id="categoryMembersModalLabel">Members List</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="categoryMembersModalBody">
                    <div class="text-center">
                        <i class="fas fa-spinner fa-spin fa-3x"></i>
                        <p class="mt-2">Loading members...</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    {{-- Load AdminLTE from CDN as fallback --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    {{-- Mobile Responsive Styles --}}
    <link rel="stylesheet" href="{{ asset('css/mobile-responsive.css') }}">
    
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
                labels: ['Male', 'Female'],
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
                    label: 'Members',
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
                
                $('#categoryMembersModalLabel').text(title + ' List');
                $('#categoryMembersModalBody').html('<div class="text-center"><i class="fas fa-spinner fa-spin fa-3x"></i><p class="mt-2">Loading members...</p></div>');
                $('#categoryMembersModal').modal('show');
                
                $.ajax({
                    url: '{{ route("dashboard.members") }}',
                    type: 'GET',
                    data: { category: category },
                    success: function(response) {
                        $('#categoryMembersModalBody').html(response.html);
                    },
                    error: function() {
                        $('#categoryMembersModalBody').html('<div class="alert alert-danger">Error loading members. Please try again.</div>');
                    }
                });
            });
        });
    </script>
    
    {{-- Mobile Enhancements --}}
    <script src="{{ asset('js/mobile-enhancements.js') }}"></script>
@stop
