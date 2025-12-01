@extends('adminlte::page')

@section('title', 'Add Branch')

@section('content_header')
    <h1><i class="fas fa-code-branch"></i> Add New Branch</h1>
@stop

@section('content')
    <div class="card">
        <form action="{{ route('branches.store') }}" method="POST">
            @csrf
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Family <span class="text-danger">*</span></label>
                            <select name="family_id" class="form-control @error('family_id') is-invalid @enderror" required>
                                <option value="">Select Family...</option>
                                @foreach(\App\Models\Family::with('clan')->get() as $family)
                                    <option value="{{ $family->id }}" {{ old('family_id') == $family->id ? 'selected' : '' }}>
                                        {{ $family->name }} ({{ $family->clan->name }})
                                    </option>
                                @endforeach
                            </select>
                            @error('family_id')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Branch Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                   value="{{ old('name') }}" required>
                            @error('name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Location</label>
                            <input type="text" name="location" class="form-control @error('location') is-invalid @enderror" 
                                   value="{{ old('location') }}" placeholder="City, State/Country">
                            @error('location')
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
                    <i class="fas fa-save"></i> Create Branch
                </button>
                <a href="{{ route('branches.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
@stop
