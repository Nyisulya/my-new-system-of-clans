<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Gallery;
use App\Models\GalleryPhoto;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GalleryController extends Controller
{
    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    public function index()
    {
        // Auto-create default "Picha za Jumla" gallery if it does not exist
        $defaultTitle = 'Picha za Jumla';
        Gallery::firstOrCreate(
            ['title' => $defaultTitle],
            [
                'description' => 'Picha zilizopakiwa bila albamu maalum.',
                'created_by' => Auth::id() ?? 1,
            ]
        );

        $galleries = Gallery::withCount('photos')
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        $allGalleries = Gallery::orderBy('title', 'asc')->get();

        return view('galleries.index', compact('galleries', 'allGalleries'));
    }

    public function create()
    {
        return view('galleries.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $gallery = Gallery::create([
            'title' => $request->title,
            'description' => $request->description,
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('galleries.show', $gallery)->with('success', 'Gallery created successfully.');
    }

    public function show(Gallery $gallery)
    {
        $gallery->load(['photos.member']);
        return view('galleries.show', compact('gallery'));
    }

    public function uploadPhotos(Request $request, Gallery $gallery)
    {
        $request->validate([
            'photos' => 'required|array',
            'photos.*' => 'image|max:5120', // 5MB max
        ]);

        foreach ($request->file('photos') as $index => $photo) {
            $result = $this->imageService->uploadProfilePhoto($photo, 'galleries');
            
            GalleryPhoto::create([
                'gallery_id' => $gallery->id,
                'photo_path' => $result['path'],
                'caption' => $request->captions[$index] ?? null,
                'member_id' => $request->members[$index] ?? null,
                'uploaded_by' => Auth::id(),
            ]);
        }

        return back()->with('success', count($request->photos) . ' photo(s) uploaded successfully.');
    }

    public function uploadPhotosGeneral(Request $request)
    {
        $request->validate([
            'photos' => 'required|array',
            'photos.*' => 'image|max:5120', // 5MB max
            'gallery_id' => 'nullable|exists:galleries,id',
        ]);

        $galleryId = $request->gallery_id;

        if (!$galleryId) {
            $defaultTitle = 'Picha za Jumla';
            $gallery = Gallery::firstOrCreate(
                ['title' => $defaultTitle],
                [
                    'description' => 'Picha zilizopakiwa bila albamu maalum.',
                    'created_by' => Auth::id() ?? 1,
                ]
            );
            $galleryId = $gallery->id;
        }

        foreach ($request->file('photos') as $index => $photo) {
            $result = $this->imageService->uploadProfilePhoto($photo, 'galleries');
            
            GalleryPhoto::create([
                'gallery_id' => $galleryId,
                'photo_path' => $result['path'],
                'caption' => $request->captions[$index] ?? null,
                'member_id' => $request->members[$index] ?? null,
                'uploaded_by' => Auth::id(),
            ]);
        }

        return response()->json(['success' => true]);
    }

    public function deletePhoto($id)
    {
        $photo = GalleryPhoto::findOrFail($id);
        $this->imageService->deleteImage($photo->photo_path);
        $photo->delete();

        return back()->with('success', 'Photo deleted successfully.');
    }

    public function destroy(Gallery $gallery)
    {
        // First delete all physical photos
        $photos = $gallery->photos;
        foreach ($photos as $photo) {
            $this->imageService->deleteImage($photo->photo_path);
        }
        
        // Then delete the gallery (database cascading should handle the gallery_photos rows, but let's be safe)
        $gallery->photos()->delete();
        $gallery->delete();

        return redirect()->route('galleries.index')->with('success', 'Album imefutwa kikamilifu.');
    }
}
