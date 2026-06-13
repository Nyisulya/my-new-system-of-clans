<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Carbon\Carbon;

class Member extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'clan_id',
        'family_id',
        'branch_id',
        'first_name',
        'middle_name',
        'last_name',
        'maiden_name',
        'gender',
        'date_of_birth',
        'place_of_birth',
        'father_id',
        'mother_id',
        'generation_number',
        'status',
        'date_of_death',
        'place_of_death',
        'email',
        'phone',
        'address',
        'street',
        'city',
        'region',
        'district',
        'country',
        'profile_photo',
        'biography',
        'occupation',
        'notes',
        'created_by',
        'updated_by',
        'birth_place',
        'birth_lat',
        'birth_lng',
        'current_location',
        'current_lat',
        'current_lng',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'date_of_death' => 'date',
        'generation_number' => 'integer',
    ];

    protected $appends = ['full_name', 'age'];

    /**
     * Boot method for model events
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-calculate generation number before saving
        static::saving(function ($member) {
            if (!$member->generation_number || $member->isDirty(['father_id', 'mother_id'])) {
                $member->generation_number = $member->calculateGeneration();
            }
        });
    }

    /**
     * Relationships
     */
    public function clan(): BelongsTo
    {
        return $this->belongsTo(Clan::class);
    }

    public function family(): BelongsTo
    {
        return $this->belongsTo(Family::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function father(): BelongsTo
    {
        return $this->belongsTo(Member::class, 'father_id');
    }

    public function mother(): BelongsTo
    {
        return $this->belongsTo(Member::class, 'mother_id');
    }

    /**
     * Get all marriages where this member is the husband
     */
    public function marriagesAsHusband(): HasMany
    {
        return $this->hasMany(Marriage::class, 'husband_id');
    }

    /**
     * Get all marriages where this member is the wife
     */
    public function marriagesAsWife(): HasMany
    {
        return $this->hasMany(Marriage::class, 'wife_id');
    }

    /**
     * Get all marriages (combined)
     */
    public function marriages()
    {
        if ($this->gender === 'male') {
            return $this->marriagesAsHusband();
        } elseif ($this->gender === 'female') {
            return $this->marriagesAsWife();
        }
        return $this->marriagesAsHusband()->union($this->marriagesAsWife()->getQuery());
    }

    /**
     * Get all wives (for male members)
     */
    public function wives()
    {
        return $this->hasManyThrough(
            Member::class,
            Marriage::class,
            'husband_id',
            'id',
            'id',
            'wife_id'
        )->where('marriages.status', 'active');
    }

    /**
     * Get husband (for female members)
     */
    public function husband(): ?Member
    {
        $marriage = $this->marriagesAsWife()->active()->first();
        return $marriage ? $marriage->husband : null;
    }

    /**
     * Get all spouses (works for both genders)
     */
    public function spouses()
    {
        if ($this->gender === 'male') {
            return $this->wives()->get();
        } elseif ($this->gender === 'female') {
            $husband = $this->husband();
            return $husband ? collect([$husband]) : collect();
        }
        return collect();
    }

    /**
     * Get all children (where this member is the father)
     */
    public function childrenAsFather(): HasMany
    {
        return $this->hasMany(Member::class, 'father_id');
    }

    /**
     * Get all children (where this member is the mother)
     */
    public function childrenAsMother(): HasMany
    {
        return $this->hasMany(Member::class, 'mother_id');
    }

    /**
     * Get all children (combined)
     */
    public function children()
    {
        if ($this->gender === 'male') {
            return $this->childrenAsFather();
        } elseif ($this->gender === 'female') {
            return $this->childrenAsMother();
        }
        
        // If gender is 'other' or not set, merge both
        return $this->childrenAsFather()->union($this->childrenAsMother()->getQuery());
    }

    /**
     * Get all descendants recursively
     */
    public function descendants()
    {
        $descendants = collect();
        
        foreach ($this->children()->get() as $child) {
            $descendants->push($child);
            $descendants = $descendants->merge($child->descendants());
        }
        
        return $descendants;
    }

    /**
     * Get all ancestors recursively
     */
    public function ancestors()
    {
        $ancestors = collect();
        
        if ($this->father) {
            $ancestors->push($this->father);
            $ancestors = $ancestors->merge($this->father->ancestors());
        }
        
        if ($this->mother) {
            $ancestors->push($this->mother);
            $ancestors = $ancestors->merge($this->mother->ancestors());
        }
        
        return $ancestors->unique('id');
    }

    /**
     * Get media files
     */
    public function media(): MorphMany
    {
        return $this->morphMany(Media::class, 'mediable');
    }

    /**
     * Get user who created this member
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get user who last updated this member
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get the user account associated with this member
     */
    public function user()
    {
        return $this->hasOne(User::class);
    }

    /**
     * Scopes
     */
    public function scopeAlive($query)
    {
        return $query->where('status', 'alive');
    }

    public function scopeDeceased($query)
    {
        return $query->where('status', 'deceased');
    }

    public function scopeByGender($query, $gender)
    {
        return $query->where('gender', $gender);
    }

    public function scopeByGeneration($query, $generation)
    {
        return $query->where('generation_number', $generation);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('first_name', 'like', "%{$search}%")
              ->orWhere('middle_name', 'like', "%{$search}%")
              ->orWhere('last_name', 'like', "%{$search}%")
              ->orWhere('maiden_name', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%")
              ->orWhere('phone', 'like', "%{$search}%");
        });
    }

    /**
     * Accessors
     */
    public function getFullNameAttribute(): string
    {
        $name = $this->first_name;
        
        if ($this->middle_name) {
            $name .= ' ' . $this->middle_name;
        }
        
        $name .= ' ' . $this->last_name;
        
        if ($this->maiden_name) {
            $name .= ' (née ' . $this->maiden_name . ')';
        }
        
        return $name;
    }

    public function getAgeAttribute(): ?int
    {
        if (!$this->date_of_birth) {
            return null;
        }

        $endDate = $this->status === 'deceased' && $this->date_of_death 
            ? $this->date_of_death 
            : Carbon::now();

        return $this->date_of_birth->diffInYears($endDate);
    }

    public function getProfilePhotoUrlAttribute(): ?string
    {
        if ($this->profile_photo) {
            return asset('storage/' . $this->profile_photo);
        }
        
        // Generate default avatar using UI Avatars service
        $name = urlencode($this->first_name . ' ' . $this->last_name);
        $background = $this->gender === 'male' ? '3B82F6' : ($this->gender === 'female' ? 'EC4899' : '8B5CF6');
        
        return "https://ui-avatars.com/api/?name={$name}&background={$background}&color=fff&size=200";
    }

    /**
     * Calculate generation number based on parents
     */
    public function calculateGeneration(): int
    {
        $fatherGeneration = 0;
        $motherGeneration = 0;

        if ($this->father_id) {
            $father = Member::find($this->father_id);
            $fatherGeneration = $father ? $father->generation_number : 0;
        }

        if ($this->mother_id) {
            $mother = Member::find($this->mother_id);
            $motherGeneration = $mother ? $mother->generation_number : 0;
        }

        // If both parents exist, take the max and add 1
        if ($fatherGeneration > 0 || $motherGeneration > 0) {
            return max($fatherGeneration, $motherGeneration) + 1;
        }

        // Root member (no parents)
        return 1;
    }

    /**
     * Check for potential duplicates
     */
    public static function findPotentialDuplicates($firstName, $lastName, $dateOfBirth)
    {
        return self::where('first_name', 'like', "%{$firstName}%")
                   ->where('last_name', 'like', "%{$lastName}%")
                   ->where('date_of_birth', $dateOfBirth)
                   ->get();
    }
}
