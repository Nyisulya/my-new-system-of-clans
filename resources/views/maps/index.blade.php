@extends('adminlte::page')

@section('title', 'Ancestral Map')

@section('content_header')
    <h1><i class="fas fa-globe-africa"></i> Ancestral Map</h1>
@stop

@section('content')
    <div class="card card-primary card-outline">
        <div class="card-header">
            <h3 class="card-title">Family Migration Map</h3>
            <div class="card-tools d-flex align-items-center">
                <!-- Location Type Filters -->
                <div class="btn-group btn-group-sm mr-3" role="group">
                    <button type="button" class="btn btn-outline-success active" data-filter="birth" id="filterBirth">
                        <i class="fas fa-baby"></i> Birth
                    </button>
                    <button type="button" class="btn btn-outline-primary active" data-filter="living" id="filterLiving">
                        <i class="fas fa-home"></i> Living
                    </button>
                    <button type="button" class="btn btn-outline-danger active" data-filter="death" id="filterDeath">
                        <i class="fas fa-cross"></i> Death
                    </button>
                </div>
                
                <!-- Member Search -->
                <div class="mr-3" style="width: 250px;">
                    <select id="member-search" class="form-control select2">
                        <option></option>
                        @foreach($members as $item)
                            <option value="{{ $item['member']->id }}">{{ $item['member']->full_name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <button type="button" class="btn btn-tool" data-card-widget="maximize">
                    <i class="fas fa-expand"></i>
                </button>
            </div>
        </div>
        <div class="card-body p-0">
            <div id="map" style="height: 600px; width: 100%;"></div>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .custom-popup .leaflet-popup-content-wrapper {
            border-radius: 5px;
        }
        .select2-container .select2-selection--single {
            height: 38px;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 38px;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px;
        }
    </style>
@stop

@section('js')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Base Layers
            var streetLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                maxZoom: 19
            });

            // High-resolution satellite imagery
            var satelliteLayer = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
                attribution: 'Tiles &copy; Esri &mdash; Source: Esri, i-cubed, USDA, USGS, AEX, GeoEye, Getmapping, Aerogrid, IGN, IGP, UPR-EGP, and the GIS User Community',
                maxZoom: 19
            });

            // Satellite with labels (Hybrid view) - clearer view showing streets and place names
            var hybridLayer = L.layerGroup([
                L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
                    maxZoom: 19
                }),
                L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/Reference/World_Boundaries_and_Places/MapServer/tile/{z}/{y}/{x}', {
                    maxZoom: 19
                })
            ]);

            // Initialize map with default layer
            var map = L.map('map', {
                center: [0, 20],
                zoom: 3,
                layers: [streetLayer]
            });

            // Layer Control with clearer satellite option
            var baseMaps = {
                "Street View": streetLayer,
                "Satellite": satelliteLayer,
                "Satellite + Labels": hybridLayer
            };
            L.control.layers(baseMaps).addTo(map);

            var members = @json($members);
            var markers = L.layerGroup().addTo(map);
            var memberMarkers = {}; // Store markers by member ID

            members.forEach(function(item) {
                item.locations.forEach(function(loc) {
                    var iconColor = loc.type === 'birth' ? 'green' : (loc.type === 'death' ? 'red' : 'blue');
                    
                    // Simple custom marker
                    var marker = L.marker([loc.lat, loc.lng]).addTo(markers);
                    
                    // Add type to marker for filtering
                    marker.locationType = loc.type;
                    marker.memberStatus = item.member.status;
                    
                    var photoHtml = item.member.profile_photo_url 
                        ? `<img src="${item.member.profile_photo_url}" class="img-circle mb-2" style="width: 50px; height: 50px; object-fit: cover;">`
                        : '';

                    var popupContent = `
                        <div class="text-center">
                            ${photoHtml}<br>
                            <strong>${loc.title}</strong><br>
                            ${loc.name}<br>
                            <a href="/members/${item.member.id}/dashboard" class="btn btn-xs btn-primary mt-2">View Profile</a>
                        </div>
                    `;

                    marker.bindPopup(popupContent);
                    
                    // Store marker reference
                    if (!memberMarkers[item.member.id]) {
                        memberMarkers[item.member.id] = [];
                    }
                    memberMarkers[item.member.id].push(marker);
                });
            });

            // Fit bounds if there are markers
            if (members.length > 0) {
                var group = new L.featureGroup(markers.getLayers());
                map.fitBounds(group.getBounds().pad(0.1));
            }

            // Filter functionality
            var activeFilters = {
                birth: true,
                living: true,
                death: true
            };

            function updateMarkerVisibility() {
                markers.eachLayer(function(marker) {
                    var shouldShow = false;
                    
                    if (marker.locationType === 'birth' && activeFilters.birth) {
                        shouldShow = true;
                    } else if (marker.locationType === 'current') {
                        // Show current location if member is alive and living filter is on
                        // OR if member is deceased and death filter is on
                        if (marker.memberStatus === 'alive' && activeFilters.living) {
                            shouldShow = true;
                        } else if (marker.memberStatus === 'deceased' && activeFilters.death) {
                            shouldShow = true;
                        }
                    }
                    
                    if (shouldShow) {
                        marker.setOpacity(1);
                        if (!map.hasLayer(marker)) {
                            map.addLayer(marker);
                        }
                    } else {
                        marker.setOpacity(0);
                        if (map.hasLayer(marker)) {
                            map.removeLayer(marker);
                        }
                    }
                });
            }

            // Filter button click handlers
            $('.btn-group[role="group"] button').on('click', function() {
                $(this).toggleClass('active');
                var filterType = $(this).data('filter');
                activeFilters[filterType] = $(this).hasClass('active');
                updateMarkerVisibility();
            });

            // Initialize Select2
            $('.select2').select2({
                placeholder: "Search for a member...",
                allowClear: true
            });

            // Handle Search Selection
            $('#member-search').on('select2:select', function (e) {
                var memberId = e.params.data.id;
                
                if (memberMarkers[memberId] && memberMarkers[memberId].length > 0) {
                    var marker = memberMarkers[memberId][0]; // Fly to the first marker (usually birth or current)
                    map.flyTo(marker.getLatLng(), 10, {
                        animate: true,
                        duration: 1.5
                    });
                    marker.openPopup();
                }
            });
        });
    </script>
@stop
