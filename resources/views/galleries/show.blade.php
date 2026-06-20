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

    <!-- Upload Modal — AJAX one-by-one uploader -->
    <div class="modal fade" id="uploadModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-upload"></i> Pakia Picha</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Chagua Picha (unaweza chagua nyingi)</label>
                        <input type="file" name="photos[]" class="form-control" multiple accept="image/*" id="galleryFileInput">
                        <small class="text-muted">Kila picha itapunguzwa na kupakiwa moja moja — hakuna tatizo la ukubwa.</small>
                    </div>
                    <div id="compressionStatus" class="mt-1"></div>
                    <div id="photoPreviewContainer" class="row mt-2"></div>
                    <div id="uploadProgressContainer" class="mt-2" style="display:none;">
                        <div class="progress">
                            <div id="uploadProgressBar" class="progress-bar progress-bar-striped progress-bar-animated bg-success"
                                 role="progressbar" style="width:0%">0%</div>
                        </div>
                        <small id="uploadProgressText" class="text-muted"></small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal" id="cancelUploadBtn">Ghairi</button>
                    <button type="button" class="btn btn-success" id="startUploadBtn" disabled>
                        <i class="fas fa-cloud-upload-alt"></i> Pakia
                    </button>
                </div>
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

        // ─── Settings ──────────────────────────────────────────────────────
        const MAX_W  = 1200;
        const MAX_H  = 1200;
        const QUALITY = 0.75;
        const UPLOAD_URL = '{{ route('galleries.upload-photos', $gallery) }}';
        const CSRF_TOKEN = '{{ csrf_token() }}';

        let compressedFiles = [];

        // ─── Compress one image → returns Promise<File> ────────────────────
        function compressImage(file) {
            return new Promise(resolve => {
                const reader = new FileReader();
                reader.onload = ev => {
                    const img = new Image();
                    img.onload = () => {
                        let w = img.width, h = img.height;
                        const ratio = Math.min(MAX_W / w, MAX_H / h, 1.0);
                        w = Math.round(w * ratio);
                        h = Math.round(h * ratio);
                        const canvas = document.createElement('canvas');
                        canvas.width = w; canvas.height = h;
                        const ctx = canvas.getContext('2d');
                        ctx.fillStyle = '#fff';
                        ctx.fillRect(0, 0, w, h);
                        ctx.drawImage(img, 0, 0, w, h);
                        canvas.toBlob(blob => {
                            const name = file.name.replace(/\.[^/.]+$/, '') + '.jpg';
                            resolve(new File([blob], name, { type: 'image/jpeg' }));
                        }, 'image/jpeg', QUALITY);
                    };
                    img.src = ev.target.result;
                };
                reader.readAsDataURL(file);
            });
        }

        // ─── On file selection: compress & preview ─────────────────────────
        document.getElementById('galleryFileInput').addEventListener('change', async function(e) {
            const container = document.getElementById('photoPreviewContainer');
            const statusEl  = document.getElementById('compressionStatus');
            const uploadBtn = document.getElementById('startUploadBtn');
            container.innerHTML = '';
            compressedFiles = [];
            uploadBtn.disabled = true;

            if (!e.target.files.length) return;

            statusEl.innerHTML = '<div class="alert alert-info py-1"><i class="fas fa-spinner fa-spin"></i> Inapunguza picha...</div>';

            let totalOrig = 0, totalComp = 0;
            for (const file of Array.from(e.target.files)) {
                totalOrig += file.size;
                const compressed = await compressImage(file);
                totalComp += compressed.size;
                compressedFiles.push(compressed);

                // preview card
                const col = document.createElement('div');
                col.className = 'col-4 mb-2';
                col.innerHTML = `
                    <img src="${URL.createObjectURL(compressed)}" class="img-fluid rounded" style="height:90px;object-fit:cover;">
                    <small class="d-block text-center text-muted" style="font-size:10px;">
                        ${(file.size/1024).toFixed(0)}KB → ${(compressed.size/1024).toFixed(0)}KB
                    </small>`;
                container.appendChild(col);
            }

            const saved = Math.round((1 - totalComp / totalOrig) * 100);
            statusEl.innerHTML = `<div class="alert alert-success py-1">
                <i class="fas fa-check-circle"></i>
                Picha <strong>${compressedFiles.length}</strong> tayari.
                ${(totalOrig/1024/1024).toFixed(1)}MB → ${(totalComp/1024/1024).toFixed(1)}MB
                (imepungua ${saved}%)
            </div>`;
            uploadBtn.disabled = false;
        });

        // ─── Upload one-by-one via AJAX ────────────────────────────────────
        document.getElementById('startUploadBtn').addEventListener('click', async function() {
            if (!compressedFiles.length) return;

            const progressContainer = document.getElementById('uploadProgressContainer');
            const progressBar       = document.getElementById('uploadProgressBar');
            const progressText      = document.getElementById('uploadProgressText');
            const statusEl          = document.getElementById('compressionStatus');
            const uploadBtn         = document.getElementById('startUploadBtn');
            const cancelBtn         = document.getElementById('cancelUploadBtn');

            progressContainer.style.display = 'block';
            uploadBtn.disabled  = true;
            cancelBtn.disabled  = true;

            let done = 0;
            let failed = 0;

            for (const file of compressedFiles) {
                progressText.textContent = `Inapakia ${done + 1} kati ya ${compressedFiles.length}: ${file.name}`;

                const formData = new FormData();
                formData.append('_token', CSRF_TOKEN);
                formData.append('photos[]', file);

                try {
                    const resp = await fetch(UPLOAD_URL, { method: 'POST', body: formData });
                    if (!resp.ok) throw new Error('Upload failed');
                } catch (err) {
                    failed++;
                }

                done++;
                const pct = Math.round((done / compressedFiles.length) * 100);
                progressBar.style.width = pct + '%';
                progressBar.textContent = pct + '%';
            }

            // Done — reload page to show new photos
            if (failed === 0) {
                statusEl.innerHTML = '<div class="alert alert-success"><i class="fas fa-check-circle"></i> Picha zote zimepakiwa! Ukurasa unafunguka upya...</div>';
            } else {
                statusEl.innerHTML = `<div class="alert alert-warning"><i class="fas fa-exclamation-triangle"></i> ${failed} picha hazikupakiwa. Nyingine zimefanikiwa.</div>`;
            }
            setTimeout(() => location.reload(), 1500);
        });
    </script>
@stop


