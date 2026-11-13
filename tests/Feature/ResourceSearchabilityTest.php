<?php

declare(strict_types=1);

use App\Filament\Resources\ActivityLogResource;
use App\Filament\Resources\BranchResource;
use App\Filament\Resources\UserResource;
use App\Models\Branch;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('exposes globally searchable attributes and titles for core resources', function (): void {
    expect(BranchResource::getGloballySearchableAttributes())->toBe(['name', 'code'])
        ->and(UserResource::getGloballySearchableAttributes())->toBe(['name', 'email', 'phone'])
        ->and(ActivityLogResource::getGloballySearchableAttributes())->toBe(['description', 'log_name', 'event']);

    $branch = Branch::factory()->create(['name' => 'Cabang A', 'code' => 'CBA']);
    $user = User::factory()->create(['name' => 'Budi', 'email' => 'budi@example.test', 'phone' => '0812']);

    expect(BranchResource::getGlobalSearchResultTitle($branch))->toContain('Cabang A')
        ->and(UserResource::getGlobalSearchResultTitle($user))->toBe('Budi')
        ->and(UserResource::getGlobalSearchResultDetails($user)['Email'])->toBe('budi@example.test');
});
