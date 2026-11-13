<?php

declare(strict_types=1);

use App\Models\Branch;
use App\Models\User;
use App\Support\BranchContext;
use App\Support\OwnerProtection;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\PermissionRegistrar;

use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

it('resolves permission team id to a branch when in all branches mode for authorized user', function (): void {
    $this->seed(RolePermissionSeeder::class);

    $branch = Branch::factory()->create();
    $user = User::factory()->create(['is_active' => true, 'active_branch_id' => $branch->id]);
    $user->branches()->attach($branch->id, ['is_primary' => true]);

    app(PermissionRegistrar::class)->setPermissionsTeamId($branch->id);
    $user->assignRole('Owner');
    app(PermissionRegistrar::class)->forgetCachedPermissions();

    actingAs($user);
    BranchContext::setAllBranches();

    expect(BranchContext::isAllBranches())->toBeTrue()
        ->and(BranchContext::currentId())->toBeNull()
        ->and(BranchContext::permissionTeamId())->toBe($branch->id)
        ->and($user->fresh()->can('platform.branches.view_all'))->toBeTrue();
});

it('does not treat unauthorised user as all branches mode', function (): void {
    $this->seed(RolePermissionSeeder::class);

    $branch = Branch::factory()->create();
    $user = User::factory()->create(['is_active' => true, 'active_branch_id' => $branch->id]);
    $user->branches()->attach($branch->id, ['is_primary' => true]);

    actingAs($user);
    BranchContext::setAllBranches();

    expect(BranchContext::isAllBranches())->toBeFalse();
});

it('counts active owners across team-scoped role assignments', function (): void {
    $this->seed(RolePermissionSeeder::class);

    $branch = Branch::factory()->create();
    $ownerA = User::factory()->create(['is_active' => true, 'active_branch_id' => $branch->id]);
    $ownerB = User::factory()->create(['is_active' => true, 'active_branch_id' => $branch->id]);

    $registrar = app(PermissionRegistrar::class);
    $registrar->setPermissionsTeamId($branch->id);
    $ownerA->assignRole('Owner');
    $ownerB->assignRole('Owner');
    $registrar->forgetCachedPermissions();

    expect(OwnerProtection::activeOwnerCount())->toBe(2)
        ->and(OwnerProtection::isOwner($ownerA->fresh()))->toBeTrue()
        ->and(OwnerProtection::canDeactivate($ownerA->fresh()))->toBeTrue()
        ->and(OwnerProtection::canDelete($ownerA->fresh()))->toBeTrue();
});

it('prevents removing the last active owner', function (): void {
    $this->seed(RolePermissionSeeder::class);

    $branch = Branch::factory()->create();
    $owner = User::factory()->create(['is_active' => true, 'active_branch_id' => $branch->id]);
    $other = User::factory()->create(['is_active' => true, 'active_branch_id' => $branch->id]);

    $registrar = app(PermissionRegistrar::class);
    $registrar->setPermissionsTeamId($branch->id);
    $owner->assignRole('Owner');
    $registrar->forgetCachedPermissions();

    expect(OwnerProtection::activeOwnerCount())->toBe(1)
        ->and(OwnerProtection::canDeactivate($owner->fresh()))->toBeFalse()
        ->and(OwnerProtection::canRemoveOwnerRole($owner->fresh()))->toBeFalse()
        ->and(OwnerProtection::canDelete($owner->fresh()))->toBeFalse()
        ->and(OwnerProtection::canDeactivate($other->fresh()))->toBeTrue();
});
