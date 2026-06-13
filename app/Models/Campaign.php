<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Campaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'target_amount',
        'start_date',
        'end_date',
        'status',
        'created_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'target_amount' => 'decimal:2',
    ];

    public function contributions(): HasMany
    {
        return $this->hasMany(Contribution::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getRaisedAmountAttribute()
    {
        return $this->contributions()->sum('amount');
    }

    public function getProgressPercentageAttribute()
    {
        if ($this->target_amount <= 0) return 0;
        return min(100, round(($this->raised_amount / $this->target_amount) * 100));
    }
}
