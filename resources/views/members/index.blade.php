@extends('adminlte::page')

@section('title', 'Members')

@section('content_header')
    <h1>
        <i class="fas fa-users"></i> 
        @if(request('category'))
            {{ ucwords(str_replace('_', ' ', request('category'))) }}
        @else
            Family Members
        @endif
        <small>Manage all family members</small>
    </h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                @if(request('category'))
                    {{ ucwords(str_replace('_', ' ', request('category'))) }} List
                @else
                    Members List
                @endif
            </h3>
            <div class="card-tools">
                <a href="{{ route('members.create') }}" class="btn btn-success btn-sm">
                    <i class="fas fa-plus"></i> <span class="d-none d-sm-inline">Add New Member</span>
                </a>
            </div>
        </div>
        
        {{-- Filters --}}
        <div class="card-body">
            <form action="{{ route('members.index') }}" method="GET" class="mb-3">
                <input type="hidden" name="category" value="{{ request('category') }}">
                <div class="row">
                    <div class="col-12 col-sm-6 col-md-3 mb-2">
                        <input type="text" name="search" class="form-control" placeholder="Search by name..." value="{{ request('search') }}">
                    </div>
                    <div class="col-12 col-sm-6 col-md-2 mb-2">
                        <select name="gender" class="form-control" onchange="this.form.submit()">
                            <option value="">All Genders</option>
                            <option value="male" {{ request('gender') == 'male' ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ request('gender') == 'female' ? 'selected' : '' }}>Female</option>
                        </select>
                    </div>
                    <div class="col-12 col-sm-6 col-md-2 mb-2">
                        <select name="status" class="form-control" onchange="this.form.submit()">
                            <option value="">All Status</option>
                            <option value="alive" {{ request('status') == 'alive' ? 'selected' : '' }}>Alive</option>
                            <option value="deceased" {{ request('status') == 'deceased' ? 'selected' : '' }}>Deceased</option>
                        </select>
                    </div>
                    <div class="col-12 col-sm-6 col-md-3 mb-2">
                        <select name="family_id" class="form-control" onchange="this.form.submit()">
                            <option value="">All Families</option>
                            @foreach($families as $family)
                                <option value="{{ $family->id }}" {{ request('family_id') == $family->id ? 'selected' : '' }}>
                                    {{ $family->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-md-2 mb-2">
                        <button type="submit" class="btn btn-primary btn-block w-100">
                            <i class="fas fa-search"></i> Filter
                        </button>
                    </div>
                </div>
            </form>

            {{-- Members Table --}}
            <div class="table-responsive">
                <table class="table table-hover no-mobile-cards">
                    <thead>
                        <tr>
                            <th>Photo</th>
                            <th>Name</th>
                            <th class="d-none d-sm-table-cell">Gender</th>
                            <th class="d-none d-md-table-cell">DOB</th>
                            <th class="d-none d-lg-table-cell">Age</th>
                            <th>Generation</th>
                            <th class="d-none d-md-table-cell">Family</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($members as $member)
                            <tr onclick="window.location='{{ route('members.dashboard', $member) }}'" style="cursor: pointer;">
                                <td>
                                    <img src="{{ $member->profile_photo_url }}" alt="{{ $member->full_name }}" 
                                         class="img-circle" width="40" height="40">
                                </td>
                                <td>
                                    <strong>{{ $member->full_name }}</strong>
                                </td>
                                <td class="d-none d-sm-table-cell">
                                    @if($member->gender == 'male')
                                        <i class="fas fa-mars text-primary"></i> <span class="d-none d-md-inline">Male</span>
                                    @elseif($member->gender == 'female')
                                        <i class="fas fa-venus text-danger"></i> <span class="d-none d-md-inline">Female</span>
                                    @else
                                        Other
                                    @endif
                                </td>
                                <td class="d-none d-md-table-cell">{{ $member->date_of_birth?->format('M d, Y') }}</td>
                                <td class="d-none d-lg-table-cell">{{ $member->age }} years</td>
                                <td><span class="badge badge-info">Gen {{ $member->generation_number }}</span></td>
                                <td class="d-none d-md-table-cell">{{ $member->family->name ?? 'N/A' }}</td>
                                <td>
                                    @if($member->status == 'alive')
                                        <span class="badge badge-success">Alive</span>
                                    @else
                                        <span class="badge badge-secondary">Deceased</span>
                                    @endif
                                </td>
                                <td onclick="event.stopPropagation();">
                                    <div class="action-buttons">
                                        <a href="{{ route('members.dashboard', $member) }}" class="btn btn-xs btn-primary" title="Dashboard">
                                            <i class="fas fa-tachometer-alt"></i>
                                        </a>
                                        <a href="{{ route('members.show', $member) }}" class="btn btn-xs btn-info" title="View Profile">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @can('update', $member)
                                            <a href="{{ route('members.edit', $member) }}" class="btn btn-xs btn-warning" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        @endcan
                                        @can('delete', $member)
                                            <form action="{{ route('members.destroy', $member) }}" method="POST" style="display: inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-xs btn-danger" 
                                                        onclick="return confirm('Are you sure?')" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center">
                                    <p class="text-muted">No members found. <a href="{{ route('members.create') }}">Add your first member</a></p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="mt-3 d-flex justify-content-center">
                {{ $members->links() }}
            </div>
        </div>
    </div>
@stop

@section('css')
    {{-- Mobile Responsive Styles --}}
    <link rel="stylesheet" href="{{ asset('css/mobile-responsive.css') }}">
    
    <style>
        /* Custom Action Buttons Container */
        .action-buttons {
            display: inline-flex;
            gap: 4px;
            vertical-align: middle;
        }
        
        /* Desktop/Tablet: Normal button sizes */
        @media (min-width: 768px) {
            .btn-xs {
                padding: 0.25rem 0.5rem !important;
                font-size: 0.875rem !important;
                line-height: 1.5 !important;
            }
        }
        
        /* Small screens/phones: Professional Compact View */
        @media (max-width: 767px) {
            /* Table adjustments for space */
            .table td, .table th {
                padding: 0.5rem 0.25rem !important;
                vertical-align: middle !important;
                white-space: nowrap;
            }
            
            /* Action column specific */
            td:last-child {
                width: 1%; /* Shrink to fit content */
                white-space: nowrap !important;
                text-align: right;
            }

            /* Action Buttons Container - Force Horizontal */
            .action-buttons {
                display: inline-flex !important;
                flex-direction: row !important;
                flex-wrap: nowrap !important;
                gap: 4px !important;
                align-items: center !important;
            }
            
            /* Individual Buttons */
            .action-buttons .btn-xs {
                width: 32px !important; /* Slightly larger touch target */
                height: 32px !important;
                padding: 0 !important;
                display: inline-flex !important;
                align-items: center !important;
                justify-content: center !important;
                border-radius: 4px !important;
                font-size: 14px !important; /* Clearer icon */
                line-height: 1 !important;
                border: 1px solid rgba(0,0,0,0.1); /* Subtle border */
                margin: 0 !important; /* Override global margins */
            }
            
            /* Icon sizing */
            .action-buttons .btn-xs i {
                font-size: 14px !important;
                margin: 0 !important;
                line-height: 1 !important;
            }
            
            /* Ensure delete form doesn't break layout */
            .action-buttons form {
                margin: 0 !important;
                display: inline-flex !important;
            }

            /* Filter/Search adjustments */
            .card-body .row .col-12 {
                margin-bottom: 0.5rem !important;
            }
            
            .form-control, .btn-block {
                height: 38px !important; /* Standard touch height */
                font-size: 14px !important;
            }
            
            /* Image adjustments */
            .img-circle {
                width: 36px !important;
                height: 36px !important;
            }
        }
    </style>
@stop

@section('js')
    {{-- Mobile Enhancements --}}
    <script src="{{ asset('js/mobile-enhancements.js') }}"></script>
@stop
