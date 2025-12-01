@extends('adminlte::page')

@section('title', $gallery->title)

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>{{ $gallery->title }}</h1>
        <div>
            <button type="button" class="btn btn-success" data-toggle="modal" data-target="#uploadModal">
                <i class="fas fa-upload"></i> Upload Photos
            </button>
            <a href="{{ route('galleries.index') }}" class="btn btn-default">Back</a>
        </div>
    </div>
@stop

@section('content')
    @if($gallery->description)
        <p class="text-muted">{{ $gallery->description }}</p>
    @endif

    <div class="row">
        @forelse($gallery->photos as $photo)
            <div class="col-md-3 mb-3">
                <div class="card">
                    <a href="{{ asset('storage/' . $photo->photo_path) }}" data-lightbox="gallery" data-title="{{ $photo->caption }}">
                        <img src="{{ asset('storage/' . $photo->photo_path) }}" class="card-img-top" alt="{{ $photo->caption }}" style="height: 200px; object-fit: cover; cursor: pointer;">
                    </a>
                    <div class="card-body p-2">
                        @if($photo->caption)
                            <p class="card-text text-sm mb-1">{{ $photo->caption }}</p>
                        @endif
                        @if($photo->member)
                            <small class="text-muted"><i class="fas fa-user"></i> {{ $photo->member->full_name }}</small>
                        @endif
                        <form action="{{ route('galleries.delete-photo', $photo->id) }}" method="POST" class="d-inline float-right" onsubmit="return confirm('Delete this photo?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info">
                    No photos yet. Click "Upload Photos" to add some!
                </div>
            </div>
        @endforelse
    </div>

    <!-- Upload Modal -->
    <div class="modal fade" id="uploadModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form action="{{ route('galleries.upload-photos', $gallery) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Upload Photos</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Select Photos</label>
                            <input type="file" name="photos[]" class="form-control" multiple accept="image/*" required>
                            <small class="text-muted">You can select multiple photos</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Upload</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.min.css">
@stop

@section('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js"></script>
@stop
