<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['admin', 'owner']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return in_array($user->role, ['admin', 'owner']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $model): bool
    {
        // Garante que o usuário só possa editar usuários da mesma empresa
        if ($user->enterprise_id !== $model->enterprise_id) {
            return false;
        }

        if ($model->role === 'dev') {
            return false;
        }

        if (!in_array($user->role, ['admin', 'owner'])) {
            return false;
        }

        if ($user->role === 'admin' && $model->role === 'owner') {
            return false;
        }

        return true;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model): bool
    {
        // Garante que o usuário só possa deletar usuários da mesma empresa
        if ($user->enterprise_id !== $model->enterprise_id) {
            return false;
        }

        if ($model->role === 'dev') {
            return false;
        }

        if (!in_array($user->role, ['admin', 'owner'])) {
            return false;
        }

        if ($user->role === 'admin' && $model->role === 'owner') {
            return false;
        }

        return true;
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
