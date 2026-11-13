<?php

declare(strict_types=1);

use App\Enums\PermissionExceptionMode;
use App\Filament\Resources\UserResource;
use App\Models\Branch;
use App\Models\PermissionException;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Activitylog\Models\Activity;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

function makeBranchUser(string $role = 'Kasir'): array
{
    $branch = Branch::factory()->create();
    $user = User::factory()->create(['is_active' => true, 'active_branch_id' => $branch->id]);
    $user->branches()->attach($branch->id, ['is_primary' => true]);

    $registrar = app(PermissionRegistrar::class);
    $registrar->setPermissionsTeamId($branch->id);
    $registrar->forgetCachedPermissions();

    if ($role !== '') {
        $user->assignRole($role);
        $registrar->forgetCachedPermissions();
    }

    return [$branch, $user];
}

it('denies a role-granted permission when a revoke exception exists', function (): void {
    $this->seed(RolePermissionSeeder::class);
    [$branch, $user] = makeBranchUser('Owner');

    $permission = Permission::findByName('platform.settings.view');

    expect($user->can('platform.settings.view'))->toBeTrue();

    PermissionException::query()->create([
        'user_id' => $user->id,
        'permission_id' => $permission->id,
        'mode' => PermissionExceptionMode::Revoke,
        'reason' => 'Ditolak sementara oleh Owner.',
    ]);

    expect($user->fresh()->can('platform.settings.view'))->toBeFalse()
        ->and($user->fresh()->can('platform.branches.view'))->toBeTrue();
});

it('grants a permission not provided by any role when a grant exception exists', function (): void {
    $this->seed(RolePermissionSeeder::class);
    [, $user] = makeBranchUser('Kasir');

    $permission = Permission::findByName('platform.settings.view');

    expect($user->can('platform.settings.view'))->toBeFalse();

    PermissionException::query()->create([
        'user_id' => $user->id,
        'permission_id' => $permission->id,
        'mode' => PermissionExceptionMode::Grant,
        'reason' => 'Wakil sementara manajer.',
    ]);

    expect($user->fresh()->can('platform.settings.view'))->toBeTrue();
});

it('requires a reason when creating an exception', function (): void {
    $this->seed(RolePermissionSeeder::class);
    [, $user] = makeBranchUser('Kasir');

    $permission = Permission::findByName('platform.settings.view');

    expect(fn (): PermissionException => PermissionException::query()->create([
        'user_id' => $user->id,
        'permission_id' => $permission->id,
        'mode' => PermissionExceptionMode::Revoke,
        'reason' => '',
    ]))->toThrow(InvalidArgumentException::class);
});

it('records exception changes in the activity log', function (): void {
    $this->seed(RolePermissionSeeder::class);
    [, $user] = makeBranchUser('Owner');

    $permission = Permission::findByName('platform.settings.view');

    $exception = PermissionException::query()->create([
        'user_id' => $user->id,
        'permission_id' => $permission->id,
        'mode' => PermissionExceptionMode::Revoke,
        'reason' => 'Alasan tercatat.',
    ]);

    $log = Activity::query()
        ->where('subject_type', PermissionException::class)
        ->where('subject_id', $exception->id)
        ->where('event', 'created')
        ->first();

    expect($log)->not->toBeNull()
        ->and(json_encode($log?->properties?->toArray(), JSON_UNESCAPED_UNICODE))->toContain('Alasan tercatat.');
});

it('allows only one exception per user and permission', function (): void {
    $this->seed(RolePermissionSeeder::class);
    [, $user] = makeBranchUser('Owner');

    $permission = Permission::findByName('platform.settings.view');

    PermissionException::query()->create([
        'user_id' => $user->id,
        'permission_id' => $permission->id,
        'mode' => PermissionExceptionMode::Revoke,
        'reason' => 'Pertama.',
    ]);

    expect(fn (): PermissionException => PermissionException::query()->create([
        'user_id' => $user->id,
        'permission_id' => $permission->id,
        'mode' => PermissionExceptionMode::Grant,
        'reason' => 'Kedua.',
    ]))->toThrow(QueryException::class);
});

it('records the authenticated creator of an exception', function (): void {
    $this->seed(RolePermissionSeeder::class);
    [$branch, $owner] = makeBranchUser('Owner');
    [, $target] = makeBranchUser('Kasir');

    actingAs($owner);

    $permission = Permission::findByName('platform.settings.view');

    $exception = PermissionException::query()->create([
        'user_id' => $target->id,
        'permission_id' => $permission->id,
        'mode' => PermissionExceptionMode::Revoke,
        'reason' => 'Diatur oleh owner.',
    ]);

    expect($exception->created_by)->toBe($owner->id);
});

it('shows the per-user exceptions section on the user edit screen', function (): void {
    $this->seed(RolePermissionSeeder::class);
    [$branch, $owner] = makeBranchUser('Owner');
    [, $target] = makeBranchUser('Kasir');

    actingAs($owner);

    test()->get(UserResource::getUrl('edit', ['record' => $target->getKey()]))
        ->assertSuccessful()
        ->assertSee('Pengecualian Izin');
});
