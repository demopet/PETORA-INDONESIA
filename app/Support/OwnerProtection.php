<?php

declare(strict_types=1);

namespace App\Support;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class OwnerProtection
{
    public const ROLE = 'Owner';

    public static function activeOwnerCount(): int
    {
        $registrar = app(PermissionRegistrar::class);
        $originalTeam = $registrar->getPermissionsTeamId();
        $registrar->setPermissionsTeamId(null);

        try {
            $role = Role::query()->where('name', self::ROLE)->where('guard_name', 'web')->first();
            if (! $role) {
                return 0;
            }

            return User::query()
                ->where('is_active', true)
                ->whereHas('roles', fn ($q) => $q->where('roles.id', $role->id))
                ->count();
        } finally {
            $registrar->setPermissionsTeamId($originalTeam);
        }
    }

    public static function isOwner(User $user): bool
    {
        $registrar = app(PermissionRegistrar::class);
        $originalTeam = $registrar->getPermissionsTeamId();
        $registrar->setPermissionsTeamId(null);

        try {
            return $user->hasRole(self::ROLE);
        } finally {
            $registrar->setPermissionsTeamId($originalTeam);
        }
    }

    public static function canDeactivate(User $user): bool
    {
        if (! $user->is_active) {
            return true;
        }

        if (! self::isOwner($user)) {
            return true;
        }

        return self::activeOwnerCount() > 1;
    }

    public static function canRemoveOwnerRole(User $user): bool
    {
        if (! self::isOwner($user)) {
            return true;
        }

        return self::activeOwnerCount() > 1;
    }

    public static function canDelete(User $user): bool
    {
        if (! self::isOwner($user)) {
            return true;
        }

        return self::activeOwnerCount() > 1;
    }
}
