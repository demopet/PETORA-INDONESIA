<?php

declare(strict_types=1);

use App\Enums\DataScope;
use App\Models\Branch;
use App\Models\DocumentSequence;
use App\Models\User;
use App\Support\BranchContext;
use App\Support\DataScopeResolver;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

function makeSequence(int $branchId, ?int $createdBy, int $lastNumber, string $period = '202611'): DocumentSequence
{
    return DocumentSequence::query()->create([
        'document_type' => 'INV',
        'branch_id' => $branchId,
        'period' => $period,
        'last_number' => $lastNumber,
        'created_by' => $createdBy,
    ]);
}

function makeScopedUser(int $branchId, string $role): User
{
    $user = User::factory()->create(['is_active' => true, 'active_branch_id' => $branchId]);
    $user->branches()->attach($branchId, ['is_primary' => true]);

    $registrar = app(PermissionRegistrar::class);
    $registrar->setPermissionsTeamId($branchId);
    $registrar->forgetCachedPermissions();
    $user->assignRole($role);
    $registrar->forgetCachedPermissions();

    return $user;
}

it('limits a branch-scoped role to the active branch', function (): void {
    $this->seed(RolePermissionSeeder::class);

    $branchA = Branch::factory()->create(['code' => 'AAA']);
    $branchB = Branch::factory()->create(['code' => 'BBB']);

    $user = makeScopedUser($branchA->id, 'Kasir');

    makeSequence($branchA->id, null, 1);
    makeSequence($branchB->id, null, 2);

    actingAs($user);
    BranchContext::set($branchA->id);

    expect(DocumentSequence::query()->pluck('branch_id')->all())->toBe([$branchA->id]);
});

it('lets an all-branches role see every branch', function (): void {
    $this->seed(RolePermissionSeeder::class);

    $branchA = Branch::factory()->create(['code' => 'AAA']);
    $branchB = Branch::factory()->create(['code' => 'BBB']);

    $user = makeScopedUser($branchA->id, 'Super Admin');

    makeSequence($branchA->id, null, 1);
    makeSequence($branchB->id, null, 2);

    actingAs($user);
    BranchContext::set($branchA->id);

    expect(DocumentSequence::query()->count())->toBe(2);
});

it('limits an own-data role to records created by the user within the active branch', function (): void {
    $this->seed(RolePermissionSeeder::class);

    $branchA = Branch::factory()->create(['code' => 'AAA']);
    $branchB = Branch::factory()->create(['code' => 'BBB']);

    $user = makeScopedUser($branchA->id, 'Kasir');
    $other = User::factory()->create(['is_active' => true, 'active_branch_id' => $branchA->id]);

    Role::query()->where('name', 'Kasir')->update(['data_scope' => DataScope::Own->value]);

    makeSequence($branchA->id, $user->id, 1, '202611');
    makeSequence($branchA->id, $other->id, 2, '202612');
    makeSequence($branchB->id, $user->id, 3, '202611');

    actingAs($user);
    BranchContext::set($branchA->id);

    $rows = DocumentSequence::query()->get();
    expect($rows)->toHaveCount(1)
        ->and($rows->first()?->created_by)->toBe($user->id);
});

it('resolves the broadest data scope among a user roles', function (): void {
    $this->seed(RolePermissionSeeder::class);

    $branchA = Branch::factory()->create(['code' => 'AAA']);
    $user = makeScopedUser($branchA->id, 'Kasir');

    expect(DataScopeResolver::effective($user))->toBe(DataScope::Branch);

    $registrar = app(PermissionRegistrar::class);
    $registrar->setPermissionsTeamId($branchA->id);
    $registrar->forgetCachedPermissions();
    $user->assignRole('Super Admin');
    $registrar->forgetCachedPermissions();

    expect(DataScopeResolver::effective($user->fresh()))->toBe(DataScope::All);
});

it('does not let a user without view-all bypass the branch scope via withAllBranches', function (): void {
    $this->seed(RolePermissionSeeder::class);

    $branchA = Branch::factory()->create(['code' => 'AAA']);
    $branchB = Branch::factory()->create(['code' => 'BBB']);

    $user = makeScopedUser($branchA->id, 'Kasir');

    makeSequence($branchA->id, null, 1);
    makeSequence($branchB->id, null, 2);

    actingAs($user);
    BranchContext::set($branchA->id);

    expect(DocumentSequence::query()->withAllBranches()->count())->toBe(1);
});
