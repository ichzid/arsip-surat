<?php

namespace App\Policies;

use App\Models\OutgoingLetter;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class OutgoingLetterPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, OutgoingLetter $outgoingLetter): bool
    {
        return OutgoingLetter::visibleTo($user)->where('id', $outgoingLetter->id)->exists();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // All users with a division can create outgoing letters
        return $user->division_id !== null;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, OutgoingLetter $outgoingLetter): bool
    {
        return $user->hasRole(['Admin']) || $user->id === $outgoingLetter->created_by;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, OutgoingLetter $outgoingLetter): bool
    {
        return $user->hasRole(['Admin']) || $user->id === $outgoingLetter->created_by;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, OutgoingLetter $outgoingLetter): bool
    {
        return $user->hasRole(['Admin']);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, OutgoingLetter $outgoingLetter): bool
    {
        return $user->hasRole(['Admin']);
    }
}
