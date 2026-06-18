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
        // Admins can create members, and regular users who are not linked can create their own profile
        return $user->isAdmin() || $user->member_id === null;
    }

    /**
     * Determine whether the user can view the model's dashboard.
     * All authenticated members can view any dashboard (read-only).
     */
    public function viewDashboard(User $user, Member $member): bool
    {
        return true; // All authenticated users can view any member's dashboard
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
