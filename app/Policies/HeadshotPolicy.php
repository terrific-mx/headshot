<?php

namespace App\Policies;

use App\Models\Headshot;
use App\Models\User;

class HeadshotPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Headshot $headshot): bool
    {
        return $headshot->user->is($user);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Headshot $headshot): bool
    {
        return $headshot->user->is($user);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Headshot $headshot): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Headshot $headshot): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Headshot $headshot): bool
    {
        return false;
    }

    public function trainModel(User $user, Headshot $headshot)
    {
        return $headshot->user->is($user)
            && $headshot->selfies()->count() >= 15
            && $headshot->training_status === 'pending';
    }

    public function updatePersonalDetails(User $user, Headshot $headshot)
    {
        return $headshot->user->is($user) && $headshot->isTrainingPending();
    }

    public function createStyle(User $user, Headshot $headshot)
    {
        return $headshot->user->is($user);
    }
}
