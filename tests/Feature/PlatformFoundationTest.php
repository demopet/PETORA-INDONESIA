<?php

declare(strict_types=1);

use App\Models\Branch;
use App\Models\User;
use App\Services\DocumentNumberService;
use App\Support\Settings;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

uses(RefreshDatabase::class);

it('stores and retrieves typed settings', function (): void {
    Settings::set('app.name', 'PETORA Test', 'string', 'branding');
    Settings::set('platform.flag', true, 'bool', 'platform');
    Settings::set('platform.count', 7, 'int', 'platform');
    Settings::set('platform.meta', ['a' => 1], 'json', 'platform');

    expect(Settings::get('app.name'))->toBe('PETORA Test')
        ->and(Settings::get('platform.flag'))->toBeTrue()
        ->and(Settings::get('platform.count'))->toBe(7)
        ->and(Settings::get('platform.meta'))->toBe(['a' => 1]);
});

it('generates gapless sequential document numbers per type branch and period', function (): void {
    $branch = Branch::factory()->create();
    $service = app(DocumentNumberService::class);

    $n1 = $service->next('INV', $branch->id, '202611', ['prefix' => 'INV', 'pad' => 5]);
    $n2 = $service->next('INV', $branch->id, '202611', ['prefix' => 'INV', 'pad' => 5]);
    $n3 = $service->next('INV', null, '202611', ['prefix' => 'INV', 'pad' => 5]);

    expect($n1)->toBe('INV/202611/00001')
        ->and($n2)->toBe('INV/202611/00002')
        ->and($n3)->toBe('INV/202611/00001');
});

it('seeds default roles and assigns owner role', function (): void {
    $this->seed(RolePermissionSeeder::class);

    $user = User::factory()->create();
    $branch = Branch::factory()->create();

    app(PermissionRegistrar::class)->setPermissionsTeamId($branch->id);
    $user->assignRole('Owner');

    expect($user->hasRole('Owner'))->toBeTrue()
        ->and(Role::query()->where('name', 'Owner')->exists())->toBeTrue();
});
