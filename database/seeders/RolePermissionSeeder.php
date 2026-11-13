<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Support\PermissionCatalog;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    /**
     * Default data scope per built-in role. Owner/Super Admin see all branches;
     * every other role is limited to its active branch.
     *
     * @var array<string, string>
     */
    private const DATA_SCOPES = [
        'Owner' => 'all',
        'Super Admin' => 'all',
        'Manajer Cabang' => 'branch',
        'Kasir' => 'branch',
        'Resepsionis' => 'branch',
        'Dokter Hewan' => 'branch',
        'Paramedis' => 'branch',
        'Groomer' => 'branch',
        'Petugas Penitipan' => 'branch',
        'Admin Gudang' => 'branch',
        'Keuangan' => 'branch',
    ];

    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $guards = ['web'];

        foreach ($guards as $guard) {
            PermissionCatalog::sync($guard);

            foreach (self::DATA_SCOPES as $name => $dataScope) {
                $role = Role::query()->firstOrCreate(
                    ['name' => $name, 'guard_name' => $guard],
                    ['data_scope' => $dataScope],
                );

                if ($role->getAttribute('data_scope') !== $dataScope) {
                    $role->setAttribute('data_scope', $dataScope);
                    $role->save();
                }
            }

            $all = Permission::query()->where('guard_name', $guard)->pluck('name')->all();
            Role::query()->where('guard_name', $guard)
                ->whereIn('name', ['Owner', 'Super Admin'])
                ->get()
                ->each(function (Role $role) use ($all): void {
                    $role->syncPermissions($all);
                });
        }
    }
}
