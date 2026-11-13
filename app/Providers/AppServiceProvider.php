<?php

declare(strict_types=1);

namespace App\Providers;

use App\Listeners\LogFailedLogin;
use App\Listeners\LogLogout;
use App\Listeners\LogSuccessfulLogin;
use App\Models\Branch;
use App\Models\User;
use App\Policies\BranchPolicy;
use App\Policies\RolePolicy;
use App\Policies\UserPolicy;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Spatie\Permission\Models\Role;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Model::shouldBeStrict(! $this->app->isProduction());

        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(Branch::class, BranchPolicy::class);
        Gate::policy(Role::class, RolePolicy::class);

        Event::listen(Login::class, LogSuccessfulLogin::class);
        Event::listen(Logout::class, LogLogout::class);
        Event::listen(Failed::class, LogFailedLogin::class);
    }
}
