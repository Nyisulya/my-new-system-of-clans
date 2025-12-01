<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
        'last_login_at',
        'locale',
        'email_notifications',
        'birthday_reminders',
        'anniversary_reminders',
        'death_anniversary_reminders',
        'member_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'last_login_at' => 'datetime',
        ];
    }

    /**
     * Get members created by this user
     */
    public function createdMembers(): HasMany
    {
        return $this->hasMany(Member::class, 'created_by');
    }

    /**
     * Get members updated by this user
     */
    public function updatedMembers(): HasMany
    {
        return $this->hasMany(Member::class, 'updated_by');
    }

    /**
     * Get the member profile associated with this user
     */
    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    /**
     * Check if user is an admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is an editor
     */
    public function isEditor(): bool
    {
        return $this->role === 'editor';
    }

    /**
     * Check if user is a viewer
     */
    public function isViewer(): bool
    {
        return $this->role === 'viewer';
    }

    /**
     * Check if user can create/update members
     */
    public function canEditMembers(): bool
    {
        return in_array($this->role, ['admin', 'editor']);
    }

    /**
     * Check if user can delete members
     */
    public function canDeleteMembers(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Scope for active users
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for filtering by role
     */
    public function scopeWithRole($query, $role)
    {
        return $query->where('role', $role);
    }

    /**
     * Check if user is a regular member (self-registered)
     */
    public function isMember(): bool
    {
        return $this->role === 'member';
    }
}
