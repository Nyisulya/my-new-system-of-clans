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
        $galleries = Gallery::withCount('photos')
            ->orderBy('created_at', 'desc')
            ->get();
        return view('galleries.index', compact('galleries'));
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

    public function deletePhoto($id)
    {
        $photo = GalleryPhoto::findOrFail($id);
        $this->imageService->deleteImage($photo->photo_path);
        $photo->delete();

        return back()->with('success', 'Photo deleted successfully.');
    }
}
