<?php

namespace App\Policies;

use App\Models\UserTask;
use App\Models\User;

class UserTaskPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Superadmin dan admin bisa akses
        return in_array($user->role, [User::ROLE_SUPERADMIN, User::ROLE_ADMIN]);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, UserTask $userTask): bool
    {
        // Superadmin bisa lihat semua
        if ($user->role === User::ROLE_SUPERADMIN) {
            return true;
        }

        // Admin biasa hanya bisa lihat user task dari task yang dia buat
        return $user->role === User::ROLE_ADMIN &&
            $userTask->task &&
            $userTask->task->created_by === $user->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return in_array($user->role, [User::ROLE_SUPERADMIN, User::ROLE_ADMIN]);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, UserTask $userTask): bool
    {
        // Superadmin bisa update semua
        if ($user->role === User::ROLE_SUPERADMIN) {
            return true;
        }

        // Admin biasa hanya bisa update user task dari task yang dia buat
        return $user->role === User::ROLE_ADMIN &&
            $userTask->task &&
            $userTask->task->created_by === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, UserTask $userTask): bool
    {
        // Superadmin bisa hapus semua
        if ($user->role === User::ROLE_SUPERADMIN) {
            return true;
        }

        // Admin biasa hanya bisa hapus user task dari task yang dia buat
        return $user->role === User::ROLE_ADMIN &&
            $userTask->task &&
            $userTask->task->created_by === $user->id;
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->role === User::ROLE_SUPERADMIN;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, UserTask $userTask): bool
    {
        return $user->role === User::ROLE_SUPERADMIN;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, UserTask $userTask): bool
    {
        return $user->role === User::ROLE_SUPERADMIN;
    }
}
