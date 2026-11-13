<?php

declare(strict_types=1);

use App\Listeners\LogFailedLogin;
use App\Listeners\LogLogout;
use App\Listeners\LogSuccessfulLogin;
use App\Models\User;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Activitylog\Models\Activity;

uses(RefreshDatabase::class);

it('logs successful login and logout events', function (): void {
    $user = User::factory()->create(['email' => 'staff@example.test']);

    app(LogSuccessfulLogin::class)->handle(new Login('web', $user, false));
    app(LogLogout::class)->handle(new Logout('web', $user));

    expect(Activity::query()->where('log_name', 'auth')->where('description', 'auth.login')->exists())->toBeTrue()
        ->and(Activity::query()->where('log_name', 'auth')->where('description', 'auth.logout')->exists())->toBeTrue();

    $login = Activity::query()->where('description', 'auth.login')->latest('id')->first();
    expect($login?->causer_id)->toBe($user->id)
        ->and($login?->causer_type)->toBe(User::class);
});

it('logs failed login attempts', function (): void {
    $user = User::factory()->create(['email' => 'staff@example.test']);

    app(LogFailedLogin::class)->handle(new Failed('web', $user, ['email' => 'staff@example.test', 'password' => 'wrong']));
    app(LogFailedLogin::class)->handle(new Failed('web', null, ['email' => 'unknown@example.test', 'password' => 'wrong']));

    $failed = Activity::query()->where('log_name', 'auth')->where('description', 'auth.login_failed')->get();

    expect($failed)->toHaveCount(2)
        ->and($failed->pluck('causer_id')->filter()->values()->all())->toContain($user->id);
});
