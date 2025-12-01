@extends('adminlte::page')

@section('title', 'Member Profile - ' . $member->full_name)

@section('content_header')
    <h1>
        <i class="fas fa-user"></i> Member Profile
    </h1>
@stop

@section('content')
    @if(session('success'))
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            {{ session('success') }}
        </div>
    @endif

    <div class="row">
        {{-- Profile Card --}}
        <div class="col-md-4 col-12 mb-3">
            <div class="card card-primary card-outline">
                <div class="card-body box-profile">
                    <div class="text-center">
                        <img class="profile-user-img img-fluid img-circle" 
                             src="{{ $member->profile_photo_url }}" 
                             alt="{{ $member->full_name }}"
                             style="width: 150px; height: 150px; object-fit: cover;">
                    </div>

                    <h3 class="profile-username text-center">{{ $member->full_name }}</h3>

                    <p class="text-muted text-center">
                        @if($member->status == 'alive')
                            <span class="badge badge-success">Alive</span>
                        @else
                            <span class="badge badge-secondary">Deceased</span>
                        @endif
                        <span class="badge badge-info">Generation {{ $member->generation_number }}</span>
                    </p>

                    <ul class="list-group list-group-unbordered mb-3">
                        <li class="list-group-item">
                            <b>Gender</b> 
                            <span class="float-right">
                                @if($member->gender == 'male')
                                    <i class="fas fa-mars text-primary"></i> Male
                                @elseif($member->gender == 'female')
                                    <i class="fas fa-venus text-danger"></i> Female
                                @else
                                    Other
                                @endif
                            </span>
                        </li>
                        <li class="list-group-item">
                            <b>Age</b> <span class="float-right">{{ $member->age }} years</span>
                        </li>
                        <li class="list-group-item">
                            <b>Occupation</b> <span class="float-right">{{ $member->occupation ?? 'N/A' }}</span>
                        </li>
                        @if($member->email)
                        <li class="list-group-item">
                            <b>Email</b> <span class="float-right"><a href="mailto:{{ $member->email }}">{{ $member->email }}</a></span>
                        </li>
                        @endif
                        @if($member->phone)
                        <li class="list-group-item">
                            <b>Phone</b> <span class="float-right">{{ $member->phone }}</span>
                        </li>
                        @endif
                    </ul>

                    @can('update', $member)
                        <a href="{{ route('members.edit', $member) }}" class="btn btn-warning btn-block"><i class="fas fa-edit"></i> Edit Profile</a>
                        
                        @php
                            $spouses = $member->spouses();
                            $spouseCount = is_countable($spouses) ? $spouses->count() : 0;
                            $firstSpouse = null;
                            if ($spouseCount > 0) {
                                $firstSpouse = $spouses->first();
                            }
                        @endphp
                        
                        <a href="{{ route('members.create', [
                            'father_id' => $member->gender == 'male' ? $member->id : ($firstSpouse?->id ?? null),
                            'mother_id' => $member->gender == 'female' ? $member->id : ($firstSpouse?->id ?? null),
                            'clan_id' => $member->clan_id,
                            'family_id' => $member->family_id
                        ]) }}" class="btn btn-success btn-block mt-2">
                            <i class="fas fa-baby"></i> Add Child
                        </a>
                        
                        @if($member->gender == 'male')
                            {{-- Always show for males to support polygamy --}}
                            <a href="{{ route('members.create', [
                                'spouse_id' => $member->id,
                                'gender' => 'female'
                            ]) }}" class="btn btn-info btn-block mt-2">
                                <i class="fas fa-heart"></i> {{ $spouseCount > 0 ? 'Add Another Wife' : 'Add Wife' }}
                            </a>
                        @elseif($member->gender == 'female' && $spouseCount == 0)
                            {{-- Only show for females if they have no husband --}}
                            <a href="{{ route('members.create', [
                                'spouse_id' => $member->id,
                                'gender' => 'male'
                            ]) }}" class="btn btn-info btn-block mt-2">
                                <i class="fas fa-heart"></i> Add Husband
                            </a>
                        @endif
                    @endcan
                    @can('delete', $member)
                        <form action="{{ route('members.destroy', $member) }}" method="POST" class="mt-2">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-block" onclick="return confirm('Are you sure you want to delete this member?')">
                                <i class="fas fa-trash"></i> Delete Member
                            </button>
                        </form>
                    @endcan
                </div>
            </div>
        </div>

        {{-- Details Tabs --}}
        <div class="col-md-8 col-12">
            <div class="card">
                <div class="card-header p-2">
                    <ul class="nav nav-pills">
                        <li class="nav-item"><a class="nav-link active" href="#details" data-toggle="tab">Details</a></li>
                        <li class="nav-item"><a class="nav-link" href="#family" data-toggle="tab">Family Tree</a></li>
                        <li class="nav-item"><a class="nav-link" href="#location" data-toggle="tab">Location</a></li>
                        <li class="nav-item"><a class="nav-link" href="#timeline" data-toggle="tab">Timeline</a></li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content">
                        {{-- Details Tab --}}
                        <div class="active tab-pane" id="details">
                            <h5><i class="fas fa-calendar"></i> Birth Information</h5>
                            <dl class="row">
                                <dt class="col-sm-4">Date of Birth</dt>
                                <dd class="col-sm-8">{{ $member->date_of_birth?->format('F d, Y') ?? 'N/A' }}</dd>

                                <dt class="col-sm-4">Place of Birth</dt>
                                <dd class="col-sm-8">{{ $member->place_of_birth ?? 'N/A' }}</dd>
                            </dl>

                            @if($member->status == 'deceased')
                            <h5 class="mt-3"><i class="fas fa-dove"></i> Death Information</h5>
                            <dl class="row">
                                <dt class="col-sm-4">Date of Death</dt>
                                <dd class="col-sm-8">{{ $member->date_of_death?->format('F d, Y') ?? 'N/A' }}</dd>

                                <dt class="col-sm-4">Place of Death</dt>
                                <dd class="col-sm-8">{{ $member->place_of_death ?? 'N/A' }}</dd>
                            </dl>
                            @endif

                            <h5 class="mt-3"><i class="fas fa-sitemap"></i> Family Organization</h5>
                            <dl class="row">
                                <dt class="col-sm-4">Clan</dt>
                                <dd class="col-sm-8">{{ $member->clan->name ?? 'N/A' }}</dd>

                                <dt class="col-sm-4">Family</dt>
                                <dd class="col-sm-8">{{ $member->family->name ?? 'N/A' }}</dd>

                                <dt class="col-sm-4">Branch</dt>
                                <dd class="col-sm-8">{{ $member->branch->name ?? 'N/A' }}</dd>

                                <dt class="col-sm-4">Generation</dt>
                                <dd class="col-sm-8"><span class="badge badge-info">{{ $member->generation_number }}</span></dd>
                            </dl>

                            @if($member->address || $member->city || $member->country)
                            <h5 class="mt-3"><i class="fas fa-map-marker-alt"></i> Location</h5>
                            <dl class="row">
                                @if($member->address)
                                <dt class="col-sm-4">Address</dt>
                                <dd class="col-sm-8">{{ $member->address }}</dd>
                                @endif

                                @if($member->city)
                                <dt class="col-sm-4">City</dt>
                                <dd class="col-sm-8">{{ $member->city }}</dd>
                                @endif

                                @if($member->country)
                                <dt class="col-sm-4">Country</dt>
                                <dd class="col-sm-8">{{ $member->country }}</dd>
                                @endif
                            </dl>
                            @endif

                            @if($member->biography)
                            <h5 class="mt-3"><i class="fas fa-book"></i> Biography</h5>
                            <p>{{ $member->biography }}</p>
                            @endif

                            @if($member->notes)
                                                </a>
                                                <br><small class="text-muted">Generation {{ $member->mother->generation_number }}</small>
                                            @else
                                                <span class="text-muted">Not recorded</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @php
                                $spouses = $member->spouses();
                                $spouseCount = is_countable($spouses) ? $spouses->count() : $spouses->count();
                            @endphp
                            @if($spouseCount > 0)
                            <h5><i class="fas fa-heart text-danger"></i> {{ $member->gender == 'male' ? ($spouseCount > 1 ? 'Wives' : 'Wife') : 'Husband' }}</h5>
                            @foreach($member->spouses() as $index => $spouse)
                            <div class="card mb-3">
                                <div class="card-body">
                                    <h6>
                                        @if($member->gender == 'male' && $spouseCount > 1)
                                            <i class="fas fa-heart text-danger"></i> Wife {{ $index + 1 }}
                                        @endif
                                    </h6>
                                    <a href="{{ route('members.show', $spouse) }}">
                                        {{ $spouse->full_name }}
                                    </a>
                                    <br><small class="text-muted">{{ ucfirst($spouse->status) }}</small>
                                </div>
                            </div>
                            @endforeach
                            @endif

                            @if($siblings->count() > 0)
                            <h5><i class="fas fa-users"></i> Siblings</h5>
                            <ul class="list-group mb-3">
                                @foreach($siblings as $sibling)
                                    <li class="list-group-item">
                                        <a href="{{ route('members.show', $sibling) }}">{{ $sibling->full_name }}</a>
                                        <span class="badge badge-info float-right">Gen {{ $sibling->generation_number }}</span>
                                    </li>
                                @endforeach
                            </ul>
                            @endif

                            @if($member->children->count() > 0)
                            <h5><i class="fas fa-baby"></i> Children ({{ $member->children->count() }})</h5>
                            <ul class="list-group">
                                @foreach($member->children as $child)
                                    <li class="list-group-item">
                                        <a href="{{ route('members.show', $child) }}">{{ $child->full_name }}</a>
                                        <span class="badge badge-info float-right">Gen {{ $child->generation_number }}</span>
                                        <br><small class="text-muted">{{ $child->status }}</small>
                                    </li>
                                @endforeach
                            </ul>
                            @endif
                        </div>

                        {{-- Location Tab --}}
                        <div class="tab-pane" id="location">
                            @if($member->current_lat && $member->current_lng)
                                <h5><i class="fas fa-map-marked-alt text-primary"></i> Current Residence</h5>
                                
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
                                    This member's residential location has not been recorded yet. 
                                    @can('update', $member)
                                        You can <a href="{{ route('members.edit', $member) }}" class="alert-link">edit the profile</a> to add location information.
                                    @endcan
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

                        {{-- Timeline Tab --}}
                        <div class="tab-pane" id="timeline">
                            <div class="timeline">
                                @if($member->date_of_birth)
                                <div>
                                    <i class="fas fa-birthday-cake bg-success"></i>
                                    <div class="timeline-item">
                                        <span class="time"><i class="fas fa-clock"></i> {{ $member->date_of_birth->format('Y') }}</span>
                                        <h3 class="timeline-header">Born</h3>
                                        <div class="timeline-body">
                                            Born on {{ $member->date_of_birth->format('F d, Y') }}
                                            @if($member->place_of_birth)
                                                in {{ $member->place_of_birth }}
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @endif

                                @if($member->status == 'deceased' && $member->date_of_death)
                                <div>
                                    <i class="fas fa-dove bg-secondary"></i>
                                    <div class="timeline-item">
                                        <span class="time"><i class="fas fa-clock"></i> {{ $member->date_of_death->format('Y') }}</span>
                                        <h3 class="timeline-header">Passed Away</h3>
                                        <div class="timeline-body">
                                            Died on {{ $member->date_of_death->format('F d, Y') }}
                                            @if($member->place_of_death)
                                                in {{ $member->place_of_death }}
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @endif

                                <div>
                                    <i class="fas fa-clock bg-gray"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    {{-- Mobile Responsive Styles --}}
    <link rel="stylesheet" href="{{ asset('css/mobile-responsive.css') }}">
    
    {{-- Leaflet CSS --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" 
          integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" 
          crossorigin="anonymous"/>
    
    <style>
        /* Timeline Styles */
        .timeline {
            position: relative;
            padding-left: 50px;
        }
        .timeline::before {
            content: '';
            position: absolute;
            left: 18px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #dee2e6;
        }
        .timeline > div {
            position: relative;
            margin-bottom: 20px;
        }
        .timeline > div > i {
            position: absolute;
            left: -32px;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            text-align: center;
            line-height: 40px;
            color: white;
        }
        .timeline-item {
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 3px;
            padding: 10px;
        }
        .timeline-header {
            font-size: 16px;
            margin: 0;
        }
        
        /* Map Styles */
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
                                <i class="fas fa-info-circle"></i> Current Residence<br>
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
