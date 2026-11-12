<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Branch;
use App\Models\User;

class BranchPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('platform.branches.view');
    }

    public function view(User $user, Branch $model): bool
    {
        return $user->can('platform.branches.view');
    }

    public function create(User $user): bool
    {
        return $user->can('platform.branches.manage');
    }

    public function update(User $user, Branch $model): bool
    {
        return $user->can('platform.branches.manage');
    }

    public function delete(User $user, Branch $model): bool
    {
        return $user->can('platform.branches.manage');
    }
}
