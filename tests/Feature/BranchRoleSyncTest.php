<?php

declare(strict_types=1);

use App\Models\Branch;
use App\Models\User;
use App\Support\BranchRoleSync;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\PermissionRegistrar;

uses(RefreshDatabase::class);

it('syncs and reads roles per branch using team context', function (): void {
    $this->seed(RolePermissionSeeder::class);

    $branchA = Branch::factory()->create(['code' => 'AAA']);
    $branchB = Branch::factory()->create(['code' => 'BBB']);
    $user = User::factory()->create(['is_active' => true, 'active_branch_id' => $branchA->id]);
    $user->branches()->attach([$branchA->id, $branchB->id]);

    BranchRoleSync::syncRolesForBranch($user, $branchA->id, ['Kasir']);
    BranchRoleSync::syncRolesForBranch($user, $branchB->id, ['Manajer Cabang']);

    expect(BranchRoleSync::roleNamesForBranch($user->fresh(), $branchA->id))->toBe(['Kasir'])
        ->and(BranchRoleSync::roleNamesForBranch($user->fresh(), $branchB->id))->toBe(['Manajer Cabang']);

    $map = BranchRoleSync::rolesByBranch($user->fresh());
    expect($map[$branchA->id] ?? [])->toBe(['Kasir'])
        ->and($map[$branchB->id] ?? [])->toBe(['Manajer Cabang']);

    BranchRoleSync::syncRolesForBranch($user->fresh(), $branchA->id, []);
    expect(BranchRoleSync::roleNamesForBranch($user->fresh(), $branchA->id))->toBe([]);
});

it('does not leak team roles across branch contexts', function (): void {
    $this->seed(RolePermissionSeeder::class);

    $branchA = Branch::factory()->create(['code' => 'AAA']);
    $branchB = Branch::factory()->create(['code' => 'BBB']);
    $user = User::factory()->create(['is_active' => true, 'active_branch_id' => $branchA->id]);
    $user->branches()->attach([$branchA->id, $branchB->id]);

    BranchRoleSync::syncRolesForBranch($user, $branchA->id, ['Kasir']);

    $registrar = app(PermissionRegistrar::class);
    $registrar->setPermissionsTeamId($branchB->id);
    $registrar->forgetCachedPermissions();
    expect($user->fresh()->hasRole('Kasir'))->toBeFalse();

    $registrar->setPermissionsTeamId($branchA->id);
    $registrar->forgetCachedPermissions();
    expect($user->fresh()->hasRole('Kasir'))->toBeTrue();
});
