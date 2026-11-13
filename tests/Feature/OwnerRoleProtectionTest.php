<?php

declare(strict_types=1);

use App\Models\Branch;
use App\Models\User;
use App\Support\OwnerProtection;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

uses(RefreshDatabase::class);

it('prevents deleting the owner role via policy', function (): void {
    $this->seed(RolePermissionSeeder::class);

    $ownerRole = Role::query()->where('name', 'Owner')->where('guard_name', 'web')->firstOrFail();
    $kasirRole = Role::query()->where('name', 'Kasir')->where('guard_name', 'web')->firstOrFail();

    $branch = Branch::factory()->create();
    $user = User::factory()->create(['is_active' => true, 'active_branch_id' => $branch->id]);
    $user->branches()->attach($branch->id, ['is_primary' => true]);

    $registrar = app(PermissionRegistrar::class);
    $registrar->setPermissionsTeamId($branch->id);
    $user->assignRole('Owner');
    $registrar->forgetCachedPermissions();

    expect($user->can('delete', $ownerRole))->toBeFalse()
        ->and($user->can('delete', $kasirRole))->toBeTrue()
        ->and(OwnerProtection::activeOwnerCount())->toBe(1);
});
