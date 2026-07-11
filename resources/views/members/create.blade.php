@extends('layouts.app')

@section('title', 'Ongeza Mwanachama')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-user-plus text-primary"></i> Ongeza Mwanachama Mpya</h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashibodi</a></li>
            <li class="breadcrumb-item"><a href="{{ route('members.index') }}">Wanafamilia</a></li>
            <li class="breadcrumb-item active">Ongeza Mpya</li>
        </ol>
    </div>
@stop

@section('content')
    <form action="{{ route('members.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        {{-- Hidden field to preserve generation_number from query parameter --}}
        <input type="hidden" name="generation_number" value="{{ request('generation_number', old('generation_number')) }}">
        
        @if(session('warning'))
            <input type="hidden" name="confirm_duplicate" value="1">
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <div class="d-flex align-items-center">
                    <i class="fas fa-exclamation-triangle fa-2x mr-3"></i>
                    <div>
                        <h5 class="alert-heading">Uwezekano wa Mwanachama Kufanana Umepatikana!</h5>
                        <p class="mb-0">{{ session('warning') }}</p>
                    </div>
                </div>
                <button type="button" class="close" data-dismiss="alert" aria-label="Funga">
                    <span aria-hidden="true">&times;</span>
                </button>
                
                @if(session('partial_matches'))
                    <hr>
                    <p class="mb-2"><strong>Rekodi zinazofanana (Je, huyu ni wewe?):</strong></p>
                    <div class="list-group">
                        @foreach(session('partial_matches') as $match)
                            <div class="list-group-item list-group-item-warning d-flex flex-column flex-md-row justify-content-between align-items-md-center py-2 px-3 mb-2 shadow-sm rounded border-0" style="background-color: rgba(255, 255, 255, 0.4); border: 1px solid rgba(0,0,0,0.08);">
                                <div class="mb-2 mb-md-0">
                                    <span class="font-weight-bold text-dark d-block">
                                        <i class="fas fa-user-circle mr-1 text-secondary"></i> {{ $match->full_name }}
                                    </span>
                                    <small class="text-muted">
                                        <i class="fas fa-birthday-cake mr-1"></i> Kuzaliwa: {{ $match->date_of_birth ? $match->date_of_birth->format('M d, Y') : 'Haijulikani' }}
                                    </small>
                                </div>
                                @if(auth()->user()->member_id === null && !auth()->user()->isAdmin())
                                    <form action="{{ route('profile.claim.submit') }}" method="POST" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="member_id" value="{{ $match->id }}">
                                        <button type="submit" class="btn btn-sm btn-dark font-weight-bold link-direct-btn px-3 py-1 shadow-sm" 
                                                data-name="{{ $match->full_name }}" 
                                                data-dob="{{ $match->date_of_birth ? $match->date_of_birth->format('d M Y') : 'Hajajaza' }}">
                                            <i class="fas fa-link mr-1"></i> Unganisha na Akaunti Yangu
                                        </button>
                                    </form>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <strong><i class="fas fa-times-circle"></i> Itilafu!</strong> {{ session('error') }}
                @if(session('existing_member_id') && auth()->user()->member_id === null && !auth()->user()->isAdmin())
                    <hr>
                    <p class="mb-2"><strong>Je, huyu ni wasifu wako?</strong> Bonyeza kitufe hapa chini ili kujiunganisha nao moja kwa moja:</p>
                    <form action="{{ route('profile.claim.submit') }}" method="POST" class="d-inline">
                        @csrf
                        <input type="hidden" name="member_id" value="{{ session('existing_member_id') }}">
                        <button type="submit" class="btn btn-sm btn-light font-weight-bold text-danger link-direct-btn"
                                data-name="{{ old('first_name') }} {{ old('last_name') }}"
                                data-dob="{{ old('date_of_birth') ? \Carbon\Carbon::parse(old('date_of_birth'))->format('d M Y') : 'Hajajaza' }}">
                            <i class="fas fa-link mr-1"></i> Unganisha na Akaunti Yangu
                        </button>
                    </form>
                @endif
            </div>
        @endif

        <div class="row">
            {{-- Left Column: Main Information --}}
            <div class="col-lg-8">
                {{-- Personal Details Card --}}
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-user mr-1"></i> Maelezo Binafsi</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Jina la Kwanza <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-signature"></i></span>
                                        </div>
                                        <input type="text" name="first_name" class="form-control @error('first_name') is-invalid @enderror" 
                                               value="{{ old('first_name') }}" placeholder="Jina la Kwanza" required>
                                    </div>
                                    @error('first_name') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Jina la Kati <small class="text-muted">(Hiari)</small></label>
                                    <input type="text" name="middle_name" class="form-control @error('middle_name') is-invalid @enderror" 
                                           value="{{ old('middle_name') }}" placeholder="Jina la Kati">
                                    @error('middle_name') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Jina la Mwisho / Ukoo <small class="text-muted">(Hiari)</small></label>
                                    <input type="text" name="last_name" class="form-control @error('last_name') is-invalid @enderror" 
                                           value="{{ old('last_name') }}" placeholder="Jina la Mwisho">
                                    @error('last_name') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Jinsia <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-venus-mars"></i></span>
                                        </div>
                                        <select name="gender" class="form-control @error('gender') is-invalid @enderror" required>
                                            <option value="">Chagua...</option>
                                            <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Mwanaume</option>
                                            <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Mwanamke</option>
                                            <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Nyingine</option>
                                        </select>
                                    </div>
                                    @error('gender') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Tarehe ya Kuzaliwa <small class="text-muted">(Hiari)</small></label>
                                    <input type="hidden" name="date_of_birth" id="dob_hidden" value="{{ old('date_of_birth') }}">
                                    <div class="row">
                                        <div class="col-4 pr-1">
                                            <select id="dob_day" class="form-control @error('date_of_birth') is-invalid @enderror">
                                                <option value="">Siku</option>
                                                @for ($d = 1; $d <= 31; $d++)
                                                    <option value="{{ sprintf('%02d', $d) }}">{{ sprintf('%02d', $d) }}</option>
                                                @endfor
                                            </select>
                                        </div>
                                        <div class="col-4 px-1">
                                            <select id="dob_month" class="form-control @error('date_of_birth') is-invalid @enderror">
                                                <option value="">Mwezi</option>
                                                @php
                                                    $monthsSw = [
                                                        '01' => 'Jan', '02' => 'Feb', '03' => 'Mar', '04' => 'Apr',
                                                        '05' => 'Mei', '06' => 'Jun', '07' => 'Jul', '08' => 'Ago',
                                                        '09' => 'Sep', '10' => 'Okt', '11' => 'Nov', '12' => 'Des'
                                                    ];
                                                @endphp
                                                @foreach($monthsSw as $val => $label)
                                                    <option value="{{ $val }}">{{ $label }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-4 pl-1">
                                            <select id="dob_year" class="form-control @error('date_of_birth') is-invalid @enderror">
                                                <option value="">Mwaka</option>
                                                @for ($y = date('Y'); $y >= 1900; $y--)
                                                    <option value="{{ $y }}">{{ $y }}</option>
                                                @endfor
                                            </select>
                                        </div>
                                    </div>
                                    @error('date_of_birth') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Hali <span class="text-danger">*</span></label>
                                    <select name="status" id="statusSelect" class="form-control @error('status') is-invalid @enderror" required>
                                        <option value="alive" {{ old('status', 'alive') == 'alive' ? 'selected' : '' }}>Hai</option>
                                        <option value="deceased" {{ old('status') == 'deceased' ? 'selected' : '' }}>Marehemu</option>
                                    </select>
                                    @error('status') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Mahali pa Kuzaliwa <small class="text-muted">(Hiari)</small></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                                        </div>
                                        <input type="text" name="place_of_birth" class="form-control @error('place_of_birth') is-invalid @enderror" 
                                               value="{{ old('place_of_birth') }}" placeholder="Mji, Nchi">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Jina la Msichana <small class="text-muted">(Hiari)</small></label>
                                    <input type="text" name="maiden_name" class="form-control @error('maiden_name') is-invalid @enderror" 
                                           value="{{ old('maiden_name') }}" placeholder="Jina la Msichana">
                                </div>
                            </div>
                        </div>

                        {{-- Deceased Fields (Hidden by default) --}}
                        <div id="deceasedFields" style="display: none;">
                            <hr>
                            <h6 class="text-secondary mb-3">Maelezo ya Kifo</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Tarehe ya Kifo</label>
                                        <input type="date" name="date_of_death" class="form-control @error('date_of_death') is-invalid @enderror" 
                                               value="{{ old('date_of_death') }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Mahali pa Kifo</label>
                                        <input type="text" name="place_of_death" class="form-control @error('place_of_death') is-invalid @enderror" 
                                               value="{{ old('place_of_death') }}" placeholder="Mji, Nchi">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Family Organization Card --}}
                <div class="card card-info card-outline">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-sitemap mr-1"></i> Muundo wa Familia</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Ukoo @if(!isset($selectedSpouse))<span class="text-danger">*</span>@else<small class="text-muted">(Hiari)</small>@endif</label>
                                    @if(isset($selectedSpouse))
                                        <input type="text" name="clan_name" class="form-control @error('clan_name') is-invalid @enderror" value="{{ old('clan_name') }}" placeholder="Ukoo wa Mwenzi (Hiari)">
                                        <small class="text-muted">Ingiza jina la ukoo wa mwenzi (Hiari)</small>
                                        @error('clan_name') <span class="text-danger small">{{ $message }}</span> @enderror
                                    @else
                                        <select name="clan_id" class="form-control select2 @error('clan_id') is-invalid @enderror" required>
                                            <option value="">Chagua Ukoo...</option>
                                            @foreach($clans as $clan)
                                                <option value="{{ $clan->id }}" {{ old('clan_id', $selectedClanId ?? '') == $clan->id ? 'selected' : '' }}>
                                                    {{ $clan->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('clan_id') <span class="text-danger small">{{ $message }}</span> @enderror
                                    @endif
                                </div>
                            </div>




                        </div>

                        <hr>
                        <h6 class="text-secondary mb-3">Uhusiano (Wazazi/Mwenzi)</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Baba</label>
                                    @if(isset($selectedSpouse))
                                        <input type="text" name="father_name" class="form-control" placeholder="Jina la Baba">
                                    @else
                                        <select name="father_id" class="form-control select2">
                                            <option value="">Haijulikani / Hakuna</option>
                                            @foreach($potentialFathers as $father)
                                                <option value="{{ $father->id }}" 
                                                        data-clan="{{ $father->clan_id }}" 
                                                        data-family="{{ $father->family_id }}"
                                                        data-spouse-id="{{ $father->marriagesAsHusband->where('status', 'active')->first()?->wife_id }}"
                                                        {{ old('father_id', $selectedFatherId ?? '') == $father->id ? 'selected' : '' }}>
                                                    {{ $father->full_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Mama</label>
                                    @if(isset($selectedSpouse))
                                        <input type="text" name="mother_name" class="form-control" placeholder="Jina la Mama">
                                    @else
                                        <select name="mother_id" class="form-control select2">
                                            <option value="">Haijulikani / Hakuna</option>
                                            @foreach($potentialMothers as $mother)
                                                <option value="{{ $mother->id }}"
                                                        data-clan="{{ $mother->clan_id }}"
                                                        data-family="{{ $mother->family_id }}"
                                                        data-spouse-id="{{ $mother->marriagesAsWife->where('status', 'active')->first()?->husband_id }}"
                                                        {{ old('mother_id', $selectedMotherId ?? '') == $mother->id ? 'selected' : '' }}>
                                                    {{ $mother->full_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mt-2">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Mwenzi <small class="text-muted">(Hiari)</small></label>
                                    @if(isset($selectedSpouse))
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text bg-success text-white"><i class="fas fa-check"></i></span>
                                            </div>
                                            <input type="text" class="form-control" value="{{ $selectedSpouse->full_name }}" readonly>
                                            <input type="hidden" name="spouse_id" value="{{ $selectedSpouse->id }}">
                                        </div>
                                        <small class="text-success">Kumwongeza mwenzi wa {{ $selectedSpouse->full_name }}</small>
                                    @else
                                        <input type="text" name="spouse_name" class="form-control" value="{{ old('spouse_name') }}" placeholder="Ingiza jina ili kutengeneza wasifu wa mwenzi moja kwa moja">
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right Column: Photo & Contact --}}
            <div class="col-lg-4">
                {{-- Profile Photo Card --}}
                <div class="card card-warning card-outline">
                    <div class="card-body box-profile">
                        <div class="text-center mb-3">
                            <img class="profile-user-img img-fluid img-circle"
                                 src="{{ asset('vendor/adminlte/dist/img/user4-128x128.jpg') }}"
                                 alt="User profile picture"
                                 id="profilePreview"
                                 style="width: 120px; height: 120px; object-fit: cover;">
                        </div>
                        <h3 class="profile-username text-center text-muted">Pakia Picha <small>(Hiari)</small></h3>
                        
                        <div class="form-group mt-3">
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="profilePhoto" name="profile_photo" accept="image/*">
                                <label class="custom-file-label" for="profilePhoto">Chagua faili</label>
                            </div>
                            <small class="text-muted d-block text-center mt-1">Upeo wa 2MB (JPG, PNG)</small>
                        </div>
                    </div>
                </div>

                {{-- Contact Info Card --}}
                <div class="card card-success card-outline">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-address-book mr-1"></i> Mawasiliano</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label><i class="fas fa-envelope mr-1 text-muted"></i> Barua Pepe <small class="text-muted">(Hiari)</small></label>
                            <input type="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="Barua Pepe">
                        </div>
                        <div class="form-group">
                            <label><i class="fas fa-phone mr-1 text-muted"></i> Simu <small class="text-muted">(Hiari)</small></label>
                            <input type="text" name="phone" class="form-control" value="{{ old('phone') }}" placeholder="Namba ya Simu">
                        </div>

                        {{-- Cascading Location Picker --}}
                        <div class="card bg-light border-0">
                            <div class="card-body p-3">
                                <h6 class="text-primary mb-3"><i class="fas fa-map-marked-alt mr-1"></i> Maelezo ya Mahali <small class="text-muted">(Hiari)</small></h6>
                                
                                {{-- 1. Country --}}
                                <div class="form-group">
                                    <label class="small text-muted mb-0">1. Nchi</label>
                                    <select id="countrySelect" class="form-control select2" style="width: 100%;">
                                        <option value="">Chagua Nchi...</option>
                                        {{-- Populated via JS --}}
                                    </select>
                                </div>

                                {{-- 2. Region --}}
                                <div class="form-group">
                                    <label class="small text-muted mb-0">2. Mkoa / Jimbo</label>
                                    <select id="regionSelect" class="form-control select2" style="width: 100%;" disabled>
                                        <option value="">Chagua Nchi Kwanza...</option>
                                    </select>
                                </div>

                                {{-- 3. District --}}
                                <div class="form-group">
                                    <label class="small text-muted mb-0">3. Wilaya</label>
                                    <select id="districtSelect" class="form-control select2" style="width: 100%;" disabled>
                                        <option value="">Chagua Mkoa Kwanza...</option>
                                    </select>
                                </div>

                                {{-- 4. Street --}}
                                <div class="form-group">
                                    <label class="small text-muted mb-0">4. Mtaa / Kijiji</label>
                                    <select id="streetSelect" class="form-control select2" style="width: 100%;" disabled>
                                        <option value="">Chagua Wilaya Kwanza...</option>
                                    </select>
                                </div>

                                <button type="button" id="resetLocation" class="btn btn-sm btn-outline-danger mt-2">
                                    <i class="fas fa-undo"></i> Futa Mahali
                                </button>
                            </div>
                        </div>

                        {{-- Hidden Fields for Location Data --}}
                        <div id="locationDetails" class="d-none bg-white border p-3 rounded mt-2">
                            <h6 class="text-success"><i class="fas fa-check-circle"></i> Mahali Pamehifadhiwa</h6>
                            <p class="mb-1 small text-muted" id="displayAddress"></p>
                            <input type="hidden" name="address" id="address">
                            <input type="hidden" name="street" id="street">
                            <input type="hidden" name="city" id="city">
                            <input type="hidden" name="district" id="district">
                            <input type="hidden" name="region" id="region">
                            <input type="hidden" name="country" id="country">
                            <input type="hidden" name="current_lat" id="current_lat">
                            <input type="hidden" name="current_lng" id="current_lng">
                        </div>
                    </div>
                </div>

                {{-- Additional Info Card --}}
                <div class="card card-secondary card-outline">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-info-circle mr-1"></i> Maelezo ya Ziada</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label>Kazi / Shughuli <small class="text-muted">(Hiari)</small></label>
                            <input type="text" name="occupation" class="form-control" value="{{ old('occupation') }}" placeholder="Kazi/Kikazi">
                        </div>
                        <div class="form-group">
                            <label>Wasifu <small class="text-muted">(Hiari)</small></label>
                            <textarea name="biography" class="form-control" rows="3" placeholder="Wasifu mfupi...">{{ old('biography') }}</textarea>
                        </div>
                        <div class="form-group">
                            <label>Maelezo Binafsi ya Ndani <small class="text-muted">(Hiari)</small></label>
                            <textarea name="notes" class="form-control" rows="2" placeholder="Maelezo ya ndani ya siri...">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mb-5">
            <div class="col-12">
                <button type="submit" class="btn btn-success btn-lg float-right">
                    <i class="fas fa-save mr-1"></i> {{ session('warning') ? 'Thibitisha & Hifadhi' : 'Hifadhi Mwanachama' }}
                </button>
                <a href="{{ route('members.index') }}" class="btn btn-secondary btn-lg float-right mr-2">
                    <i class="fas fa-times mr-1"></i> Ghairi
                </a>
            </div>
        </div>
    </form>
@stop

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2-bootstrap-theme/0.1.0-beta.10/select2-bootstrap.min.css">
    <style>
        .card-title { font-weight: 600; }
        .custom-file-label::after { content: "Browse"; }
        .select2-container .select2-selection--single { height: 38px; }
        .select2-container--default .select2-selection--single .select2-selection__arrow { height: 36px; }
    </style>
@stop

@section('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script src="{{ asset('js/location-data.js') }}"></script>
    <script>
        $(document).ready(function() {
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
                $('#countrySelect').append(new Option(country, country));
            });

            // --- 3. Cascading Logic ---
            
            // Helper to reset child selects
            function resetSelect(selector, placeholder) {
                $(selector).empty().append(new Option(placeholder, "")).prop('disabled', true).trigger('change');
            }

            // Helper to configure Nominatim AJAX
            function configureNominatim(selector, placeholder, queryBuilder) {
                // Destroy existing Select2 to reset options/ajax
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
                    $(selector).append(new Option(item, item));
                });
                
                // Set selected value if provided
                if (selectedValue) {
                    $(selector).val(selectedValue);
                }

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
                resetSelect('#regionSelect', 'Select Region...');
                resetSelect('#districtSelect', 'Select District First...');
                resetSelect('#streetSelect', 'Select District First...');
                
                if (country) {
                    $('#regionSelect').prop('disabled', false);
                    
                    if (locationData[country]) {
                        // Use Static List (Keys of the object)
                        const regions = Object.keys(locationData[country]);
                        configureStatic('#regionSelect', 'Select Region...', regions);
                        updateLocationData({ country: country });
                    } else {
                        // Use Nominatim Search
                        configureNominatim('#regionSelect', 'Search Region...', function(term) {
                            return term + ", " + country;
                        });
                        updateLocationData({ country: country });
                    }
                }
            });

            // B. Region Change
            $('#regionSelect').on('change select2:select', function(e) {
                // Handle both static (change) and ajax (select2:select) events
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
                    // Use Static District List (Keys of the region object)
                    // Note: locationData[country][region] is now an object where keys are districts
                    const districts = Object.keys(locationData[country][region]);
                    configureStatic('#districtSelect', 'Select District...', districts);
                } else {
                    // Use Nominatim Search
                    configureNominatim('#districtSelect', 'Search District...', function(term) {
                        return term + ", " + region + ", " + country;
                    });
                }
                
                if (data) {
                    updateLocationData(data.address, data.lat, data.lon);
                } else {
                    updateLocationData({ region: region });
                }
            });

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
                const region = $('#regionSelect').val(); // Assuming static selection for now if we are here
                
                // Check if we have static wards for this district
                // We need to be careful: region might be from search, so it might not match the key exactly if not careful
                // But if we are in static mode, it should match.
                
                if (locationData[country] && locationData[country][region] && locationData[country][region][district]) {
                    // Use Static Ward List
                    configureStatic('#streetSelect', 'Select Street/Village...', locationData[country][region][district]);
                } else {
                    // Use Nominatim Search
                    configureNominatim('#streetSelect', 'Search Street/Village...', function(term) {
                        // Try to get region text safely
                        let regionText = "";
                        // Check if region is select2 data or simple value
                        if ($('#regionSelect').data('select2') && $('#regionSelect').select2('data')[0]) {
                             regionText = $('#regionSelect').select2('data')[0].text.split(',')[0];
                        } else {
                            regionText = $('#regionSelect').val();
                        }
                        return term + ", " + district + ", " + regionText + ", " + $('#countrySelect').val();
                    });
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
                    // For static street, we just update the street field
                    // We don't have lat/lon for static streets unless we add them to data
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

            // Image Preview & Compression
            $('#profilePhoto').change(function(e){
                let file = e.target.files[0];
                if (!file) return;
                
                $(this).next('.custom-file-label').addClass("selected").html(file.name);
                
                let reader = new FileReader();
                reader.onload = (readerEvent) => { 
                    let img = new Image();
                    img.onload = function() {
                        let canvas = document.createElement('canvas');
                        let ctx = canvas.getContext('2d');
                        let maxWidth = 1920;
                        let maxHeight = 1920;
                        let width = img.width;
                        let height = img.height;

                        if (width > height) {
                            if (width > maxWidth) {
                                height *= maxWidth / width;
                                width = maxWidth;
                            }
                        } else {
                            if (height > maxHeight) {
                                width *= maxHeight / height;
                                height = maxHeight;
                            }
                        }
                        
                        canvas.width = width;
                        canvas.height = height;
                        ctx.drawImage(img, 0, 0, width, height);
                        
                        // Compress to 70% quality JPEG
                        canvas.toBlob(function(blob) {
                            if(blob) {
                                let newFile = new File([blob], file.name.replace(/\.[^/.]+$/, "") + ".jpg", { type: "image/jpeg", lastModified: Date.now() });
                                let dataTransfer = new DataTransfer();
                                dataTransfer.items.add(newFile);
                                document.getElementById('profilePhoto').files = dataTransfer.files;
                                
                                $('#profilePreview').attr('src', URL.createObjectURL(blob));
                            }
                        }, 'image/jpeg', 0.92);
                    };
                    img.src = readerEvent.target.result;
                }
                reader.readAsDataURL(file);
            });

            // Toggle Deceased Fields
            function toggleDeceased() {
                if($('#statusSelect').val() === 'deceased') {
                    $('#deceasedFields').slideDown();
                } else {
                    $('#deceasedFields').slideUp();
                }
            }
            // Direct Link confirmation dialog
            $(document).on('click', '.link-direct-btn', function(e) {
                const name = $(this).data('name');
                const dob = $(this).data('dob');
                let msg = `Je, una uhakika wewe ni ${name}`;
                if (dob && dob !== 'Hajajaza') {
                    msg += ` (aliyezaliwa tarehe ${dob})`;
                }
                msg += `?\n\nIlani: Ukishajiunganisha, huwezi kubadilisha wasifu huu mwenyewe bila msaada wa Admin.`;
                
                if (!confirm(msg)) {
                    e.preventDefault();
                }
            });

            let isAutoSelecting = false;

            function handleParentSelection(type) {
                if (isAutoSelecting) return;

                const select = $('select[name="' + type + '_id"]');
                const selectedOption = select.find('option:selected');
                if (!selectedOption.val()) return;

                const clanId = selectedOption.data('clan');
                const spouseId = selectedOption.data('spouse-id');

                if (spouseId) {
                    const otherType = type === 'father' ? 'mother' : 'father';
                    const otherSelect = $('select[name="' + otherType + '_id"]');
                    if (!otherSelect.val()) {
                        isAutoSelecting = true;
                        otherSelect.val(spouseId).trigger('change');
                        isAutoSelecting = false;
                    }
                }

                // Clan auto-population: set directly from the selected parent
                if (clanId) {
                    $('select[name="clan_id"]').val(clanId).trigger('change');
                }
            }

            $('select[name="father_id"]').change(function() {
                handleParentSelection('father');
            });

            $('select[name="mother_id"]').change(function() {
                handleParentSelection('mother');
            });

            // Run on initial load
            if ($('select[name="father_id"]').val()) {
                handleParentSelection('father');
            } else if ($('select[name="mother_id"]').val()) {
                handleParentSelection('mother');
            }

            // Date of Birth compilation from Day/Month/Year selects
            function compileDOB() {
                const day = $('#dob_day').val();
                const month = $('#dob_month').val();
                const year = $('#dob_year').val();
                if (day && month && year) {
                    $('#dob_hidden').val(`${year}-${month}-${day}`);
                } else {
                    $('#dob_hidden').val('');
                }
            }

            $('#dob_day, #dob_month, #dob_year').change(compileDOB);

            // Pre-populate selects from hidden field on load
            const initialDob = $('#dob_hidden').val();
            if (initialDob) {
                const parts = initialDob.split('-');
                if (parts.length === 3) {
                    $('#dob_year').val(parts[0]);
                    $('#dob_month').val(parts[1]);
                    $('#dob_day').val(parts[2]);
                }
            }

            $('#statusSelect').change(toggleDeceased);
            toggleDeceased(); 


        });
    </script>
@stop
