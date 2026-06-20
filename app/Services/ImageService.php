<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageService
{
    // Max dimensions for profile photos
    protected int $maxWidth     = 600;
    protected int $maxHeight    = 600;
    // JPEG quality (0-100). 75 gives great quality at ~50-100KB
    protected int $jpegQuality  = 75;

    /**
     * Upload a profile photo — compresses first, then uploads to Cloudinary
     * or falls back to local storage.
     */
    public function uploadProfilePhoto(UploadedFile $file, string $folder = 'clan-profiles'): array
    {
        // 1. Compress image in memory to small JPEG
        $compressedData = $this->compressImage($file);

        // 2. Upload compressed data
        if ($this->isCloudinaryConfigured()) {
            return $this->uploadCompressedToCloudinary($compressedData, $folder);
        }

        return $this->uploadCompressedToLocal($compressedData, 'profiles');
    }

    /**
     * Compress image: resize to max 600x600, convert to JPEG at 75% quality.
     * Returns raw JPEG binary string.
     */
    protected function compressImage(UploadedFile $file): string
    {
        $imageData = file_get_contents($file->getRealPath());
        $src = @imagecreatefromstring($imageData);

        if ($src === false) {
            throw new \Exception('Picha hii haiwezi kusomwa. Tafadhali jaribu picha nyingine.');
        }

        $originalWidth  = imagesx($src);
        $originalHeight = imagesy($src);

        // Calculate new dimensions keeping aspect ratio
        $ratio     = min($this->maxWidth / $originalWidth, $this->maxHeight / $originalHeight, 1.0);
        $newWidth  = (int) round($originalWidth * $ratio);
        $newHeight = (int) round($originalHeight * $ratio);

        // Create resized canvas
        $dst = imagecreatetruecolor($newWidth, $newHeight);

        // White background (for transparency in PNG/GIF)
        $white = imagecolorallocate($dst, 255, 255, 255);
        imagefilledrectangle($dst, 0, 0, $newWidth, $newHeight, $white);

        // Resample (high quality)
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $newWidth, $newHeight, $originalWidth, $originalHeight);

        // Capture as JPEG
        ob_start();
        imagejpeg($dst, null, $this->jpegQuality);
        $compressed = ob_get_clean();

        imagedestroy($src);
        imagedestroy($dst);

        \Log::info('ImageService: compressed photo', [
            'original_size' => strlen($imageData),
            'compressed_size' => strlen($compressed),
            'original_dims' => "{$originalWidth}x{$originalHeight}",
            'new_dims' => "{$newWidth}x{$newHeight}",
        ]);

        return $compressed;
    }

    /**
     * Upload compressed JPEG binary to Cloudinary
     */
    protected function uploadCompressedToCloudinary(string $jpegData, string $folder): array
    {
        // Write to a temp file so Cloudinary SDK can read it
        $tempPath = sys_get_temp_dir() . '/' . Str::uuid() . '.jpg';
        file_put_contents($tempPath, $jpegData);

        try {
            $cloudinary = new \Cloudinary\Cloudinary([
                'cloud' => [
                    'cloud_name' => config('cloudinary.cloud.cloud_name'),
                    'api_key'    => config('cloudinary.cloud.api_key'),
                    'api_secret' => config('cloudinary.cloud.api_secret'),
                ],
            ]);

            $result = $cloudinary->uploadApi()->upload($tempPath, [
                'folder'        => $folder,
                'resource_type' => 'image',
                // Cloudinary will also optimize on delivery via f_auto, q_auto in URL
            ]);

            return [
                'path' => $result['public_id'],
                'url'  => $result['secure_url'],
            ];
        } finally {
            // Always clean up temp file
            if (file_exists($tempPath)) {
                unlink($tempPath);
            }
        }
    }

    /**
     * Upload compressed JPEG binary to local storage (fallback)
     */
    protected function uploadCompressedToLocal(string $jpegData, string $directory): array
    {
        $filename = Str::uuid() . '.jpg';
        $fullPath = $directory . '/' . $filename;

        Storage::disk('public')->put($fullPath, $jpegData);

        return [
            'path' => $fullPath,
            'url'  => Storage::disk('public')->url($fullPath),
        ];
    }

    /**
     * Delete image from Cloudinary or local storage
     */
    public function deleteImage(string $path): bool
    {
        if ($this->isCloudinaryConfigured() && $this->isCloudinaryPublicId($path)) {
            try {
                $cloudinary = new \Cloudinary\Cloudinary([
                    'cloud' => [
                        'cloud_name' => config('cloudinary.cloud.cloud_name'),
                        'api_key'    => config('cloudinary.cloud.api_key'),
                        'api_secret' => config('cloudinary.cloud.api_secret'),
                    ],
                ]);
                $cloudinary->adminApi()->deleteAssets([$path]);
                return true;
            } catch (\Exception $e) {
                \Log::warning("Cloudinary delete failed for {$path}: " . $e->getMessage());
                return false;
            }
        }

        // Local storage fallback
        $deleted       = Storage::disk('public')->delete($path);
        $thumbnailPath = dirname($path) . '/thumbnails/thumb_' . basename($path);
        Storage::disk('public')->delete($thumbnailPath);
        return $deleted;
    }

    /**
     * Detect if a stored path is a Cloudinary public_id.
     * Cloudinary IDs have no file extension and use our folder prefixes.
     */
    protected function isCloudinaryPublicId(string $path): bool
    {
        return str_starts_with($path, 'clan-profiles/')
            || str_starts_with($path, 'galleries/')
            || (!str_contains($path, '.') && str_contains($path, '/'));
    }

    /**
     * Check if Cloudinary is configured via .env
     */
    protected function isCloudinaryConfigured(): bool
    {
        return !empty(config('cloudinary.cloud.cloud_name'))
            && !empty(config('cloudinary.cloud.api_key'))
            && !empty(config('cloudinary.cloud.api_secret'));
    }
}
