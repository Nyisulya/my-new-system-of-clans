<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class ImageService
{
    protected int $maxWidth = 800;
    protected int $maxHeight = 800;
    protected int $thumbnailSize = 150;

    /**
     * Upload and process a profile photo
     * 
     * @param UploadedFile $file
     * @param string $directory
     * @return array ['path' => string, 'thumbnail' => string]
     */
    public function uploadProfilePhoto(UploadedFile $file, string $directory = 'profiles'): array
    {
        // Validate image
        $this->validateImage($file);

        // Generate unique filename
        $filename = $this->generateFilename($file);
        $thumbnailFilename = 'thumb_' . $filename;

        // Create directories if they don't exist
        $fullPath = $directory . '/' . $filename;
        $thumbnailPath = $directory . '/thumbnails/' . $thumbnailFilename;

        // Resize and save main image
        $image = getimagesize($file->getRealPath());
        
        if ($image[0] > $this->maxWidth || $image[1] > $this->maxHeight) {
            $this->resizeAndSave($file, $fullPath, $this->maxWidth, $this->maxHeight);
        } else {
            Storage::disk('public')->put($fullPath, file_get_contents($file->getRealPath()));
        }

        // Create thumbnail
        $this->createThumbnail($file, $thumbnailPath, $this->thumbnailSize);

        return [
            'path' => $fullPath,
            'thumbnail' => $thumbnailPath,
            'url' => Storage::disk('public')->url($fullPath),
            'thumbnail_url' => Storage::disk('public')->url($thumbnailPath),
        ];
    }

    /**
     * Resize and save image
     */
    protected function resizeAndSave(UploadedFile $file, string $path, int $maxWidth, int $maxHeight): void
    {
        $image = imagecreatefromstring(file_get_contents($file->getRealPath()));
        
        if ($image === false) {
            throw new \Exception('Failed to create image from file');
        }

        $originalWidth = imagesx($image);
        $originalHeight = imagesy($image);

        // Calculate new dimensions maintaining aspect ratio
        $ratio = min($maxWidth / $originalWidth, $maxHeight / $originalHeight);
        $newWidth = (int) ($originalWidth * $ratio);
        $newHeight = (int) ($originalHeight * $ratio);

        // Create new image
        $resized = imagecreatetruecolor($newWidth, $newHeight);
        
        // Preserve transparency for PNG
        if ($file->getClientOriginalExtension() === 'png') {
            imagealphablending($resized, false);
            imagesavealpha($resized, true);
        }

        imagecopyresampled($resized, $image, 0, 0, 0, 0, $newWidth, $newHeight, $originalWidth, $originalHeight);

        // Save to storage
        ob_start();
        
        switch ($file->getClientOriginalExtension()) {
            case 'png':
                imagepng($resized, null, 9);
                break;
            case 'gif':
                imagegif($resized);
                break;
            default:
                imagejpeg($resized, null, 90);
        }
        
        $imageData = ob_get_clean();
        
        Storage::disk('public')->put($path, $imageData);

        imagedestroy($image);
        imagedestroy($resized);
    }

    /**
     * Create thumbnail
     */
    protected function createThumbnail(UploadedFile $file, string $path, int $size): void
    {
        $image = imagecreatefromstring(file_get_contents($file->getRealPath()));
        
        if ($image === false) {
            return;
        }

        $originalWidth = imagesx($image);
        $originalHeight = imagesy($image);

        // Calculate crop dimensions (square crop from center)
        $cropSize = min($originalWidth, $originalHeight);
        $cropX = ($originalWidth - $cropSize) / 2;
        $cropY = ($originalHeight - $cropSize) / 2;

        // Create thumbnail
        $thumbnail = imagecreatetruecolor($size, $size);
        
        if ($file->getClientOriginalExtension() === 'png') {
            imagealphablending($thumbnail, false);
            imagesavealpha($thumbnail, true);
        }

        $cropped = imagecrop($image, ['x' => $cropX, 'y' => $cropY, 'width' => $cropSize, 'height' => $cropSize]);
        
        if ($cropped !== false) {
            imagecopyresampled($thumbnail, $cropped, 0, 0, 0, 0, $size, $size, $cropSize, $cropSize);
            imagedestroy($cropped);
        }

        // Save thumbnail
        ob_start();
        imagejpeg($thumbnail, null, 85);
        $thumbnailData = ob_get_clean();
        
        Storage::disk('public')->put($path, $thumbnailData);

        imagedestroy($image);
        imagedestroy($thumbnail);
    }

    /**
     * Validate uploaded image
     */
    protected function validateImage(UploadedFile $file): void
    {
        $allowedMimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $maxSize = 2 * 1024 * 1024; // 2MB

        if (!in_array($file->getMimeType(), $allowedMimes)) {
            throw new \Exception('Invalid file type. Only JPEG, PNG, GIF, and WebP images are allowed.');
        }

        if ($file->getSize() > $maxSize) {
            throw new \Exception('File size exceeds 2MB limit.');
        }
    }

    /**
     * Generate unique filename
     */
    protected function generateFilename(UploadedFile $file): string
    {
        $extension = $file->getClientOriginalExtension();
        return Str::uuid() . '.' . $extension;
    }

    /**
     * Delete image and its thumbnail
     * 
     * @param string $path
     * @return bool
     */
    public function deleteImage(string $path): bool
    {
        $deleted = Storage::disk('public')->delete($path);

        // Try to delete thumbnail
        $thumbnailPath = dirname($path) . '/thumbnails/thumb_' . basename($path);
        Storage::disk('public')->delete($thumbnailPath);

        return $deleted;
    }

    /**
     * Get image dimensions
     * 
     * @param string $path
     * @return array|null
     */
    public function getImageDimensions(string $path): ?array
    {
        $fullPath = Storage::disk('public')->path($path);
        
        if (!file_exists($fullPath)) {
            return null;
        }

        $dimensions = getimagesize($fullPath);
        
        if (!$dimensions) {
            return null;
        }

        return [
            'width' => $dimensions[0],
            'height' => $dimensions[1],
            'mime' => $dimensions['mime'],
        ];
    }
}
