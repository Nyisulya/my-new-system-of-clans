@extends('adminlte::page')

@section('title', 'Edit Member')

@section('content_header')
    <h1><i class="fas fa-user-edit"></i> Edit Member: {{ $member->full_name }}</h1>
@stop

@section('content')
    @if(session('warning'))
        <div class="alert alert-warning alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <strong>Warning!</strong> {{ session('warning') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <strong>Error!</strong> {{ session('error') }}
        </div>
    @endif

    <div class="card">
        <form action="{{ route('members.update', $member) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="card-body">
                {{-- Personal Information --}}
                <h5 class="text-primary"><i class="fas fa-user"></i> Personal Information</h5>
                <hr>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>First Name <span class="text-danger">*</span></label>
                            <input type="text" name="first_name" class="form-control @error('first_name') is-invalid @enderror" 
                                   value="{{ old('first_name', $member->first_name) }}" required>
                            @error('first_name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Middle Name</label>
                            <input type="text" name="middle_name" class="form-control @error('middle_name') is-invalid @enderror" 
                                   value="{{ old('middle_name', $member->middle_name) }}">
                            @error('middle_name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Last Name <span class="text-danger">*</span></label>
                            <input type="text" name="last_name" class="form-control @error('last_name') is-invalid @enderror" 
                                   value="{{ old('last_name', $member->last_name) }}" required>
                            @error('last_name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Maiden Name</label>
                            <input type="text" name="maiden_name" class="form-control @error('maiden_name') is-invalid @enderror" 
                                   value="{{ old('maiden_name', $member->maiden_name) }}">
                            @error('maiden_name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Gender <span class="text-danger">*</span></label>
                            <select name="gender" class="form-control @error('gender') is-invalid @enderror" required>
                                <option value="">Select Gender...</option>
                                <option value="male" {{ old('gender', $member->gender) == 'male' ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ old('gender', $member->gender) == 'female' ? 'selected' : '' }}>Female</option>
                                <option value="other" {{ old('gender', $member->gender) == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('gender')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Date of Birth <span class="text-danger">*</span></label>
                            <input type="date" name="date_of_birth" class="form-control @error('date_of_birth') is-invalid @enderror" 
                                   value="{{ old('date_of_birth', $member->date_of_birth?->format('Y-m-d')) }}" required>
                            @error('date_of_birth')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Place of Birth</label>
                            <input type="text" name="place_of_birth" class="form-control @error('place_of_birth') is-invalid @enderror" 
                                   value="{{ old('place_of_birth', $member->place_of_birth) }}" placeholder="City, Country">
                            @error('place_of_birth')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Status <span class="text-danger">*</span></label>
                            <select name="status" class="form-control @error('status') is-invalid @enderror" required>
                                <option value="alive" {{ old('status', $member->status) == 'alive' ? 'selected' : '' }}>Alive</option>
                                <option value="deceased" {{ old('status', $member->status) == 'deceased' ? 'selected' : '' }}>Deceased</option>
                            </select>
                            @error('status')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Date of Death</label>
                            <input type="date" name="date_of_death" class="form-control @error('date_of_death') is-invalid @enderror" 
                                   value="{{ old('date_of_death', $member->date_of_death?->format('Y-m-d')) }}">
                            @error('date_of_death')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Place of Death</label>
                            <input type="text" name="place_of_death" class="form-control @error('place_of_death') is-invalid @enderror" 
                                   value="{{ old('place_of_death', $member->place_of_death) }}">
                            @error('place_of_death')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Family Organization --}}
                <h5 class="text-primary mt-4"><i class="fas fa-sitemap"></i> Family Organization</h5>
                <hr>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Clan <span class="text-danger">*</span></label>
                            <select name="clan_id" class="form-control @error('clan_id') is-invalid @enderror" required>
                                <option value="">Select Clan...</option>
                                @foreach($clans as $clan)
                                    <option value="{{ $clan->id }}" {{ old('clan_id', $member->clan_id) == $clan->id ? 'selected' : '' }}>
                                        {{ $clan->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('clan_id')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Family <span class="text-danger">*</span></label>
                            <select name="family_id" class="form-control @error('family_id') is-invalid @enderror" required>
                                <option value="">Select Family...</option>
                                @foreach($clans as $clan)
                                    @foreach($clan->families as $family)
                                        <option value="{{ $family->id }}" {{ old('family_id', $member->family_id) == $family->id ? 'selected' : '' }}>
                                            {{ $family->name }} ({{ $clan->name }})
                                        </option>
                                    @endforeach
                                @endforeach
                            </select>
                            @error('family_id')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Branch <small class="text-muted">(Optional)</small></label>
                            <select name="branch_id" class="form-control @error('branch_id') is-invalid @enderror">
                                <option value="">None</option>
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}" {{ old('branch_id', $member->branch_id) == $branch->id ? 'selected' : '' }}>
                                        {{ $branch->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('branch_id')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Family Relationships --}}
                <h5 class="text-primary mt-4"><i class="fas fa-users"></i> Family Relationships</h5>
                <hr>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Father</label>
                            <select name="father_id" class="form-control @error('father_id') is-invalid @enderror">
                                <option value="">None</option>
                                @foreach($potentialFathers as $father)
                                    <option value="{{ $father->id }}" {{ old('father_id', $member->father_id) == $father->id ? 'selected' : '' }}>
                                        {{ $father->full_name }} (Gen {{ $father->generation_number }})
                                    </option>
                                @endforeach
                            </select>
                            @error('father_id')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Mother</label>
                            <select name="mother_id" class="form-control @error('mother_id') is-invalid @enderror">
                                <option value="">None</option>
                                @foreach($potentialMothers as $mother)
                                    <option value="{{ $mother->id }}" {{ old('mother_id', $member->mother_id) == $mother->id ? 'selected' : '' }}>
                                        {{ $mother->full_name }} (Gen {{ $mother->generation_number }})
                                    </option>
                                @endforeach
                            </select>
                            @error('mother_id')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Current Spouses</label>
                            <div class="border rounded p-2" style="min-height: 38px;">
                                @php
                                    $currentSpouses = $member->spouses();
                                    $spouseCount = is_countable($currentSpouses) ? $currentSpouses->count() : 0;
                                @endphp
                                @if($spouseCount > 0)
                                    @foreach($currentSpouses as $spouse)
                                        <span class="badge badge-info mr-1">{{ $spouse->full_name }}</span>
                                    @endforeach
                                @else
                                    <span class="text-muted">No spouse</span>
                                @endif
                            </div>
                            <small class="form-text text-muted">
                                Manage marriages from the member's detail page
                            </small>
                        </div>
                    </div>
                </div>

                {{-- Contact Information --}}
                <h5 class="text-primary mt-4"><i class="fas fa-address-card"></i> Contact Information</h5>
                <hr>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                                   value="{{ old('email', $member->email) }}">
                            @error('email')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Phone</label>
                            <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" 
                                   value="{{ old('phone', $member->phone) }}">
                            @error('phone')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Profile Photo</label>
                            <div class="custom-file">
                                <input type="file" name="profile_photo" class="custom-file-input @error('profile_photo') is-invalid @enderror" id="profile_photo" accept="image/*">
                                <label class="custom-file-label" for="profile_photo">Choose file</label>
                            </div>
                            <small class="form-text text-muted">Leave empty to keep current photo. Max 2MB.</small>
                            @if($member->profile_photo)
                                <div class="mt-2">
                                    <img src="{{ $member->profile_photo_url }}" alt="Current Photo" class="img-thumbnail" style="height: 100px">
                                </div>
                            @endif
                            @error('profile_photo')
                                <span class="invalid-feedback d-block">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Cascading Location Picker --}}
                <div class="row">
                    <div class="col-md-12">
                        <div class="card bg-light border-0">
                            <div class="card-body p-3">
                                <h6 class="text-primary mb-3"><i class="fas fa-map-marked-alt mr-1"></i> Location Details</h6>
                                
                                {{-- 1. Country --}}
                                <div class="form-group">
                                    <label class="small text-muted mb-0">1. Country</label>
                                    <select id="countrySelect" class="form-control select2" style="width: 100%;">
                                        <option value="">Select Country...</option>
                                        {{-- Populated via JS --}}
                                    </select>
                                </div>

                                {{-- 2. Region --}}
                                <div class="form-group">
                                    <label class="small text-muted mb-0">2. Region / State</label>
                                    <select id="regionSelect" class="form-control select2" style="width: 100%;" disabled>
                                        <option value="">Select Country First...</option>
                                    </select>
                                </div>

                                {{-- 3. District --}}
                                <div class="form-group">
                                    <label class="small text-muted mb-0">3. District / County</label>
                                    <select id="districtSelect" class="form-control select2" style="width: 100%;" disabled>
                                        <option value="">Select Region First...</option>
                                    </select>
                                </div>

                                {{-- 4. Street --}}
                                <div class="form-group">
                                    <label class="small text-muted mb-0">4. Street / Village / Area</label>
                                    <select id="streetSelect" class="form-control select2" style="width: 100%;" disabled>
                                        <option value="">Select District First...</option>
                                    </select>
                                </div>

                                <button type="button" id="resetLocation" class="btn btn-xs btn-outline-danger mt-2">
                                    <i class="fas fa-undo"></i> Reset Location
                                </button>
                            </div>
                        </div>

                        {{-- Hidden Fields for Location Data --}}
                        <div id="locationDetails" class="{{ $member->current_location ? '' : 'd-none' }} bg-white border p-3 rounded mt-2">
                            <h6 class="text-success"><i class="fas fa-check-circle"></i> Location Locked</h6>
                            <p class="mb-1 small text-muted" id="displayAddress">{{ $member->current_location }}</p>
                            <input type="hidden" name="address" id="address" value="{{ old('address', $member->address) }}">
                            <input type="hidden" name="street" id="street" value="{{ old('street', $member->street) }}">
                            <input type="hidden" name="city" id="city" value="{{ old('city', $member->city) }}">
                            <input type="hidden" name="district" id="district" value="{{ old('district', $member->district) }}">
                            <input type="hidden" name="region" id="region" value="{{ old('region', $member->region) }}">
                            <input type="hidden" name="country" id="country" value="{{ old('country', $member->country) }}">
                            <input type="hidden" name="current_lat" id="current_lat" value="{{ old('current_lat', $member->current_lat) }}">
                            <input type="hidden" name="current_lng" id="current_lng" value="{{ old('current_lng', $member->current_lng) }}">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Biography</label>
                            <textarea name="biography" class="form-control @error('biography') is-invalid @enderror" 
                                      rows="3">{{ old('biography', $member->biography) }}</textarea>
                            @error('biography')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Notes</label>
                            <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" 
                                      rows="2">{{ old('notes', $member->notes) }}</textarea>
                            @error('notes')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Member
                </button>
                <a href="{{ route('members.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
            </div>
        </form>
    </div>
@stop

@section('css')
    <style>
        .form-group label {
            font-weight: 600;
        }
        .text-danger {
            font-weight: 700;
        }
    </style>
@stop

@section('js')
    <script src="{{ asset('js/location-data.js') }}"></script>
    <script>
        $(document).ready(function() {
            // Custom file input label update
            $(".custom-file-input").on("change", function() {
                var fileName = $(this).val().split("\\").pop();
                $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
            });

            // --- 1. Initialize Select2 ---
            $('.select2').select2({ theme: 'bootstrap' });

            // --- 2. Country List (Comprehensive) ---
            const countries = [
                "Afghanistan", "Albania", "Algeria", "Andorra", "Angola", "Antigua and Barbuda", "Argentina", "Armenia", "Australia", "Austria", "Azerbaijan",
                "Bahamas", "Bahrain", "Bangladesh", "Barbados", "Belarus", "Belgium", "Belize", "Benin", "Bhutan", "Bolivia", "Bosnia and Herzegovina", "Botswana", "Brazil", "Brunei", "Bulgaria", "Burkina Faso", "Burundi",
                "Cabo Verde", "Cambodia", "Cameroon", "Canada", "Central African Republic", "Chad", "Chile", "China", "Colombia", "Comoros", "Congo, Democratic Republic of the", "Congo, Republic of the", "Costa Rica", "Croatia", "Cuba", "Cyprus", "Czech Republic",
                "Denmark", "Djibouti", "Dominica", "Dominican Republic",
                "East Timor", "Ecuador", "Egypt", "El Salvador", "Equatorial Guinea", "Eritrea", "Estonia", "Eswatini", "Ethiopia",
                "Fiji", "Finland", "France",
                "Gabon", "Gambia", "Georgia", "Germany", "Ghana", "Greece", "Grenada", "Guatemala", "Guinea", "Guinea-Bissau", "Guyana",
                "Haiti", "Honduras", "Hungary",
                "Iceland", "India", "Indonesia", "Iran", "Iraq", "Ireland", "Israel", "Italy", "Ivory Coast",
                "Jamaica", "Japan", "Jordan",
                "Kazakhstan", "Kenya", "Kiribati", "Korea, North", "Korea, South", "Kosovo", "Kuwait", "Kyrgyzstan",
                "Laos", "Latvia", "Lebanon", "Lesotho", "Liberia", "Libya", "Liechtenstein", "Lithuania", "Luxembourg",
                "Madagascar", "Malawi", "Malaysia", "Maldives", "Mali", "Malta", "Marshall Islands", "Mauritania", "Mauritius", "Mexico", "Micronesia", "Moldova", "Monaco", "Mongolia", "Montenegro", "Morocco", "Mozambique", "Myanmar",
                "Namibia", "Nauru", "Nepal", "Netherlands", "New Zealand", "Nicaragua", "Niger", "Nigeria", "North Macedonia", "Norway",
                "Oman",
                "Pakistan", "Palau", "Palestine", "Panama", "Papua New Guinea", "Paraguay", "Peru", "Philippines", "Poland", "Portugal",
                "Qatar",
                "Romania", "Russia", "Rwanda",
                "Saint Kitts and Nevis", "Saint Lucia", "Saint Vincent and the Grenadines", "Samoa", "San Marino", "Sao Tome and Principe", "Saudi Arabia", "Senegal", "Serbia", "Seychelles", "Sierra Leone", "Singapore", "Slovakia", "Slovenia", "Solomon Islands", "Somalia", "South Africa", "South Sudan", "Spain", "Sri Lanka", "Sudan", "Suriname", "Sweden", "Switzerland", "Syria",
                "Taiwan", "Tajikistan", "Tanzania", "Thailand", "Togo", "Tonga", "Trinidad and Tobago", "Tunisia", "Turkey", "Turkmenistan", "Tuvalu",
                "Uganda", "Ukraine", "United Arab Emirates", "United Kingdom", "United States", "Uruguay", "Uzbekistan",
                "Vanuatu", "Vatican City", "Venezuela", "Vietnam",
                "Yemen",
                "Zambia", "Zimbabwe"
            ];
            // Add countries to select
            countries.sort().forEach(country => {
                const isSelected = "{{ $member->country }}" === country;
                $('#countrySelect').append(new Option(country, country, isSelected, isSelected));
            });

            // --- 3. Cascading Logic ---
            
            // Helper to reset child selects
            function resetSelect(selector, placeholder) {
                $(selector).empty().append(new Option(placeholder, "")).prop('disabled', true).trigger('change');
            }

            // Helper to configure Nominatim AJAX
            function configureNominatim(selector, placeholder, queryBuilder) {
                // Destroy existing Select2
                if ($(selector).data('select2')) {
                    $(selector).select2('destroy');
                }

                $(selector).select2({
                    theme: 'bootstrap',
                    placeholder: placeholder + ' (Type to search...)',
                    minimumInputLength: 2,
                    language: {
                        inputTooShort: function () {
                            return '🔍 Type at least 2 characters to search';
                        },
                        searching: function () {
                            return '🔍 Searching...';
                        },
                        noResults: function () {
                            return 'No results found. Try different keywords.';
                        }
                    },
                    ajax: {
                        url: 'https://nominatim.openstreetmap.org/search',
                        dataType: 'json',
                        delay: 500,
                        headers: { 'User-Agent': 'FamilyTree/1.0' },
                        data: function (params) {
                            return {
                                q: queryBuilder(params.term),
                                format: 'json',
                                addressdetails: 1,
                                limit: 10
                            };
                        },
                        processResults: function (data) {
                            return {
                                results: $.map(data, function (item) {
                                    return {
                                        text: item.display_name,
                                        id: item.place_id,
                                        data: item
                                    }
                                })
                            };
                        },
                        cache: true
                    }
                });
                
                // Add a visual indicator that this is a search field
                const $container = $(selector).parent();
                if (!$container.find('.search-help-text').length) {
                    $container.append('<small class="form-text text-muted search-help-text"><i class="fas fa-info-circle"></i> Type to search for locations</small>');
                }
            }

            // Helper to configure Static Select
            function configureStatic(selector, placeholder, items, selectedValue = null) {
                // Destroy existing Select2
                if ($(selector).data('select2')) {
                    $(selector).select2('destroy');
                }

                $(selector).empty().append(new Option(placeholder, ""));
                items.sort().forEach(item => {
                    const isSelected = selectedValue === item;
                    $(selector).append(new Option(item, item, isSelected, isSelected));
                });

                $(selector).select2({
                    theme: 'bootstrap',
                    placeholder: placeholder
                });
                
                // Remove search help text if it exists (switching from search to static)
                $(selector).parent().find('.search-help-text').remove();
            }

            // A. Country Change
            $('#countrySelect').change(function() {
                const country = $(this).val();
                
                // Only reset if we are changing country manually (not initial load)
                // But for simplicity, we might need to be careful about initial load
                // The initial load logic is handled separately below
            });

            // We need to handle the change event carefully to not wipe out data on load if we trigger it
            // So we separate the logic
            function handleCountryChange(country, preserveChild = false) {
                if (!preserveChild) {
                    resetSelect('#regionSelect', 'Select Region...');
                    resetSelect('#districtSelect', 'Select District First...');
                    resetSelect('#streetSelect', 'Select District First...');
                }

                if (country) {
                    $('#regionSelect').prop('disabled', false);
                    
                    const currentRegion = "{{ $member->region }}";
                    const useRegion = preserveChild ? currentRegion : null;

                    if (locationData[country]) {
                        // Use Static List (Keys of the object)
                        const regions = Object.keys(locationData[country]);
                        configureStatic('#regionSelect', 'Select Region...', regions, useRegion);
                        updateLocationData({ country: country });
                    } else {
                        // Use Nominatim Search
                        configureNominatim('#regionSelect', 'Search Region...', function(term) {
                            return term + ", " + country;
                        });
                        // If we have a region and it's not in static list (because we are in search mode), 
                        // we might want to pre-fill it if it's an edit. 
                        if (useRegion) {
                             var option = new Option(useRegion, useRegion, true, true);
                             $('#regionSelect').append(option).trigger('change');
                        }
                        updateLocationData({ country: country });
                    }
                } else {
                    resetSelect('#regionSelect', 'Select Country First...');
                    resetSelect('#districtSelect', 'Select Region First...');
                    resetSelect('#streetSelect', 'Select District First...');
                }
            }

            $('#countrySelect').change(function() {
                handleCountryChange($(this).val());
            });

            // Trigger change if country is pre-selected (Initial Load)
            if ($('#countrySelect').val()) {
                handleCountryChange($('#countrySelect').val(), true);
            }

            // B. Region Change
            $('#regionSelect').on('change select2:select', function(e) {
                let region = "";
                let data = null;

                if (e.type === 'select2:select') {
                    data = e.params.data.data;
                    region = data.address.state || data.address.region || data.display_name.split(',')[0];
                } else {
                    region = $(this).val();
                }

                if (!region) return;
                
                resetSelect('#districtSelect', 'Select District...');
                resetSelect('#streetSelect', 'Select District First...');
                
                $('#districtSelect').prop('disabled', false);
                
                const country = $('#countrySelect').val();
                
                // Check if we have static districts for this region
                if (locationData[country] && locationData[country][region]) {
                    // Use Static District List
                    // Check if we should preserve the value (only if it matches the static list)
                    const districts = Object.keys(locationData[country][region]);
                    
                    const currentDistrict = "{{ $member->district }}";
                    const isInitialLoad = (currentDistrict && districts.includes(currentDistrict) && $('#districtSelect').val() === null);
                    
                    const memberRegion = "{{ $member->region }}";
                    const useDistrict = (region === memberRegion) ? currentDistrict : null;
                    
                    configureStatic('#districtSelect', 'Select District...', districts, useDistrict);
                } else {
                    // Use Nominatim Search
                    configureNominatim('#districtSelect', 'Search District...', function(term) {
                        return term + ", " + region + ", " + country;
                    });
                    
                    const currentDistrict = "{{ $member->district }}";
                    const memberRegion = "{{ $member->region }}";
                    if (region === memberRegion && currentDistrict) {
                         var option = new Option(currentDistrict, currentDistrict, true, true);
                         $('#districtSelect').append(option).trigger('change');
                    }
                }
                
                if (data) {
                    updateLocationData(data.address, data.lat, data.lon);
                } else {
                    updateLocationData({ region: region });
                }
            });
            
            // Trigger region change if it has a value (Initial Load)
            // We need to do this manually because configureStatic doesn't trigger change
            if ($('#regionSelect').val()) {
                $('#regionSelect').trigger('change');
            }

            // C. District Change
            $('#districtSelect').on('change select2:select', function(e) {
                let district = "";
                let data = null;

                if (e.type === 'select2:select') {
                    data = e.params.data.data;
                    district = data.address.county || data.address.city || data.display_name.split(',')[0];
                } else {
                    district = $(this).val();
                }
                
                if (!district) return;

                resetSelect('#streetSelect', 'Search Street/Village...');
                
                $('#streetSelect').prop('disabled', false);
                
                const country = $('#countrySelect').val();
                const region = $('#regionSelect').val();
                
                // Check if we have static wards for this district
                if (locationData[country] && locationData[country][region] && locationData[country][region][district]) {
                    // Use Static Ward List
                    const wards = locationData[country][region][district];
                    
                    const currentStreet = "{{ $member->street }}";
                    const memberDistrict = "{{ $member->district }}";
                    const useStreet = (district === memberDistrict) ? currentStreet : null;
                    
                    configureStatic('#streetSelect', 'Select Street/Village...', wards, useStreet);
                } else {
                    // Use Nominatim Search
                    configureNominatim('#streetSelect', 'Search Street/Village...', function(term) {
                        let regionText = "";
                        if ($('#regionSelect').data('select2') && $('#regionSelect').select2('data')[0]) {
                             regionText = $('#regionSelect').select2('data')[0].text.split(',')[0];
                        } else {
                            regionText = $('#regionSelect').val();
                        }
                        return term + ", " + district + ", " + regionText + ", " + $('#countrySelect').val();
                    });
                    
                    // Preserve Street if applicable
                    const currentStreet = "{{ $member->street }}";
                    const memberDistrict = "{{ $member->district }}";
                    
                    if (district === memberDistrict && currentStreet) {
                         var option = new Option(currentStreet, currentStreet, true, true);
                         $('#streetSelect').append(option).trigger('change');
                    }
                }
                
                if (data) {
                    updateLocationData(data.address, data.lat, data.lon);
                } else {
                    updateLocationData({ district: district });
                }
            });

            // D. Street Change
            $('#streetSelect').on('change select2:select', function(e) {
                let street = "";
                let data = null;

                if (e.type === 'select2:select') {
                    data = e.params.data.data;
                    street = data.address.road || data.address.pedestrian || data.display_name.split(',')[0];
                } else {
                    street = $(this).val();
                }

                if (data) {
                    updateLocationData(data.address, data.lat, data.lon);
                } else {
                    updateLocationData({ road: street });
                }
            });

            // --- 4. Update Hidden Fields ---
            function updateLocationData(addr, lat, lng) {
                if (addr.country) $('#country').val(addr.country);
                if (addr.state || addr.region) $('#region').val(addr.state || addr.region);
                if (addr.county || addr.district) $('#district').val(addr.county || addr.district);
                if (addr.city || addr.town || addr.village) $('#city').val(addr.city || addr.town || addr.village);
                if (addr.road || addr.pedestrian) $('#street').val(addr.road || addr.pedestrian);
                if (addr.house_number) $('#address').val(addr.house_number);

                // If only region is passed (static selection), ensure we update it
                if (addr.region && !addr.state) $('#region').val(addr.region);
                // If only district is passed (static selection)
                if (addr.district && !addr.county) $('#district').val(addr.district);

                if (lat && lng) {
                    $('#current_lat').val(lat);
                    $('#current_lng').val(lng);
                }

                // Update Display
                const parts = [
                    $('#street').val(), 
                    $('#city').val(), 
                    $('#district').val(), 
                    $('#region').val(), 
                    $('#country').val()
                ].filter(Boolean);
                
                $('#displayAddress').text(parts.join(', '));
                $('#locationDetails').removeClass('d-none');
            }

            // Reset Button
            $('#resetLocation').click(function() {
                $('#countrySelect').val('').trigger('change');
                $('#locationDetails').addClass('d-none');
                $('#address, #street, #city, #district, #region, #country, #current_lat, #current_lng').val('');
            });
        });
    </script>
@stop
