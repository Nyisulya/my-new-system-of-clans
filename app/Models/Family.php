<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Family extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'clan_id',
        'name',
        'surname',
        'description',
        'origin_place',
        'established_date',
        'is_active',
    ];

    protected $casts = [
        'established_date' => 'date',
        'is_active' => 'boolean',
    ];

    /**
     * Get the clan this family belongs to
     */
    public function clan(): BelongsTo
    {
        return $this->belongsTo(Clan::class);
    }

    /**
     * Get all branches in this family
     */
    public function branches(): HasMany
    {
        return $this->hasMany(Branch::class);
    }

    /**
     * Get all members in this family
     */
    public function members(): HasMany
    {
        return $this->hasMany(Member::class);
    }

    /**
     * Scope for active families
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for searching families
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('surname', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%");
        });
    }

    /**
     * Get total member count
     */
    public function getMemberCountAttribute(): int
    {
        return $this->members()->count();
    }
}
