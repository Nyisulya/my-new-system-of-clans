@extends('adminlte::page')

@section('title', 'Add Clan')

@section('content_header')
    <h1><i class="fas fa-sitemap"></i> Add New Clan</h1>
@stop

@section('content')
    <div class="card">
        <form action="{{ route('clans.store') }}" method="POST">
            @csrf
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Clan Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                   value="{{ old('name') }}" required>
                            @error('name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Origin Location</label>
                            <input type="text" name="origin_location" class="form-control @error('origin_location') is-invalid @enderror" 
                                   value="{{ old('origin_location') }}" placeholder="e.g., County Cork, Ireland">
                            @error('origin_location')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Founding Date</label>
                            <input type="date" name="founding_date" class="form-control @error('founding_date') is-invalid @enderror" 
                                   value="{{ old('founding_date') }}">
                            @error('founding_date')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
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
                    <i class="fas fa-save"></i> Create Clan
                </button>
                <a href="{{ route('clans.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
@stop
