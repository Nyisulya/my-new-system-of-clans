<?php

namespace App\Policies;

use App\Models\Member;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class MemberPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true; // All authenticated users can view members
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Member $member): bool
    {
        return true; // All authenticated users can view members
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Only admins can create members for others
        // Regular members auto-create their own on registration
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Member $member): bool
    {
        // Admin can edit anyone
        if ($user->isAdmin()) {
            return true;
        }
        
        // Regular members can only edit their own profile
        return $user->member_id === $member->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Member $member): bool
    {
        // Admin can delete anyone
        if ($user->isAdmin()) {
            return true;
        }
        
        // Regular members can delete their own profile
        return $user->member_id === $member->id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Member $member): bool
    {
        return $user->isAdmin(); // Admin only
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Member $member): bool
    {
        return $user->isAdmin(); // Admin only
    }
}
