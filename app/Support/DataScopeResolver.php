<?php

declare(strict_types=1);

namespace App\Support;

use App\Enums\DataScope;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DataScopeResolver
{
    /**
     * Resolve the effective (broadest) data scope for a user in the current
     * permission team. Falls back to `branch` when the user holds no role.
     */
    public static function effective(User $user): DataScope
    {
        $table = config('permission.table_names.model_has_roles');
        $roleTable = config('permission.table_names.roles');
        $teamKey = config('permission.column_names.team_foreign_key');
        $modelKey = config('permission.column_names.model_morph_key');

        $query = DB::table($table)
            ->join($roleTable, "{$roleTable}.id", '=', "{$table}.role_id")
            ->where("{$table}.model_type", $user->getMorphClass())
            ->where("{$table}.{$modelKey}", $user->getKey());

        $teamId = BranchContext::permissionTeamId();
        if ($teamId !== null) {
            $query->where("{$table}.{$teamKey}", $teamId);
        }

        $effective = null;

        foreach ($query->pluck("{$roleTable}.data_scope") as $value) {
            $scope = DataScope::tryFrom((string) $value) ?? DataScope::Branch;
            if ($effective === null || $scope->isBroaderThan($effective)) {
                $effective = $scope;
            }
        }

        return $effective ?? DataScope::Branch;
    }
}
