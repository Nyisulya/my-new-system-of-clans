@extends('layouts.app')

@section('title', 'Picha za Familia')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
        <h1><i class="fas fa-images text-primary"></i> Picha za Familia</h1>
        <div class="d-flex align-items-center flex-wrap gap-2">
            <button type="button" class="btn btn-success font-weight-bold shadow-sm mr-2" data-toggle="modal" data-target="#uploadModal">
                <i class="fas fa-upload mr-1"></i> Pakia Picha
            </button>
            <a href="{{ route('galleries.create') }}" class="btn btn-primary font-weight-bold shadow-sm">
                <i class="fas fa-plus mr-1"></i> Albamu Mpya
            </a>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        @forelse($galleries as $gallery)
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100 border-0 shadow-sm gallery-card" style="border-radius: 12px; overflow: hidden; transition: all 0.3s ease;">
                    @if($gallery->photos->first())
                        @php
                            $coverPath  = $gallery->photos->first()->photo_path;
                            $cloudName  = config('cloudinary.cloud.cloud_name');
                            $coverUrl   = ($cloudName && !str_contains($coverPath, '.'))
                                ? "https://res.cloudinary.com/{$cloudName}/image/upload/w_600,h_300,c_fill,q_auto,f_auto/{$coverPath}"
                                : asset('storage/' . $coverPath);
                        @endphp
                        <div class="cover-image-container" style="position: relative; overflow: hidden; height: 220px;">
                            <img src="{{ $coverUrl }}" class="card-img-top w-100 h-100" alt="{{ $gallery->title }}" style="object-fit: cover; transition: transform 0.5s ease;">
                        </div>
                    @else
                        @php
                            // Set a nice gradient for default empty album
                            $isDefault = $gallery->title === 'Picha za Jumla';
                            $gradient = $isDefault 
                                ? 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)' 
                                : 'linear-gradient(135deg, #a1c4fd 0%, #c2e9fb 100%)';
                        @endphp
                        <div class="card-img-top" style="height: 220px; background: {{ $gradient }}; display: flex; flex-direction: column; align-items: center; justify-content: center; color: white;">
                            <i class="fas fa-images fa-4x mb-2 opacity-75"></i>
                            @if($isDefault)
                                <small class="text-uppercase tracking-wider font-weight-bold opacity-75">Albamu Maalum</small>
                            @endif
                        </div>
                    @endif
                    <div class="card-body d-flex flex-column p-4">
                        <h5 class="card-title font-weight-bold text-dark mb-2">{{ $gallery->title }}</h5>
                        <p class="card-text text-muted flex-grow-1 mb-4" style="font-size: 0.9rem;">
                            {{ $gallery->description ? Str::limit($gallery->description, 100) : 'Hakuna maelezo kwa albamu hii bado.' }}
                        </p>
                        <div class="d-flex justify-content-between align-items-center mt-auto">
                            <span class="badge badge-pill badge-primary px-3 py-2" style="font-size: 0.8rem;">
                                <i class="fas fa-image mr-1"></i> {{ $gallery->photos_count }} picha
                            </span>
                            <div class="d-flex align-items-center gap-2">
                                <a href="{{ route('galleries.show', $gallery) }}" class="btn btn-sm btn-outline-primary font-weight-bold px-3 mr-2" style="border-radius: 6px;">
                                    Tazama <i class="fas fa-arrow-right ml-1"></i>
                                </a>
                                @if(auth()->id() === $gallery->created_by || auth()->user()->isAdmin())
                                    @if($gallery->title !== 'Picha za Jumla')
                                        <form action="{{ route('galleries.destroy', $gallery) }}" method="POST" onsubmit="return confirm('Una uhakika unataka kufuta album hii pamoja na picha zake zote?');" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" style="border-radius: 6px;" title="Futa Albamu">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info py-3 shadow-sm border-0" style="border-radius: 8px;">
                    <i class="fas fa-info-circle mr-2"></i> Hakuna albamu bado. Unda albamu yako ya kwanza ya familia!
                </div>
            </div>
        @endforelse
    </div>

    @if($galleries->hasPages())
        <div class="d-flex justify-content-center mt-4 w-100">
            {{ $galleries->links() }}
        </div>
    @endif

    <!-- Upload Modal — AJAX uploader -->
    <div class="modal fade" id="uploadModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content" style="border-radius: 12px; overflow: hidden; border: 0;">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title font-weight-bold"><i class="fas fa-upload mr-2"></i> Pakia Picha Mpya</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-4">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold text-dark">Chagua Albamu (Hiari)</label>
                                <select name="gallery_id" id="uploadGalleryId" class="form-control" style="border-radius: 6px; height: 38px;">
                                    <option value="">-- Picha za Jumla (Bila Albamu) --</option>
                                    @foreach($allGalleries as $g)
                                        <option value="{{ $g->id }}">{{ $g->title }}</option>
                                    @endforeach
                                </select>
                                <small class="text-muted d-block mt-1">
                                    Picha zitawekwa kwenye albamu uliyochagua au kwenye "Picha za Jumla" kama ukiiacha wazi.
                                </small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold text-dark">Chagua Faili za Picha</label>
                                <div class="custom-file">
                                    <input type="file" name="photos[]" class="custom-file-input" id="galleryFileInput" multiple accept="image/*">
                                    <label class="custom-file-label" for="galleryFileInput" style="border-radius: 6px; overflow: hidden;">Chagua faili...</label>
                                </div>
                                <small class="text-muted d-block mt-1">Unaweza kuchagua picha nyingi kwa mara moja.</small>
                            </div>
                        </div>
                    </div>

                    <div id="compressionStatus" class="mt-3"></div>
                    <div id="photoPreviewContainer" class="row mt-2" style="max-height: 240px; overflow-y: auto;"></div>

                    <div id="uploadProgressContainer" class="mt-3" style="display:none;">
                        <div class="progress" style="height: 15px; border-radius: 30px;">
                            <div id="uploadProgressBar" class="progress-bar progress-bar-striped progress-bar-animated bg-success"
                                 role="progressbar" style="width:0%; border-radius: 30px;">0%</div>
                        </div>
                        <small id="uploadProgressText" class="text-muted d-block text-center mt-1"></small>
                    </div>
                </div>
                <div class="modal-footer bg-light p-3">
                    <button type="button" class="btn btn-secondary font-weight-bold px-4" data-dismiss="modal" id="cancelUploadBtn" style="border-radius: 6px;">Ghairi</button>
                    <button type="button" class="btn btn-success font-weight-bold px-4" id="startUploadBtn" disabled style="border-radius: 6px;">
                        <i class="fas fa-cloud-upload-alt mr-1"></i> Pakia Picha
                    </button>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        .gallery-card {
            box-shadow: 0 4px 15px rgba(0,0,0,0.05) !important;
        }
        .gallery-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.12) !important;
        }
        .gallery-card:hover .cover-image-container img {
            transform: scale(1.05);
        }
        .cover-image-container {
            border-bottom: 1px solid rgba(0,0,0,0.05);
        }
        .custom-file-label::after {
            content: "Tafuta";
        }
    </style>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            // Update input label with selected files count
            $('#galleryFileInput').change(function(e) {
                let filesCount = e.target.files.length;
                let label = filesCount === 1 ? e.target.files[0].name : filesCount + ' files selected';
                $(this).next('.custom-file-label').html(label);
            });
        });

        // ─── Settings ──────────────────────────────────────────────────────
        const MAX_W  = 1920;
        const MAX_H  = 1920;
        const QUALITY = 0.92;
        const UPLOAD_URL = '{{ route('galleries.upload-photos-general') }}';
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

            statusEl.innerHTML = '<div class="alert alert-info py-2 shadow-sm border-0" style="border-radius: 6px;"><i class="fas fa-spinner fa-spin mr-1"></i> Inapunguza ukubwa wa picha...</div>';

            let totalOrig = 0, totalComp = 0;
            for (const file of Array.from(e.target.files)) {
                totalOrig += file.size;
                const compressed = await compressImage(file);
                totalComp += compressed.size;
                compressedFiles.push(compressed);

                // preview card
                const col = document.createElement('div');
                col.className = 'col-3 mb-2';
                col.innerHTML = `
                    <div class="card p-1 border shadow-sm h-100" style="border-radius: 6px; background: #fafafa;">
                        <img src="${URL.createObjectURL(compressed)}" class="img-fluid rounded" style="height:70px; width: 100%; object-fit:cover;">
                        <small class="d-block text-center text-muted mt-1" style="font-size:10px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                            ${(file.size/1024).toFixed(0)}KB → ${(compressed.size/1024).toFixed(0)}KB
                        </small>
                    </div>`;
                container.appendChild(col);
            }

            const saved = Math.round((1 - totalComp / totalOrig) * 100);
            statusEl.innerHTML = `<div class="alert alert-success py-2 shadow-sm border-0" style="border-radius: 6px;">
                <i class="fas fa-check-circle mr-1"></i>
                Tayari kwa kupakiwa: Picha <strong>${compressedFiles.length}</strong>.
                Ukubwa umepungua kutoka ${(totalOrig/1024/1024).toFixed(1)}MB hadi ${(totalComp/1024/1024).toFixed(1)}MB (Umeokoa ${saved}%)
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
            const galleryIdSelect   = document.getElementById('uploadGalleryId');

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
                
                if (galleryIdSelect && galleryIdSelect.value) {
                    formData.append('gallery_id', galleryIdSelect.value);
                }

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
                statusEl.innerHTML = '<div class="alert alert-success py-2 shadow-sm border-0" style="border-radius: 6px;"><i class="fas fa-check-circle mr-1"></i> Picha zote zimepakiwa kwa mafanikio! Ukurasa unafunguliwa upya...</div>';
            } else {
                statusEl.innerHTML = `<div class="alert alert-warning py-2 shadow-sm border-0" style="border-radius: 6px;"><i class="fas fa-exclamation-triangle mr-1"></i> Picha ${failed} zilishindwa kupakiwa. Nyingine zimefanikiwa.</div>`;
            }
            setTimeout(() => location.reload(), 1500);
        });
    </script>
@stop
