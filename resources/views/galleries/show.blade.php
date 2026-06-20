@extends('layouts.app')

@section('title', $gallery->title)

@section('content_header')
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h1>{{ $gallery->title }}</h1>
        <div class="d-flex align-items-center flex-wrap gap-2">
            <button type="button" class="btn btn-success mr-2" data-toggle="modal" data-target="#uploadModal">
                <i class="fas fa-upload"></i> Upload
            </button>
            @if(auth()->id() === $gallery->created_by || auth()->user()->isAdmin())
            <form action="{{ route('galleries.destroy', $gallery) }}" method="POST" class="d-inline mr-2" onsubmit="return confirm('Una uhakika unataka kufuta album hii yote na picha zake zote?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-trash"></i> Futa Album
                </button>
            </form>
            @endif
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
            @php
                $cloudName = config('cloudinary.cloud.cloud_name');
                // Detect Cloudinary vs local: Cloudinary paths have no file extension
                if ($cloudName && !str_contains($photo->photo_path, '.')) {
                    $fullUrl  = "https://res.cloudinary.com/{$cloudName}/image/upload/q_auto,f_auto/{$photo->photo_path}";
                    $thumbUrl = "https://res.cloudinary.com/{$cloudName}/image/upload/w_400,h_300,c_fill,q_auto,f_auto/{$photo->photo_path}";
                } else {
                    $fullUrl  = asset('storage/' . $photo->photo_path);
                    $thumbUrl = $fullUrl;
                }
            @endphp
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card h-100 shadow-sm gallery-card">
                    <a href="{{ $fullUrl }}" data-lightbox="gallery-{{ $gallery->id }}" data-title="{{ $photo->caption ?? '' }}">
                        <img src="{{ $thumbUrl }}"
                             class="card-img-top"
                             alt="{{ $photo->caption ?? 'Gallery Photo' }}"
                             style="height: 200px; object-fit: cover; cursor: zoom-in;">
                    </a>
                    <div class="card-body p-2">
                        @if($photo->caption)
                            <p class="card-text small mb-1">{{ $photo->caption }}</p>
                        @endif
                        @if($photo->member)
                            <small class="text-muted"><i class="fas fa-user"></i> {{ $photo->member->full_name }}</small>
                        @endif
                        <form action="{{ route('galleries.delete-photo', $photo->id) }}" method="POST" class="d-inline float-right" onsubmit="return confirm('Futa picha hii?');">
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
                    <i class="fas fa-images"></i> Bado hakuna picha. Bonyeza "Upload" kuongeza picha!
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
                        <h5 class="modal-title"><i class="fas fa-upload"></i> Pakia Picha</h5>
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Chagua Picha (unaweza chagua nyingi)</label>
                            <input type="file" name="photos[]" class="form-control" multiple accept="image/*" required id="galleryFileInput">
                            <small class="text-muted">Picha zitapunguzwa kiotomatiki kabla ya kupakiwa.</small>
                        </div>
                        <div id="photoPreviewContainer" class="row mt-2"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Ghairi</button>
                        <button type="submit" class="btn btn-success"><i class="fas fa-cloud-upload-alt"></i> Pakia</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/css/lightbox.min.css">
    <style>
        .gallery-card { transition: transform 0.2s, box-shadow 0.2s; }
        .gallery-card:hover { transform: translateY(-4px); box-shadow: 0 8px 20px rgba(0,0,0,0.18) !important; }
        .gallery-card .card-img-top { transition: opacity 0.2s; }
        .gallery-card .card-img-top:hover { opacity: 0.88; }
    </style>
@stop

@section('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/js/lightbox.min.js"></script>
    <script>
        lightbox.option({ resizeDuration: 200, wrapAround: true, albumLabel: 'Picha %1 kati ya %2' });

        // Preview selected photos before upload
        document.getElementById('galleryFileInput').addEventListener('change', function(e) {
            const container = document.getElementById('photoPreviewContainer');
            container.innerHTML = '';
            Array.from(e.target.files).forEach(file => {
                const reader = new FileReader();
                reader.onload = ev => {
                    const col = document.createElement('div');
                    col.className = 'col-4 mb-2';
                    col.innerHTML = `<img src="${ev.target.result}" class="img-fluid rounded" style="height:100px;object-fit:cover;">`;
                    container.appendChild(col);
                };
                reader.readAsDataURL(file);
            });
        });
    </script>
@stop
