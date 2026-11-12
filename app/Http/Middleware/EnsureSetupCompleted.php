<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Support\SetupState;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSetupCompleted
{
    /** @param  Closure(Request): Response  $next */
    public function handle(Request $request, Closure $next): Response
    {
        if (app()->runningUnitTests()) {
            return $next($request);
        }

        try {
            $completed = SetupState::isCompleted();
        } catch (\Throwable) {
            $completed = false;
        }

        $path = trim($request->path(), '/');
        $isSetup = $path === 'admin/setup' || str_starts_with($path, 'admin/setup/');
        $isAuth = str_starts_with($path, 'admin/login')
            || str_starts_with($path, 'admin/password-reset')
            || str_starts_with($path, 'admin/forgot-password')
            || str_starts_with($path, 'admin/logout')
            || str_starts_with($path, 'livewire');

        if (! $completed) {
            if (! $isSetup && ! $isAuth) {
                return redirect()->to('/admin/setup');
            }

            // Allow setup page to be accessed without prior login during initial install.
            return $next($request);
        }

        if ($isSetup) {
            return redirect()->to('/admin');
        }

        return $next($request);
    }
}
