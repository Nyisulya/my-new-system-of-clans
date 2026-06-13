@extends('adminlte::page')

@section('title', $member->full_name . ' - Dashboard')

@section('content_header')
<h1>
    <i class="fas fa-user-circle"></i> {{ $member->full_name }}
    <small>Member Dashboard</small>
</h1>
@stop

@section('content')
<div class="row">
    <!-- Profile Card -->
    <div class="col-md-4">
        <div class="card card-primary card-outline">
            <div class="card-body box-profile">
                <div class="text-center">
                    <img class="profile-user-img img-fluid img-circle"
                         src="{{ $member->profile_photo_url }}"
                         alt="User profile picture">
                </div>
                <h3 class="profile-username text-center">{{ $member->full_name }}</h3>
                <p class="text-muted text-center">{{ $member->occupation ?? 'Member' }}</p>
                <ul class="list-group list-group-unbordered mb-3">
                    <li class="list-group-item"><b>Clan</b> <a class="float-right">{{ $member->clan->name ?? 'N/A' }}</a></li>
                    <li class="list-group-item"><b>Family</b> <a class="float-right">{{ $member->family->name ?? 'N/A' }}</a></li>
                    <li class="list-group-item"><b>Generation</b> <a class="float-right">{{ $member->generation_number }}</a></li>
                    
                    @php 
                        $spouses = $member->spouses();
                        $spouseCount = $spouses->count();
                    @endphp
                    
                    @if($spouseCount > 0)
                        <li class="list-group-item">
                            <b>{{ $member->gender == 'male' ? ($spouseCount > 1 ? 'Wives' : 'Wife') : 'Husband' }}</b>
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
                            @if($member->status == 'alive')
                                <span class="badge badge-success">Alive</span>
                            @else
                                <span class="badge badge-secondary">Deceased</span>
                            @endif
                        </a>
                    </li>
                </ul>
                <div class="d-grid gap-2">
                    <a href="{{ route('members.edit', $member) }}" class="btn btn-primary btn-block"><i class="fas fa-edit"></i> Edit Profile</a>
                    
                    @php 
                        $spouses = $member->spouses();
                        $spouseCount = $spouses->count();
                        $firstSpouse = $spouses->first();
                    @endphp
                    
                    <a href="{{ route('members.create', [
                        'father_id' => $member->gender == 'male' ? $member->id : ($firstSpouse->id ?? null),
                        'mother_id' => $member->gender == 'female' ? $member->id : ($firstSpouse->id ?? null),
                        'clan_id' => $member->clan_id,
                        'family_id' => $member->family_id
                    ]) }}" class="btn btn-success btn-block"><i class="fas fa-child"></i> Add Child</a>
                    
                    @if($member->gender == 'male')
                        {{-- Always show for males to support polygamy --}}
                        <a href="{{ route('members.create', [
                            'spouse_id' => $member->id,
                            'gender' => 'female'
                        ]) }}" class="btn btn-info btn-block">
                            <i class="fas fa-heart"></i> {{ $spouseCount > 0 ? 'Add Another Wife' : 'Add Wife' }}
                        </a>
                    @elseif($member->gender == 'female' && $spouseCount == 0)
                        {{-- Only show for females if they have no husband --}}
                        <a href="{{ route('members.create', [
                            'spouse_id' => $member->id,
                            'gender' => 'male'
                        ]) }}" class="btn btn-info btn-block">
                            <i class="fas fa-heart"></i> Add Husband
                        </a>
                    @endif
                    
                    @can('delete', $member)
                        <form action="{{ route('members.destroy', $member) }}" method="POST" class="mt-2">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-block" 
                                    onclick="return confirm('Are you sure you want to delete this member? This action cannot be undone and will remove all associated data.')">
                                <i class="fas fa-trash"></i> Delete Profile
                            </button>
                        </form>
                    @endcan
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header p-2">
                <ul class="nav nav-pills">
                    <li class="nav-item"><a class="nav-link active" href="#family-tree" data-toggle="tab">Family Tree</a></li>
                    <li class="nav-item"><a class="nav-link" href="#contact-info" data-toggle="tab">Contact Info</a></li>
                    <li class="nav-item"><a class="nav-link" href="#location" data-toggle="tab">Location</a></li>
                    <li class="nav-item"><a class="nav-link" href="#children" data-toggle="tab">Children</a></li>
                    <li class="nav-item"><a class="nav-link" href="#details" data-toggle="tab">Details</a></li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content">
                    <!-- Family Tree Tab -->
                    <div class="tab-pane active" id="family-tree">
                        @if($member->father || $member->mother)
                            <h5><i class="fas fa-user-friends"></i> Parents</h5>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-body">
                                            <h6><i class="fas fa-mars text-primary"></i> Father</h6>
                                            @if($member->father)
                                                <a href="{{ route('members.dashboard', $member->father->id) }}">
                                                    {{ $member->father->full_name }}
                                                </a>
                                                <br><small class="text-muted">Generation {{ $member->father->generation_number }}</small>
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
                                            @if($member->mother)
                                                <a href="{{ route('members.dashboard', $member->mother->id) }}">
                                                    {{ $member->mother->full_name }}
                                                </a>
                                                <br><small class="text-muted">Generation {{ $member->mother->generation_number }}</small>
                                            @else
                                                <span class="text-muted">Not recorded</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                        
                        @php 
                            $spouses = $member->spouses();
                            $spouseCount = $spouses->count();
                        @endphp
                        
                        @if($spouseCount > 0)
                            <h5><i class="fas fa-heart text-danger"></i> {{ $member->gender == 'male' ? ($spouseCount > 1 ? 'Wives' : 'Wife') : 'Husband' }}</h5>
                            @foreach($spouses as $index => $spouse)
                                <div class="card mb-3">
                                    <div class="card-body">
                                        @if($member->gender == 'male' && $spouseCount > 1)
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
                        
                        @php
                            // Get siblings - members with same parents
                            $siblings = collect();
                            if ($member->father_id || $member->mother_id) {
                                $siblings = \App\Models\Member::where('id', '!=', $member->id)
                                    ->where(function($query) use ($member) {
                                        if ($member->father_id) {
                                            $query->where('father_id', $member->father_id);
                                        }
                                        if ($member->mother_id) {
                                            $query->orWhere('mother_id', $member->mother_id);
                                        }
                                    })
                                    ->get();
                            }
                        @endphp
                        
                        @if($siblings->count() > 0)
                            <h5 class="mt-3"><i class="fas fa-users"></i> Siblings</h5>
                            <ul class="list-group">
                                @foreach($siblings as $sibling)
                                    <li class="list-group-item">
                                        <a href="{{ route('members.dashboard', $sibling->id) }}">{{ $sibling->full_name }}</a>
                                        <span class="badge badge-info float-right">Gen {{ $sibling->generation_number }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                    <!-- Contact Info Tab -->
                    <div class="tab-pane" id="contact-info">
                        <ul class="list-group list-group-unbordered mb-3">
                            <li class="list-group-item"><b>Email</b> <a class="float-right">{{ $member->email ?? 'N/A' }}</a></li>
                            <li class="list-group-item"><b>Phone</b> <a class="float-right">{{ $member->phone ?? 'N/A' }}</a></li>
                            <li class="list-group-item"><b>Address</b> <a class="float-right">{{ $member->address ?? 'N/A' }}</a></li>
                            <li class="list-group-item"><b>City</b> <a class="float-right">{{ $member->city ?? 'N/A' }}</a></li>
                            <li class="list-group-item"><b>Country</b> <a class="float-right">{{ $member->country ?? 'N/A' }}</a></li>
                        </ul>
                    </div>
                    
                    <!-- Location Tab -->
                    <div class="tab-pane" id="location">
                        @if($member->current_lat && $member->current_lng)
                            <h5><i class="fas fa-map-marked-alt text-primary"></i> My Current Residence</h5>
                            
                            {{-- Map Container --}}
                            <div id="memberLocationMap" style="height: 450px; width: 100%; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 20px;"></div>
                            
                            {{-- Address Information --}}
                            <div class="card">
                                <div class="card-body">
                                    <h6><i class="fas fa-home text-success"></i> Full Address</h6>
                                    <p class="mb-1">
                                        @php
                                            $addressParts = array_filter([
                                                $member->address,
                                                $member->street,
                                                $member->city,
                                                $member->district,
                                                $member->region,
                                                $member->country
                                            ]);
                                            $fullAddress = implode(', ', $addressParts);
                                        @endphp
                                        <strong>{{ $fullAddress ?: 'No detailed address available' }}</strong>
                                    </p>
                                    
                                    <hr>
                                    
                                    <div class="row small text-muted">
                                        <div class="col-md-6">
                                            <i class="fas fa-map-pin"></i> <strong>Coordinates:</strong><br>
                                            Latitude: {{ number_format($member->current_lat, 6) }}°<br>
                                            Longitude: {{ number_format($member->current_lng, 6) }}°
                                        </div>
                                        <div class="col-md-6">
                                            <i class="fas fa-info-circle"></i> <strong>Map Info:</strong><br>
                                            Zoom: Street level view<br>
                                            Layers: Street & Satellite available
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> 
                                <strong>No Location Data</strong><br>
                                Your residential location has not been recorded yet. 
                                You can <a href="{{ route('members.edit', $member) }}" class="alert-link">edit your profile</a> to add location information.
                            </div>
                            
                            @if($member->country || $member->city)
                            <div class="card">
                                <div class="card-body">
                                    <h6><i class="fas fa-map-marker-alt"></i> Available Location Info</h6>
                                    <dl class="row mb-0">
                                        @if($member->country)
                                        <dt class="col-sm-4">Country</dt>
                                        <dd class="col-sm-8">{{ $member->country }}</dd>
                                        @endif
                                        
                                        @if($member->region)
                                        <dt class="col-sm-4">Region</dt>
                                        <dd class="col-sm-8">{{ $member->region }}</dd>
                                        @endif
                                        
                                        @if($member->district)
                                        <dt class="col-sm-4">District</dt>
                                        <dd class="col-sm-8">{{ $member->district }}</dd>
                                        @endif
                                        
                                        @if($member->city)
                                        <dt class="col-sm-4">City</dt>
                                        <dd class="col-sm-8">{{ $member->city }}</dd>
                                        @endif
                                    </dl>
                                </div>
                            </div>
                            @endif
                        @endif
                    </div>
                    
                    <!-- Children Tab -->
                    <div class="tab-pane" id="children">
                        @if(isset($children) && $children->count() > 0)
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
                            <div class="alert alert-info"><i class="fas fa-info-circle"></i> No children recorded for this member.</div>
                            @php $firstSpouse = $member->spouses()->first(); @endphp
                            <a href="{{ route('members.create', [
                                'father_id' => $member->gender == 'male' ? $member->id : ($firstSpouse->id ?? null),
                                'mother_id' => $member->gender == 'female' ? $member->id : ($firstSpouse->id ?? null),
                                'clan_id' => $member->clan_id,
                                'family_id' => $member->family_id
                            ]) }}" class="btn btn-primary"><i class="fas fa-plus"></i> Add First Child</a>
                        @endif
                    </div>
                    <!-- Details Tab -->
                    <div class="tab-pane" id="details">
                        <dl class="row">
                            <dt class="col-sm-4">Full Name</dt><dd class="col-sm-8">{{ $member->full_name }}</dd>
                            <dt class="col-sm-4">Gender</dt><dd class="col-sm-8">{{ ucfirst($member->gender) }}</dd>
                            <dt class="col-sm-4">Date of Birth</dt><dd class="col-sm-8">{{ $member->date_of_birth ? $member->date_of_birth->format('F j, Y') : 'N/A' }}</dd>
                            <dt class="col-sm-4">Occupation</dt><dd class="col-sm-8">{{ $member->occupation ?? 'N/A' }}</dd>
                            <dt class="col-sm-4">Biography</dt><dd class="col-sm-8">{{ $member->biography ?? 'No biography available.' }}</dd>
                            @if($member->status == 'deceased')
                                <dt class="col-sm-4">Date of Death</dt><dd class="col-sm-8">{{ $member->date_of_death ? $member->date_of_death->format('F j, Y') : 'N/A' }}</dd>
                                <dt class="col-sm-4">Place of Death</dt><dd class="col-sm-8">{{ $member->place_of_death ?? 'N/A' }}</dd>
                            @endif
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
    {{-- Leaflet CSS --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" 
          integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" 
          crossorigin="anonymous"/>
    
    <style>
        #memberLocationMap {
            z-index: 1;
        }
        .leaflet-popup-content-wrapper {
            border-radius: 8px;
        }
        .leaflet-popup-content {
            font-family: inherit;
            margin: 13px 19px;
        }
        .custom-popup h6 {
            margin: 0 0 5px 0;
            color: #007bff;
            font-weight: 600;
        }
        .custom-popup p {
            margin: 0;
            font-size: 13px;
            color: #666;
        }
    </style>
@stop

@section('js')
    {{-- Leaflet JavaScript --}}
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" 
            integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" 
            crossorigin="anonymous"></script>
    
    <script>
        $(document).ready(function() {
            // Check if member has coordinates
            @if($member->current_lat && $member->current_lng)
                // Initialize map when Location tab is shown
                $('a[href="#location"]').on('shown.bs.tab', function (e) {
                    // Only initialize map once
                    if (!window.memberMap) {
                        initializeMemberMap();
                    }
                });
                
                // If location tab is already active (direct link), initialize immediately
                if ($('#location').hasClass('active')) {
                    initializeMemberMap();
                }
                
                function initializeMemberMap() {
                    const lat = {{ $member->current_lat }};
                    const lng = {{ $member->current_lng }};
                    const memberName = "{{ $member->full_name }}";
                    const fullAddress = @json($fullAddress ?? 'Location not specified');
                    
                    // Create map with smart zoom level for detailed street view
                    window.memberMap = L.map('memberLocationMap', {
                        center: [lat, lng],
                        zoom: 16, // Street-level detail
                        zoomControl: true,
                        scrollWheelZoom: true
                    });
                    
                    // Define base layers for smart map switching
                    const streetMap = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                        maxZoom: 19,
                        className: 'map-tiles'
                    });
                    
                    const satelliteMap = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
                        attribution: 'Tiles &copy; Esri &mdash; Source: Esri, i-cubed, USDA, USGS, AEX, GeoEye, Getmapping, Aerogrid, IGN, IGP, UPR-EGP, and the GIS User Community',
                        maxZoom: 19
                    });
                    
                    const hybridMap = L.layerGroup([
                        L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
                            maxZoom: 19
                        }),
                        L.tileLayer('https://stamen-tiles-{s}.a.ssl.fastly.net/toner-hybrid/{z}/{x}/{y}.png', {
                            attribution: 'Map tiles by <a href="http://stamen.com">Stamen Design</a>, <a href="http://creativecommons.org/licenses/by/3.0">CC BY 3.0</a> &mdash; Map data &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
                            subdomains: 'abcd',
                            maxZoom: 19,
                            opacity: 0.5
                        })
                    ]);
                    
                    // Add default layer (Street Map)
                    streetMap.addTo(window.memberMap);
                    
                    // Layer control for smart switching
                    const baseLayers = {
                        "<i class='fas fa-map'></i> Street Map": streetMap,
                        "<i class='fas fa-globe'></i> Satellite": satelliteMap,
                        "<i class='fas fa-layer-group'></i> Hybrid": hybridMap
                    };
                    
                    L.control.layers(baseLayers, null, {
                        position: 'topright',
                        collapsed: false
                    }).addTo(window.memberMap);
                    
                    // Create custom icon for member location
                    const homeIcon = L.divIcon({
                        className: 'custom-div-icon',
                        html: "<div style='background-color:#007bff; width: 30px; height: 30px; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 5px rgba(0,0,0,0.3); display: flex; align-items: center; justify-content: center;'><i class='fas fa-home' style='color: white; font-size: 14px;'></i></div>",
                        iconSize: [30, 30],
                        iconAnchor: [15, 15]
                    });
                    
                    // Add marker with custom popup
                    const marker = L.marker([lat, lng], { icon: homeIcon }).addTo(window.memberMap);
                    
                    // Custom popup content
                    const popupContent = `
                        <div class="custom-popup">
                            <h6><i class="fas fa-user"></i> ${memberName}</h6>
                            <p><i class="fas fa-map-marker-alt"></i> ${fullAddress}</p>
                            <hr style="margin: 8px 0;">
                            <small class="text-muted">
                                <i class="fas fa-home"></i> My Current Residence<br>
                                <i class="fas fa-crosshairs"></i> ${lat.toFixed(6)}°, ${lng.toFixed(6)}°
                            </small>
                        </div>
                    `;
                    
                    marker.bindPopup(popupContent, {
                        maxWidth: 300,
                        className: 'member-location-popup'
                    }).openPopup();
                    
                    // Add circle to show approximate area (100m radius)
                    L.circle([lat, lng], {
                        color: '#007bff',
                        fillColor: '#007bff',
                        fillOpacity: 0.1,
                        radius: 100,
                        weight: 2
                    }).addTo(window.memberMap);
                    
                    // Add scale control
                    L.control.scale({
                        imperial: false,
                        metric: true
                    }).addTo(window.memberMap);
                    
                    // Invalidate size after a short delay to ensure proper rendering
                    setTimeout(function() {
                        window.memberMap.invalidateSize();
                    }, 100);
                }
            @endif
        });
    </script>
@stop
