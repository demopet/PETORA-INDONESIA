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

it('redirects guest from admin dashboard to login', function (): void {
    get('/admin')->assertRedirect('/admin/login');
});

it('allows active owner to access admin dashboard and shows attribution', function (): void {
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
        ->assertSee('Powered by PETORA INDONESIA');
});

it('denies inactive user access to admin panel', function (): void {
    $user = User::factory()->inactive()->create();

    actingAs($user);

    get('/admin')->assertForbidden();
});
