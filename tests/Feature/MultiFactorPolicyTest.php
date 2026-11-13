<?php

declare(strict_types=1);

use App\Models\Branch;
use App\Models\User;
use App\Support\MultiFactorPolicy;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\PermissionRegistrar;

uses(RefreshDatabase::class);

it('requires MFA for users with a configured required role in any branch', function (): void {
    test()->seed(RolePermissionSeeder::class);

    $branch = Branch::factory()->create();
    $owner = User::factory()->create(['is_active' => true, 'active_branch_id' => $branch->id]);
    $kasir = User::factory()->create(['is_active' => true, 'active_branch_id' => $branch->id]);

    $registrar = app(PermissionRegistrar::class);
    $registrar->setPermissionsTeamId($branch->id);
    $owner->assignRole('Owner');
    $kasir->assignRole('Kasir');
    $registrar->forgetCachedPermissions();

    MultiFactorPolicy::setRequiredRoles(['Owner', 'Super Admin']);

    expect(MultiFactorPolicy::isRequiredFor($owner->fresh()))->toBeTrue()
        ->and(MultiFactorPolicy::isRequiredFor($kasir->fresh()))->toBeFalse();
});

it('does not require MFA when required roles list is empty', function (): void {
    test()->seed(RolePermissionSeeder::class);

    $branch = Branch::factory()->create();
    $owner = User::factory()->create(['is_active' => true, 'active_branch_id' => $branch->id]);

    $registrar = app(PermissionRegistrar::class);
    $registrar->setPermissionsTeamId($branch->id);
    $owner->assignRole('Owner');
    $registrar->forgetCachedPermissions();

    MultiFactorPolicy::setRequiredRoles([]);

    expect(MultiFactorPolicy::isRequiredFor($owner->fresh()))->toBeFalse();
});
