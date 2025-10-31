<?php

namespace App\Policies;

use App\Models\User;
use App\Models\CarritoItem;
use Illuminate\Auth\Access\Response;

class CarritoItemPolicy
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
    public function view(User $user, CarritoItem $carritoItem): bool
    {
        return $carritoItem->usuario_id === $user->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, CarritoItem $carritoItem): bool
    {
        return $carritoItem->usuario_id === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, CarritoItem $carritoItem): bool
    {
        return $carritoItem->usuario_id === $user->id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, CarritoItem $carritoItem): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, CarritoItem $carritoItem): bool
    {
        return false;
    }
}