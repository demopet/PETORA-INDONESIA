<?php

declare(strict_types=1);

namespace App\Support;

use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

/**
 * Structured catalog of platform permissions, grouped by module and action.
 *
 * This is the single source of truth for which permissions exist. The seeder,
 * the role matrix, and tests build from it instead of hard-coding flat lists.
 */
final class PermissionCatalog
{
    /**
     * @return array<string, array{label: string, permissions: list<array{name: string, label: string, risk: bool}>}>
     */
    public static function modules(): array
    {
        return [
            'platform' => [
                'label' => 'Platform',
                'permissions' => [
                    ['name' => 'platform.settings.view', 'label' => 'Lihat pengaturan', 'risk' => false],
                    ['name' => 'platform.settings.manage', 'label' => 'Ubah pengaturan', 'risk' => true],
                    ['name' => 'platform.branches.view', 'label' => 'Lihat cabang', 'risk' => false],
                    ['name' => 'platform.branches.manage', 'label' => 'Ubah cabang', 'risk' => true],
                    ['name' => 'platform.branches.view_all', 'label' => 'Lihat semua cabang (mode gabungan)', 'risk' => true],
                    ['name' => 'platform.users.view', 'label' => 'Lihat pengguna', 'risk' => false],
                    ['name' => 'platform.users.manage', 'label' => 'Ubah pengguna & pengecualian izin', 'risk' => true],
                    ['name' => 'platform.roles.view', 'label' => 'Lihat peran', 'risk' => false],
                    ['name' => 'platform.roles.manage', 'label' => 'Ubah peran', 'risk' => true],
                    ['name' => 'platform.audit.view', 'label' => 'Lihat log audit', 'risk' => false],
                    ['name' => 'platform.health.view', 'label' => 'Lihat kesehatan sistem', 'risk' => false],
                    ['name' => 'platform.integrations.view', 'label' => 'Lihat integrasi', 'risk' => false],
                    ['name' => 'platform.integrations.manage', 'label' => 'Ubah integrasi', 'risk' => true],
                ],
            ],
        ];
    }

    /**
     * Flattened list of all permission names.
     *
     * @return list<string>
     */
    public static function names(): array
    {
        $names = [];

        foreach (self::modules() as $module) {
            foreach ($module['permissions'] as $permission) {
                $names[] = $permission['name'];
            }
        }

        return array_values(array_unique($names));
    }

    /**
     * Permissions considered high-risk (e.g. changing access, settings, money).
     *
     * @return list<string>
     */
    public static function highRiskNames(): array
    {
        $names = [];

        foreach (self::modules() as $module) {
            foreach ($module['permissions'] as $permission) {
                if ($permission['risk']) {
                    $names[] = $permission['name'];
                }
            }
        }

        return array_values(array_unique($names));
    }

    public static function isHighRisk(string $name): bool
    {
        return in_array($name, self::highRiskNames(), true);
    }

    /**
     * Create any missing permissions from the catalog. Existing permissions are
     * left untouched; removal is handled separately (and deliberately avoided so
     * revoking a permission never silently breaks historical role assignments).
     */
    public static function sync(string $guard = 'web'): void
    {
        foreach (self::names() as $name) {
            Permission::query()->firstOrCreate([
                'name' => $name,
                'guard_name' => $guard,
            ]);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
