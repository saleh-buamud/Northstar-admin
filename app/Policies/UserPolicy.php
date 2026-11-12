<?php

namespace App\Policies;

use App\Models\Role;
use App\Models\User;

class UserPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('users.view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model): bool
    {
        return $user->hasPermissionTo('users.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('users.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $target): bool
    {
        return $user->hasPermissionTo('users.edit') && ! $target->hasRole('super-admin') && ! $user->is($target);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $target): bool
    {
        return $user->hasPermissionTo('users.delete') && ! $target->hasRole('super-admin') && ! $user->is($target);
    }

    public function assignRole(User $user, Role $role): bool
    {
        if ($role->slug === 'super-admin') {
            return false;
        }

        return $user->hasRole('super-admin')
            || $role->permissions->every(fn ($permission): bool => $user->hasPermissionTo($permission->name));
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, User $model): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, User $model): bool
    {
        return false;
    }
}
