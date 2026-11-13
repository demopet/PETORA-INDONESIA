<?php

declare(strict_types=1);

use App\Models\Branch;
use App\Models\User;
use App\Support\SampleData;
use App\Support\Settings;
use App\Support\SetupState;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\PermissionRegistrar;

uses(RefreshDatabase::class);

it('loads and removes sample data idempotently after setup', function (): void {
    $this->seed(RolePermissionSeeder::class);

    $branch = Branch::factory()->create(['code' => 'HQ']);
    $owner = User::factory()->create(['is_active' => true, 'active_branch_id' => $branch->id]);
    $owner->branches()->attach($branch->id, ['is_primary' => true]);

    $registrar = app(PermissionRegistrar::class);
    $registrar->setPermissionsTeamId($branch->id);
    $owner->assignRole('Owner');
    $registrar->forgetCachedPermissions();

    SetupState::markCompleted();
    Settings::set(SampleData::FLAG_KEY, false, 'bool', 'platform');

    SampleData::load();

    expect(SampleData::isLoaded())->toBeTrue()
        ->and(Branch::query()->where('code', 'DEMO')->exists())->toBeTrue()
        ->and(User::query()->where('email', 'manajer.demo@petora.test')->exists())->toBeTrue()
        ->and(User::query()->where('email', 'kasir.demo@petora.test')->exists())->toBeTrue()
        ->and(User::query()->where('email', 'dokter.demo@petora.test')->exists())->toBeTrue();

    // idempotent
    SampleData::load();
    expect(Branch::query()->where('code', 'DEMO')->count())->toBe(1)
        ->and(User::query()->whereIn('email', ['manajer.demo@petora.test', 'kasir.demo@petora.test', 'dokter.demo@petora.test'])->count())->toBe(3);

    SampleData::remove();

    expect(SampleData::isLoaded())->toBeFalse()
        ->and(User::query()->where('email', 'manajer.demo@petora.test')->exists())->toBeFalse()
        ->and(Branch::query()->where('code', 'DEMO')->exists())->toBeFalse();
});
