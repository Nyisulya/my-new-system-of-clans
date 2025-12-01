<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Media extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'mediable_id',
        'mediable_type',
        'file_name',
        'file_path',
        'file_type',
        'mime_type',
        'file_size',
        'disk',
        'description',
        'metadata',
        'order',
        'uploaded_by',
    ];

    protected $casts = [
        'metadata' => 'array',
        'file_size' => 'integer',
        'order' => 'integer',
    ];

    /**
     * Get the parent mediable model
     */
    public function mediable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the user who uploaded this media
     */
    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Get the full URL of the media file
     */
    public function getUrlAttribute(): string
    {
        return asset('storage/' . $this->file_path);
    }

    /**
     * Get human-readable file size
     */
    public function getHumanFileSizeAttribute(): string
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Scope for images only
     */
    public function scopeImages($query)
    {
        return $query->where('file_type', 'image');
    }

    /**
     * Scope for ordering media
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }
}
