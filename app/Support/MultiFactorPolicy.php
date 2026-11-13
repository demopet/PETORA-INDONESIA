<?php

declare(strict_types=1);

namespace App\Support;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\PermissionRegistrar;

class MultiFactorPolicy
{
    public const SETTING_KEY = 'security.mfa_required_roles';

    /**
     * @return list<string>
     */
    public static function requiredRoles(): array
    {
        $value = Settings::get(self::SETTING_KEY, ['Owner', 'Super Admin']);

        if (! is_array($value)) {
            return ['Owner', 'Super Admin'];
        }

        $roles = array_values(array_unique(array_filter(array_map('strval', $value))));
        sort($roles);

        return $roles;
    }

    /**
     * @param  list<string>  $roles
     */
    public static function setRequiredRoles(array $roles): void
    {
        $roles = array_values(array_unique(array_filter(array_map('strval', $roles))));
        sort($roles);
        Settings::set(self::SETTING_KEY, $roles, 'json', 'security');
    }

    public static function isRequiredFor(User $user): bool
    {
        $required = self::requiredRoles();
        if ($required === []) {
            return false;
        }

        $registrar = app(PermissionRegistrar::class);
        $original = $registrar->getPermissionsTeamId();

        try {
            // Check across teams: if user has any required role in any branch/team, MFA is required.
            $table = config('permission.table_names.model_has_roles');
            $roleTable = config('permission.table_names.roles');
            $modelKey = config('permission.column_names.model_morph_key', 'model_id');

            return DB::table($table)
                ->join($roleTable, "{$roleTable}.id", '=', "{$table}.role_id")
                ->where("{$table}.model_type", $user->getMorphClass())
                ->where("{$table}.{$modelKey}", $user->getKey())
                ->whereIn("{$roleTable}.name", $required)
                ->exists();
        } finally {
            $registrar->setPermissionsTeamId($original);
        }
    }
}
