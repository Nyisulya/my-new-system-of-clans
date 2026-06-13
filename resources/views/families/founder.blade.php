@extends('adminlte::page')

@section('title', $family->name . ' - Clan Founder')

@section('content_header')
    <h1>
        <i class="fas fa-crown"></i> {{ $family->name }} - Clan Founder
        <small>Generation 1</small>
    </h1>
@stop

@section('content')
    @if($founders->isEmpty())
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle"></i> No Generation 1 founders found for this family.
            <a href="{{ route('members.create', ['family_id' => $family->id, 'generation_number' => 1]) }}" class="btn btn-sm btn-primary ml-2">
                <i class="fas fa-plus"></i> Add Founder
            </a>
        </div>
    @else
        @foreach($founders as $founder)
            <div class="row">
                <!-- Profile Card -->
                <div class="col-md-4">
                    <div class="card card-primary card-outline">
                        <div class="card-body box-profile">
                            <div class="text-center">
                                <img class="profile-user-img img-fluid img-circle"
                                     src="{{ $founder->profile_photo_url }}"
                                     alt="Founder profile picture">
                            </div>
                            <h3 class="profile-username text-center">{{ $founder->full_name }}</h3>
                            <p class="text-muted text-center">
                                {{ $founder->occupation ?? 'Clan Founder' }}
                                <span class="badge badge-warning ml-2"><i class="fas fa-crown"></i> Generation 1</span>
                            </p>
                            <ul class="list-group list-group-unbordered mb-3">
                                <li class="list-group-item"><b>Clan</b> <a class="float-right">{{ $founder->clan->name ?? 'N/A' }}</a></li>
                                <li class="list-group-item"><b>Family</b> <a class="float-right">{{ $founder->family->name ?? 'N/A' }}</a></li>
                                <li class="list-group-item"><b>Generation</b> <a class="float-right">{{ $founder->generation_number }}</a></li>
                                
                                @php 
                                    $spouses = $founder->spouses();
                                    $spouseCount = $spouses->count();
                                @endphp
                                
                                @if($spouseCount > 0)
                                    <li class="list-group-item">
                                        <b>{{ $founder->gender == 'male' ? ($spouseCount > 1 ? 'Wives' : 'Wife') : 'Husband' }}</b>
                                        <div class="float-right text-right">
                                            @foreach($spouses as $index => $spouse)
                                                <a href="{{ route('members.dashboard', $spouse->id) }}">
                                                    {{ $spouse->full_name }}
                                                </a>
                                                @if(!$loop->last)<br>@endif
                                            @endforeach
                                        </div>
                                    </li>
                                @endif
                                
                                <li class="list-group-item"><b>Status</b>
                                    <a class="float-right">
                                        @if($founder->status == 'alive')
                                            <span class="badge badge-success">Alive</span>
                                        @else
                                            <span class="badge badge-secondary">Deceased</span>
                                        @endif
                                    </a>
                                </li>
                            </ul>
                            <div class="d-grid gap-2">
                                <a href="{{ route('members.edit', $founder) }}" class="btn btn-primary btn-block"><i class="fas fa-edit"></i> Edit Profile</a>
                                
                                @php 
                                    $firstSpouse = $spouses->first();
                                @endphp
                                
                                <a href="{{ route('members.create', [
                                    'father_id' => $founder->gender == 'male' ? $founder->id : ($firstSpouse->id ?? null),
                                    'mother_id' => $founder->gender == 'female' ? $founder->id : ($firstSpouse->id ?? null),
                                    'clan_id' => $founder->clan_id,
                                    'family_id' => $founder->family_id
                                ]) }}" class="btn btn-success btn-block"><i class="fas fa-child"></i> Add Child</a>
                                
                                @if($founder->gender == 'male')
                                    <a href="{{ route('members.create', [
                                        'spouse_id' => $founder->id,
                                        'gender' => 'female'
                                    ]) }}" class="btn btn-info btn-block">
                                        <i class="fas fa-heart"></i> {{ $spouseCount > 0 ? 'Add Another Wife' : 'Add Wife' }}
                                    </a>
                                @elseif($founder->gender == 'female' && $spouseCount == 0)
                                    <a href="{{ route('members.create', [
                                        'spouse_id' => $founder->id,
                                        'gender' => 'male'
                                    ]) }}" class="btn btn-info btn-block">
                                        <i class="fas fa-heart"></i> Add Husband
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabs -->
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header p-2">
                            <ul class="nav nav-pills">
                                <li class="nav-item"><a class="nav-link active" href="#family-tree-{{ $founder->id }}" data-toggle="tab">Family Tree</a></li>
                                <li class="nav-item"><a class="nav-link" href="#contact-info-{{ $founder->id }}" data-toggle="tab">Contact Info</a></li>
                                <li class="nav-item"><a class="nav-link" href="#children-{{ $founder->id }}" data-toggle="tab">Children</a></li>
                                <li class="nav-item"><a class="nav-link" href="#details-{{ $founder->id }}" data-toggle="tab">Details</a></li>
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="tab-content">
                                <!-- Family Tree Tab -->
                                <div class="tab-pane active" id="family-tree-{{ $founder->id }}">
                                    @if($founder->father || $founder->mother)
                                        <h5><i class="fas fa-user-friends"></i> Parents</h5>
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <h6><i class="fas fa-mars text-primary"></i> Father</h6>
                                                        @if($founder->father)
                                                            <a href="{{ route('members.dashboard', $founder->father->id) }}">
                                                                {{ $founder->father->full_name }}
                                                            </a>
                                                            <br><small class="text-muted">Generation {{ $founder->father->generation_number }}</small>
                                                        @else
                                                            <span class="text-muted">Not recorded</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <h6><i class="fas fa-venus text-danger"></i> Mother</h6>
                                                        @if($founder->mother)
                                                            <a href="{{ route('members.dashboard', $founder->mother->id) }}">
                                                                {{ $founder->mother->full_name }}
                                                            </a>
                                                            <br><small class="text-muted">Generation {{ $founder->mother->generation_number }}</small>
                                                        @else
                                                            <span class="text-muted">Not recorded</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    
                                    @if($spouseCount > 0)
                                        <h5><i class="fas fa-heart text-danger"></i> {{ $founder->gender == 'male' ? ($spouseCount > 1 ? 'Wives' : 'Wife') : 'Husband' }}</h5>
                                        @foreach($spouses as $index => $spouse)
                                            <div class="card mb-3">
                                                <div class="card-body">
                                                    @if($founder->gender == 'male' && $spouseCount > 1)
                                                        <h6><i class="fas fa-heart text-danger"></i> Wife {{ $index + 1 }}</h6>
                                                    @endif
                                                    <a href="{{ route('members.dashboard', $spouse->id) }}">
                                                        {{ $spouse->full_name }}
                                                    </a>
                                                    <br><small class="text-muted">{{ ucfirst($spouse->status) }}</small>
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle"></i> No spouse recorded.
                                        </div>
                                    @endif
                                </div>
                                <!-- Contact Info Tab -->
                                <div class="tab-pane" id="contact-info-{{ $founder->id }}">
                                    <ul class="list-group list-group-unbordered mb-3">
                                        <li class="list-group-item"><b>Email</b> <a class="float-right">{{ $founder->email ?? 'N/A' }}</a></li>
                                        <li class="list-group-item"><b>Phone</b> <a class="float-right">{{ $founder->phone ?? 'N/A' }}</a></li>
                                        <li class="list-group-item"><b>Address</b> <a class="float-right">{{ $founder->address ?? 'N/A' }}</a></li>
                                        <li class="list-group-item"><b>City</b> <a class="float-right">{{ $founder->city ?? 'N/A' }}</a></li>
                                        <li class="list-group-item"><b>Country</b> <a class="float-right">{{ $founder->country ?? 'N/A' }}</a></li>
                                    </ul>
                                </div>
                                <!-- Children Tab -->
                                <div class="tab-pane" id="children-{{ $founder->id }}">
                                    @php
                                        $children = $founder->children()->get();
                                    @endphp
                                    @if($children->count() > 0)
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Name</th>
                                                    <th>Gender</th>
                                                    <th>Status</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($children as $child)
                                                    <tr>
                                                        <td><a href="{{ route('members.dashboard', $child->id) }}">{{ $child->full_name }}</a></td>
                                                        <td>
                                                            @if($child->gender == 'male')
                                                                <i class="fas fa-mars text-primary"></i>
                                                            @elseif($child->gender == 'female')
                                                                <i class="fas fa-venus text-danger"></i>
                                                            @else
                                                                <i class="fas fa-genderless"></i>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if($child->status == 'alive')
                                                                <span class="badge badge-success">Alive</span>
                                                            @else
                                                                <span class="badge badge-secondary">Deceased</span>
                                                            @endif
                                                        </td>
                                                        <td><a href="{{ route('members.dashboard', $child->id) }}" class="btn btn-xs btn-info"><i class="fas fa-tachometer-alt"></i> Dashboard</a></td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    @else
                                        <div class="alert alert-info"><i class="fas fa-info-circle"></i> No children recorded for this founder.</div>
                                        <a href="{{ route('members.create', [
                                            'father_id' => $founder->gender == 'male' ? $founder->id : ($firstSpouse->id ?? null),
                                            'mother_id' => $founder->gender == 'female' ? $founder->id : ($firstSpouse->id ?? null),
                                            'clan_id' => $founder->clan_id,
                                            'family_id' => $founder->family_id
                                        ]) }}" class="btn btn-primary"><i class="fas fa-plus"></i> Add First Child</a>
                                    @endif
                                </div>
                                <!-- Details Tab -->
                                <div class="tab-pane" id="details-{{ $founder->id }}">
                                    <dl class="row">
                                        <dt class="col-sm-4">Full Name</dt><dd class="col-sm-8">{{ $founder->full_name }}</dd>
                                        <dt class="col-sm-4">Gender</dt><dd class="col-sm-8">{{ ucfirst($founder->gender) }}</dd>
                                        <dt class="col-sm-4">Date of Birth</dt><dd class="col-sm-8">{{ $founder->date_of_birth ? $founder->date_of_birth->format('F j, Y') : 'N/A' }}</dd>
                                        <dt class="col-sm-4">Place of Birth</dt><dd class="col-sm-8">{{ $founder->place_of_birth ?? 'N/A' }}</dd>
                                        <dt class="col-sm-4">Occupation</dt><dd class="col-sm-8">{{ $founder->occupation ?? 'N/A' }}</dd>
                                        <dt class="col-sm-4">Biography</dt><dd class="col-sm-8">{{ $founder->biography ?? 'No biography available.' }}</dd>
                                        @if($founder->status == 'deceased')
                                            <dt class="col-sm-4">Date of Death</dt><dd class="col-sm-8">{{ $founder->date_of_death ? $founder->date_of_death->format('F j, Y') : 'N/A' }}</dd>
                                            <dt class="col-sm-4">Place of Death</dt><dd class="col-sm-8">{{ $founder->place_of_death ?? 'N/A' }}</dd>
                                        @endif
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <a href="{{ route('families.parents', $family) }}" class="btn btn-success btn-block">
                                <i class="fas fa-users"></i> View Founder's Parents
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('members.dashboard', $founder->id) }}" class="btn btn-primary btn-block">
                                <i class="fas fa-tachometer-alt"></i> Full Dashboard
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('families.tree', $family) }}" class="btn btn-info btn-block">
                                <i class="fas fa-sitemap"></i> Complete Family Tree
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
        @endforeach
    @endif
@stop
