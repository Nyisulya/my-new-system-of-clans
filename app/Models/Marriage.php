<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Marriage extends Model
{
    use HasFactory;

    protected $fillable = [
        'husband_id',
        'wife_id',
        'marriage_date',
        'divorce_date',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'marriage_date' => 'date',
        'divorce_date' => 'date',
    ];

    /**
     * Get the husband in this marriage.
     */
    public function husband(): BelongsTo
    {
        return $this->belongsTo(Member::class, 'husband_id');
    }

    /**
     * Get the wife in this marriage.
     */
    public function wife(): BelongsTo
    {
        return $this->belongsTo(Member::class, 'wife_id');
    }

    /**
     * Get the user who created this marriage.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated this marriage.
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scope to get only active marriages.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope to get only divorced marriages.
     */
    public function scopeDivorced($query)
    {
        return $query->where('status', 'divorced');
    }
}
