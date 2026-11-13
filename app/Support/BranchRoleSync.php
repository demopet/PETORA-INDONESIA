<?php

declare(strict_types=1);

namespace App\Support;

use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class BranchRoleSync
{
    /**
     * @return array<int, list<string>>
     */
    public static function rolesByBranch(User $user): array
    {
        $table = config('permission.table_names.model_has_roles');
        $roleTable = config('permission.table_names.roles');
        $teamKey = config('permission.column_names.team_foreign_key', 'team_id');
        $modelKey = config('permission.column_names.model_morph_key', 'model_id');

        $rows = DB::table($table)
            ->join($roleTable, "{$roleTable}.id", '=', "{$table}.role_id")
            ->where("{$table}.model_type", $user->getMorphClass())
            ->where("{$table}.{$modelKey}", $user->getKey())
            ->select("{$table}.{$teamKey} as team_id", "{$roleTable}.name as role_name")
            ->get();

        $result = [];
        foreach ($rows as $row) {
            $teamId = (int) $row->team_id;
            $result[$teamId] ??= [];
            $result[$teamId][] = (string) $row->role_name;
        }

        foreach ($result as $teamId => $names) {
            $result[$teamId] = array_values(array_unique($names));
            sort($result[$teamId]);
        }

        return $result;
    }

    /**
     * @return list<string>
     */
    public static function roleNamesForBranch(User $user, ?int $branchId): array
    {
        if ($branchId === null) {
            return [];
        }

        $registrar = app(PermissionRegistrar::class);
        $original = $registrar->getPermissionsTeamId();

        try {
            $registrar->setPermissionsTeamId($branchId);
            $registrar->forgetCachedPermissions();
            $fresh = $user->fresh() ?? $user;

            return $fresh->getRoleNames()->values()->all();
        } finally {
            $registrar->setPermissionsTeamId($original);
            $registrar->forgetCachedPermissions();
        }
    }

    /**
     * @param  list<int|string>|Collection<int, int|string>  $roleNamesOrIds
     */
    public static function syncRolesForBranch(User $user, int $branchId, array|Collection $roleNamesOrIds): void
    {
        $registrar = app(PermissionRegistrar::class);
        $original = $registrar->getPermissionsTeamId();

        try {
            $registrar->setPermissionsTeamId($branchId);
            $registrar->forgetCachedPermissions();
            $fresh = $user->fresh() ?? $user;
            $fresh->syncRoles(collect($roleNamesOrIds)->values()->all());
        } finally {
            $registrar->setPermissionsTeamId($original);
            $registrar->forgetCachedPermissions();
        }
    }

    /**
     * Ensure user has at least their active/primary branch team for role operations.
     */
    public static function ensureTeamForRoleAssignment(User $user): ?int
    {
        $branchId = $user->active_branch_id ? (int) $user->active_branch_id : null;
        if ($branchId) {
            return $branchId;
        }

        $id = $user->branches()->where('is_active', true)->value('branches.id');

        return $id ? (int) $id : null;
    }
}
