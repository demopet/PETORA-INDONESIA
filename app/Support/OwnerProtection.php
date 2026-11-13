<?php

declare(strict_types=1);

namespace App\Support;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class OwnerProtection
{
    public const ROLE = 'Owner';

    public static function activeOwnerCount(): int
    {
        $registrar = app(PermissionRegistrar::class);
        $originalTeam = $registrar->getPermissionsTeamId();

        try {
            $registrar->setPermissionsTeamId(null);
            $registrar->forgetCachedPermissions();

            $role = Role::query()->where('name', self::ROLE)->where('guard_name', 'web')->first();
            if (! $role) {
                return 0;
            }

            $tableNames = config('permission.table_names');
            $columnNames = config('permission.column_names');
            $modelKey = $columnNames['model_morph_key'] ?? 'model_id';

            return (int) User::query()
                ->where('is_active', true)
                ->whereIn('id', function ($query) use ($tableNames, $modelKey, $role): void {
                    $query->from($tableNames['model_has_roles'])
                        ->select($modelKey)
                        ->where('role_id', $role->id)
                        ->where('model_type', User::class);
                })
                ->distinct()
                ->count('id');
        } finally {
            $registrar->setPermissionsTeamId($originalTeam);
            $registrar->forgetCachedPermissions();
        }
    }

    public static function isOwner(User $user): bool
    {
        $registrar = app(PermissionRegistrar::class);
        $originalTeam = $registrar->getPermissionsTeamId();

        try {
            $registrar->setPermissionsTeamId(null);
            $registrar->forgetCachedPermissions();

            $role = Role::query()->where('name', self::ROLE)->where('guard_name', 'web')->first();
            if (! $role) {
                return false;
            }

            $tableNames = config('permission.table_names');
            $columnNames = config('permission.column_names');
            $modelKey = $columnNames['model_morph_key'] ?? 'model_id';

            return DB::table($tableNames['model_has_roles'])
                ->where('role_id', $role->id)
                ->where('model_type', User::class)
                ->where($modelKey, $user->getKey())
                ->exists();
        } finally {
            $registrar->setPermissionsTeamId($originalTeam);
            $registrar->forgetCachedPermissions();
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
