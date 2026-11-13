<?php

declare(strict_types=1);

use App\Support\PermissionCatalog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;

uses(RefreshDatabase::class);

it('creates every catalog permission and is idempotent', function (): void {
    PermissionCatalog::sync();

    $expected = PermissionCatalog::names();

    expect(Permission::query()->where('guard_name', 'web')->count())->toBe(count($expected))
        ->and(Permission::query()->where('name', 'platform.users.manage')->exists())->toBeTrue();

    // Running again does not duplicate or remove anything.
    PermissionCatalog::sync();

    expect(Permission::query()->where('guard_name', 'web')->count())->toBe(count($expected))
        ->and(Permission::query()->where('name', 'platform.users.manage')->count())->toBe(1);
});

it('flags high-risk permissions', function (): void {
    $highRisk = PermissionCatalog::highRiskNames();

    expect($highRisk)->toContain('platform.settings.manage')
        ->and($highRisk)->toContain('platform.users.manage')
        ->and($highRisk)->toContain('platform.integrations.manage')
        ->and($highRisk)->not->toContain('platform.settings.view')
        ->and(PermissionCatalog::isHighRisk('platform.roles.manage'))->toBeTrue()
        ->and(PermissionCatalog::isHighRisk('platform.roles.view'))->toBeFalse();
});

it('groups permissions by module', function (): void {
    $modules = PermissionCatalog::modules();

    expect($modules)->toHaveKey('platform')
        ->and($modules['platform']['label'])->toBe('Platform');
});
