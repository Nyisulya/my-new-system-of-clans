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
                        <div id="compressionStatus" class="mt-1"></div>
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

        // ─── Client-side compression for gallery uploads ───────────────────
        const MAX_WIDTH    = 1200;   // px
        const MAX_HEIGHT   = 1200;   // px
        const JPEG_QUALITY = 0.75;   // 75% quality

        let compressedFiles = []; // holds the DataTransfer-ready compressed files

        /**
         * Compress a single File (image) and return a Promise<File>
         */
        function compressImage(file) {
            return new Promise((resolve) => {
                const reader = new FileReader();
                reader.onload = (ev) => {
                    const img = new Image();
                    img.onload = () => {
                        let w = img.width, h = img.height;
                        const ratio = Math.min(MAX_WIDTH / w, MAX_HEIGHT / h, 1.0);
                        w = Math.round(w * ratio);
                        h = Math.round(h * ratio);

                        const canvas = document.createElement('canvas');
                        canvas.width  = w;
                        canvas.height = h;
                        const ctx = canvas.getContext('2d');
                        ctx.fillStyle = '#ffffff';
                        ctx.fillRect(0, 0, w, h);
                        ctx.drawImage(img, 0, 0, w, h);

                        canvas.toBlob((blob) => {
                            const newName = file.name.replace(/\.[^/.]+$/, '') + '.jpg';
                            resolve(new File([blob], newName, { type: 'image/jpeg', lastModified: Date.now() }));
                        }, 'image/jpeg', JPEG_QUALITY);
                    };
                    img.src = ev.target.result;
                };
                reader.readAsDataURL(file);
            });
        }

        document.getElementById('galleryFileInput').addEventListener('change', async function(e) {
            const container  = document.getElementById('photoPreviewContainer');
            const statusEl   = document.getElementById('compressionStatus');
            container.innerHTML = '';
            compressedFiles     = [];

            if (!e.target.files.length) return;

            statusEl.innerHTML = '<div class="alert alert-info py-1"><i class="fas fa-spinner fa-spin"></i> Inapunguza picha... tafadhali subiri.</div>';

            const origFiles = Array.from(e.target.files);
            let totalOrig = 0, totalComp = 0;

            for (const file of origFiles) {
                totalOrig += file.size;
                const compressed = await compressImage(file);
                totalComp += compressed.size;
                compressedFiles.push(compressed);

                // Show preview
                const col = document.createElement('div');
                col.className = 'col-4 mb-2';
                const previewUrl = URL.createObjectURL(compressed);
                col.innerHTML = `
                    <div class="position-relative">
                        <img src="${previewUrl}" class="img-fluid rounded" style="height:100px;object-fit:cover;">
                        <small class="d-block text-muted text-center" style="font-size:10px;">
                            ${(file.size/1024).toFixed(0)}KB → ${(compressed.size/1024).toFixed(0)}KB
                        </small>
                    </div>`;
                container.appendChild(col);
            }

            const saved = Math.round((1 - totalComp/totalOrig) * 100);
            statusEl.innerHTML = `<div class="alert alert-success py-1">
                <i class="fas fa-check"></i> Picha ${compressedFiles.length} zimepunguzwa.
                Ukubwa: ${(totalOrig/1024/1024).toFixed(1)}MB → ${(totalComp/1024/1024).toFixed(1)}MB
                (imepungua ${saved}%)
            </div>`;

            // Replace file input files with compressed versions
            const dt = new DataTransfer();
            compressedFiles.forEach(f => dt.items.add(f));
            e.target.files = dt.files;
        });
    </script>
@stop

