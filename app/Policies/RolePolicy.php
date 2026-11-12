<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;
use Spatie\Permission\Models\Role;

class RolePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('platform.roles.view');
    }

    public function view(User $user, Role $model): bool
    {
        return $user->can('platform.roles.view');
    }

    public function create(User $user): bool
    {
        return $user->can('platform.roles.manage');
    }

    public function update(User $user, Role $model): bool
    {
        if ($model->name === 'Owner') {
            return $user->hasRole('Owner') || $user->can('platform.roles.manage');
        }

        return $user->can('platform.roles.manage');
    }

    public function delete(User $user, Role $model): bool
    {
        if ($model->name === 'Owner') {
            return false;
        }

        return $user->can('platform.roles.manage');
    }
}
