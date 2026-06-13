@extends('adminlte::page')

@section('title', 'Add Family')

@section('content_header')
    <h1><i class="fas fa-home"></i> Add New Family</h1>
@stop

@section('content')
    <div class="card">
        <form action="{{ route('families.store') }}" method="POST">
            @csrf
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Clan <span class="text-danger">*</span></label>
                            <select name="clan_id" class="form-control @error('clan_id') is-invalid @enderror" required>
                                <option value="">Select Clan...</option>
                                @foreach(\App\Models\Clan::all() as $clan)
                                    <option value="{{ $clan->id }}" {{ old('clan_id') == $clan->id ? 'selected' : '' }}>
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
                            <label>Family Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                   value="{{ old('name') }}" required>
                            @error('name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Surname <span class="text-danger">*</span></label>
                            <input type="text" name="surname" class="form-control @error('surname') is-invalid @enderror" 
                                   value="{{ old('surname') }}" required>
                            @error('surname')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Origin Place</label>
                            <input type="text" name="origin_place" class="form-control @error('origin_place') is-invalid @enderror" 
                                   value="{{ old('origin_place') }}">
                            @error('origin_place')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Established Date</label>
                            <input type="date" name="established_date" class="form-control @error('established_date') is-invalid @enderror" 
                                   value="{{ old('established_date') }}">
                            @error('established_date')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Status</label>
                            <select name="is_active" class="form-control">
                                <option value="1" selected>Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" class="form-control @error('description') is-invalid @enderror" 
                              rows="3">{{ old('description') }}</textarea>
                    @error('description')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save"></i> Create Family
                </button>
                <a href="{{ route('families.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
@stop
