<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Clan extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'founding_date',
        'origin_location',
        'coat_of_arms',
        'is_active',
        'is_spouse_clan',
    ];

    protected $casts = [
        'founding_date' => 'date',
        'is_active' => 'boolean',
        'is_spouse_clan' => 'boolean',
    ];

    /**
     * Get all families belonging to this clan
     */
    public function families(): HasMany
    {
        return $this->hasMany(Family::class);
    }

    /**
     * Get all members in this clan
     */
    public function members(): HasMany
    {
        return $this->hasMany(Member::class);
    }

    /**
     * Scope for active clans
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get total member count
     */
    public function getMemberCountAttribute(): int
    {
        return $this->members()->count();
    }

    /**
     * Get active member count
     */
    public function getActiveMemberCountAttribute(): int
    {
        return $this->members()->where('status', 'alive')->count();
    }
}
