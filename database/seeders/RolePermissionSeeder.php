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
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $guards = ['web'];

        foreach ($guards as $guard) {
            PermissionCatalog::sync($guard);

            $owner = Role::query()->firstOrCreate(['name' => 'Owner', 'guard_name' => $guard]);
            $super = Role::query()->firstOrCreate(['name' => 'Super Admin', 'guard_name' => $guard]);
            Role::query()->firstOrCreate(['name' => 'Manajer Cabang', 'guard_name' => $guard]);
            Role::query()->firstOrCreate(['name' => 'Kasir', 'guard_name' => $guard]);
            Role::query()->firstOrCreate(['name' => 'Resepsionis', 'guard_name' => $guard]);
            Role::query()->firstOrCreate(['name' => 'Dokter Hewan', 'guard_name' => $guard]);
            Role::query()->firstOrCreate(['name' => 'Paramedis', 'guard_name' => $guard]);
            Role::query()->firstOrCreate(['name' => 'Groomer', 'guard_name' => $guard]);
            Role::query()->firstOrCreate(['name' => 'Petugas Penitipan', 'guard_name' => $guard]);
            Role::query()->firstOrCreate(['name' => 'Admin Gudang', 'guard_name' => $guard]);
            Role::query()->firstOrCreate(['name' => 'Keuangan', 'guard_name' => $guard]);

            $all = Permission::query()->where('guard_name', $guard)->pluck('name')->all();
            $owner->syncPermissions($all);
            $super->syncPermissions($all);
        }
    }
}
