<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\PermissionRegistrar;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
        ]);

        $branch = Branch::query()->first();
        if (! $branch) {
            $branch = Branch::query()->create([
                'name' => 'Cabang Utama',
                'code' => 'HQ',
                'timezone' => 'Asia/Jakarta',
                'is_active' => true,
            ]);
        }

        $user = User::query()->firstOrCreate(
            ['email' => 'owner@petora.test'],
            [
                'name' => 'Owner',
                'password' => Hash::make('password'),
                'is_active' => true,
                'active_branch_id' => $branch->id,
            ]
        );

        if (! $user->active_branch_id) {
            $user->forceFill(['active_branch_id' => $branch->id])->save();
        }

        $user->branches()->syncWithoutDetaching([$branch->id => ['is_primary' => true]]);

        $registrar = app(PermissionRegistrar::class);
        $registrar->setPermissionsTeamId($branch->id);
        $registrar->forgetCachedPermissions();
        if (! $user->hasRole('Owner')) {
            $user->assignRole('Owner');
        }
        $registrar->forgetCachedPermissions();
    }
}
