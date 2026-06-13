@extends('adminlte::page')

@section('title', 'Family Gallery')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-images"></i> Family Gallery</h1>
        <a href="{{ route('galleries.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> New Album
        </a>
    </div>
@stop

@section('content')
    <div class="row">
        @forelse($galleries as $gallery)
            <div class="col-md-4">
                <div class="card">
                    @if($gallery->photos->first())
                        <img src="{{ asset('storage/' . $gallery->photos->first()->photo_path) }}" class="card-img-top" alt="{{ $gallery->title }}" style="height: 200px; object-fit: cover;">
                    @else
                        <div class="card-img-top bg-secondary" style="height: 200px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-images fa-5x text-white-50"></i>
                        </div>
                    @endif
                    <div class="card-body">
                        <h5 class="card-title">{{ $gallery->title }}</h5>
                        <p class="card-text text-muted">{{ Str::limit($gallery->description, 100) }}</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="badge badge-info">{{ $gallery->photos_count }} photos</span>
                            <a href="{{ route('galleries.show', $gallery) }}" class="btn btn-sm btn-primary">
                                View Album <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> No albums yet. Create your first family photo album!
                </div>
            </div>
        @endforelse
    </div>
@stop
