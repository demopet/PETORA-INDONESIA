<?php

declare(strict_types=1);

use App\Models\Branch;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\PermissionRegistrar;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

uses(RefreshDatabase::class);

it('always renders mandatory attribution in admin panel footer', function (): void {
    $this->seed(RolePermissionSeeder::class);

    $branch = Branch::factory()->create();
    $user = User::factory()->create([
        'is_active' => true,
        'active_branch_id' => $branch->id,
    ]);

    app(PermissionRegistrar::class)->setPermissionsTeamId($branch->id);
    $user->assignRole('Owner');

    actingAs($user);

    get('/admin')
        ->assertSuccessful()
        ->assertSee('Powered by PETORA INDONESIA')
        ->assertSee('data-petora-attribution="1"', false);
});
