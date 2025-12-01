@extends('adminlte::page')

@section('title', $family->name . ' - Founder Parents')

@section('content_header')
    <h1>
        <i class="fas fa-users"></i> {{ $family->name }} - Founder Parents
        <small>Generation 0 (Ancestors)</small>
    </h1>
@stop

@section('content')
    {{-- Summary Statistics --}}
    <div class="row">
        <div class="col-md-4">
            <div class="info-box bg-info">
                <span class="info-box-icon"><i class="fas fa-crown"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Clan Founders</span>
                    <span class="info-box-number">{{ $founders->count() }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="info-box bg-success">
                <span class="info-box-icon"><i class="fas fa-user-friends"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Parents Recorded</span>
                    <span class="info-box-number">{{ $generation0Members->count() }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="info-box bg-warning">
                <span class="info-box-icon"><i class="fas fa-exclamation-circle"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Founders Without Parents</span>
                    @php
                        $foundersWithoutParents = $founders->filter(function($founder) {
                            return !$founder->father_id && !$founder->mother_id;
                        })->count();
                    @endphp
                    <span class="info-box-number">{{ $foundersWithoutParents }}</span>
                </div>
            </div>
        </div>
    </div>

    @if($founders->isEmpty())
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle"></i> No Generation 1 founders found for this family.
            <a href="{{ route('members.create', ['family_id' => $family->id, 'generation_number' => 1]) }}" class="btn btn-sm btn-primary ml-2">
                <i class="fas fa-plus"></i> Add Founder
            </a>
        </div>
    @else
        {{-- Display each founder with their parents --}}
        @foreach($founders as $founder)
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-crown text-warning"></i>
                        <strong>{{ $founder->full_name }}</strong>
                        <span class="badge badge-primary ml-2">Clan Founder (Generation 1)</span>
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('members.dashboard', $founder->id) }}" class="btn btn-tool" title="View Dashboard">
                            <i class="fas fa-tachometer-alt"></i>
                        </a>
                        <form action="{{ route('members.destroy', $founder->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this founder?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-tool text-danger" title="Delete Founder">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        {{-- Founder Info --}}
                        <div class="col-md-4">
                            <div class="text-center mb-3">
                                <img src="{{ $founder->profile_photo_url }}" alt="{{ $founder->full_name }}" 
                                     class="img-fluid img-circle elevation-2 mb-2"
                                     style="width: 120px; height: 120px; object-fit: cover;">
                                <h5 class="mb-1">{{ $founder->full_name }}</h5>
                                <p class="text-muted mb-0">
                                    @if($founder->date_of_birth)
                                        Born: {{ $founder->date_of_birth->format('Y') }}
                                    @endif
                                    @if($founder->status == 'deceased' && $founder->date_of_death)
                                        - Died: {{ $founder->date_of_death->format('Y') }}
                                    @endif
                                </p>
                                @if($founder->occupation)
                                    <p class="text-muted"><i class="fas fa-briefcase"></i> {{ $founder->occupation }}</p>
                                @endif
                            </div>
                        </div>

                        {{-- Father Section --}}
                        <div class="col-md-4">
                            <div class="card {{ $founder->father ? 'card-success' : 'card-secondary' }}">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        <i class="fas fa-male"></i> Father
                                    </h3>
                                </div>
                                <div class="card-body">
                                    @if($founder->father)
                                        <div class="text-center">
                                            <img src="{{ $founder->father->profile_photo_url }}" alt="{{ $founder->father->full_name }}"
                                                 class="img-circle elevation-2 mb-2"
                                                 style="width: 80px; height: 80px; object-fit: cover;">
                                            <h6 class="mb-1">{{ $founder->father->full_name }}</h6>
                                            <p class="text-muted small mb-0">
                                                @if($founder->father->date_of_birth)
                                                    Born: {{ $founder->father->date_of_birth->format('Y') }}
                                                @endif
                                            </p>
                                            <span class="badge badge-light">Generation {{ $founder->father->generation_number }}</span>
                                        </div>
                                        <ul class="list-group list-group-unbordered mt-3">
                                            @if($founder->father->status)
                                                <li class="list-group-item p-2">
                                                    <b>Status</b>
                                                    <span class="float-right">
                                                        @if($founder->father->status == 'alive')
                                                            <span class="badge badge-success">Alive</span>
                                                        @else
                                                            <span class="badge badge-secondary">Deceased</span>
                                                        @endif
                                                    </span>
                                                </li>
                                            @endif
                                            @if($founder->father->occupation)
                                                <li class="list-group-item p-2">
                                                    <b>Occupation</b>
                                                    <span class="float-right">{{ $founder->father->occupation }}</span>
                                                </li>
                                            @endif
                                        </ul>
                                        <div class="mt-3">
                                            <a href="{{ route('members.edit', $founder->father->id) }}" class="btn btn-sm btn-primary btn-block">
                                                <i class="fas fa-edit"></i> Edit Father
                                            </a>
                                            <a href="{{ route('members.dashboard', $founder->father->id) }}" class="btn btn-sm btn-info btn-block">
                                                <i class="fas fa-eye"></i> View Dashboard
                                            </a>
                                        </div>
                                    @else
                                        <div class="text-center text-muted py-4">
                                            <i class="fas fa-user-slash fa-3x mb-3"></i>
                                            <p>No father recorded</p>
                                            <a href="{{ route('members.create', [
                                                'family_id' => $family->id,
                                                'clan_id' => $founder->clan_id,
                                                'gender' => 'male',
                                                'generation_number' => 0
                                            ]) }}?child_id={{ $founder->id }}&parent_type=father" 
                                               class="btn btn-success btn-sm">
                                                <i class="fas fa-plus"></i> Add Father
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Mother Section --}}
                        <div class="col-md-4">
                            <div class="card {{ $founder->mother ? 'card-success' : 'card-secondary' }}">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        <i class="fas fa-female"></i> Mother
                                    </h3>
                                </div>
                                <div class="card-body">
                                    @if($founder->mother)
                                        <div class="text-center">
                                            <img src="{{ $founder->mother->profile_photo_url }}" alt="{{ $founder->mother->full_name }}"
                                                 class="img-circle elevation-2 mb-2"
                                                 style="width: 80px; height: 80px; object-fit: cover;">
                                            <h6 class="mb-1">{{ $founder->mother->full_name }}</h6>
                                            <p class="text-muted small mb-0">
                                                @if($founder->mother->date_of_birth)
                                                    Born: {{ $founder->mother->date_of_birth->format('Y') }}
                                                @endif
                                            </p>
                                            <span class="badge badge-light">Generation {{ $founder->mother->generation_number }}</span>
                                        </div>
                                        <ul class="list-group list-group-unbordered mt-3">
                                            @if($founder->mother->status)
                                                <li class="list-group-item p-2">
                                                    <b>Status</b>
                                                    <span class="float-right">
                                                        @if($founder->mother->status == 'alive')
                                                            <span class="badge badge-success">Alive</span>
                                                        @else
                                                            <span class="badge badge-secondary">Deceased</span>
                                                        @endif
                                                    </span>
                                                </li>
                                            @endif
                                            @if($founder->mother->occupation)
                                                <li class="list-group-item p-2">
                                                    <b>Occupation</b>
                                                    <span class="float-right">{{ $founder->mother->occupation }}</span>
                                                </li>
                                            @endif
                                        </ul>
                                        <div class="mt-3">
                                            <a href="{{ route('members.edit', $founder->mother->id) }}" class="btn btn-sm btn-primary btn-block">
                                                <i class="fas fa-edit"></i> Edit Mother
                                            </a>
                                            <a href="{{ route('members.dashboard', $founder->mother->id) }}" class="btn btn-sm btn-info btn-block">
                                                <i class="fas fa-eye"></i> View Dashboard
                                            </a>
                                        </div>
                                    @else
                                        <div class="text-center text-muted py-4">
                                            <i class="fas fa-user-slash fa-3x mb-3"></i>
                                            <p>No mother recorded</p>
                                            <a href="{{ route('members.create', [
                                                'family_id' => $family->id,
                                                'clan_id' => $founder->clan_id,
                                                'gender' => 'female',
                                                'generation_number' => 0
                                            ]) }}?child_id={{ $founder->id }}&parent_type=mother" 
                                               class="btn btn-success btn-sm">
                                                <i class="fas fa-plus"></i> Add Mother
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @endif

    {{-- Navigation Buttons --}}
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <a href="{{ route('families.founder', $family) }}" class="btn btn-warning btn-block">
                        <i class="fas fa-crown"></i> View Clan Founder
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="{{ route('families.tree', $family) }}" class="btn btn-info btn-block">
                        <i class="fas fa-sitemap"></i> View Family Tree
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="{{ route('families.show', $family) }}" class="btn btn-primary btn-block">
                        <i class="fas fa-eye"></i> View Family Details
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="{{ route('families.index') }}" class="btn btn-secondary btn-block">
                        <i class="fas fa-arrow-left"></i> Back to Families
                    </a>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        .info-box-number {
            font-size: 2rem;
            font-weight: bold;
        }
        .card-outline.card-primary {
            border-top: 3px solid #007bff;
        }
    </style>
@stop
