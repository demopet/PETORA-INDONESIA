<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;
use App\Support\OwnerProtection;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('platform.users.view');
    }

    public function view(User $user, User $model): bool
    {
        return $user->can('platform.users.view');
    }

    public function create(User $user): bool
    {
        return $user->can('platform.users.manage');
    }

    public function update(User $user, User $model): bool
    {
        return $user->can('platform.users.manage');
    }

    public function delete(User $user, User $model): bool
    {
        if (! $user->can('platform.users.manage')) {
            return false;
        }

        if ($user->is($model)) {
            return false;
        }

        return OwnerProtection::canDelete($model);
    }
}
