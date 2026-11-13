<?php

declare(strict_types=1);

use App\Models\Branch;
use App\Models\Setting;
use App\Models\User;
use App\Support\Settings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Activitylog\Models\Activity;

uses(RefreshDatabase::class);

it('logs branch creation and updates to activity log', function (): void {
    $branch = Branch::query()->create([
        'name' => 'Cabang Uji',
        'code' => 'TST',
        'timezone' => 'Asia/Jakarta',
        'is_active' => true,
    ]);

    expect(Activity::query()->where('subject_type', Branch::class)->where('subject_id', $branch->id)->exists())->toBeTrue();

    $branch->update(['name' => 'Cabang Uji Baru']);

    $update = Activity::query()
        ->where('subject_type', Branch::class)
        ->where('subject_id', $branch->id)
        ->where('event', 'updated')
        ->latest('id')
        ->first();

    expect($update)->not->toBeNull()
        ->and(data_get($update?->attribute_changes?->toArray(), 'attributes.name'))->toBe('Cabang Uji Baru');
});

it('logs setting changes and excludes sensitive user attributes from logs', function (): void {
    Settings::set('platform.sample', 'awal', 'string', 'platform');

    $settingLog = Activity::query()
        ->where('subject_type', Setting::class)
        ->latest('id')
        ->first();

    expect($settingLog)->not->toBeNull();

    $user = User::factory()->create(['password' => 'secret-pass-123']);
    $user->update(['name' => 'Nama Baru', 'password' => 'changed-pass-456']);

    $userUpdate = Activity::query()
        ->where('subject_type', User::class)
        ->where('subject_id', $user->id)
        ->where('event', 'updated')
        ->latest('id')
        ->first();

    expect($userUpdate)->not->toBeNull()
        ->and(data_get($userUpdate?->attribute_changes?->toArray(), 'attributes.name'))->toBe('Nama Baru')
        ->and(data_get($userUpdate?->attribute_changes?->toArray(), 'attributes.password'))->toBeNull()
        ->and(data_get($userUpdate?->attribute_changes?->toArray(), 'old.password'))->toBeNull();
});
