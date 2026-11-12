<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Support\SetupState;
use Filament\Http\Middleware\Authenticate as BaseAuthenticate;

class FilamentAuthenticate extends BaseAuthenticate
{
    /**
     * @param  array<string>  $guards
     */
    protected function authenticate($request, array $guards): void
    {
        if (! app()->runningUnitTests()) {
            try {
                if (! SetupState::isCompleted()) {
                    $path = trim($request->path(), '/');
                    if ($path === 'admin/setup' || str_starts_with($path, 'admin/setup/')) {
                        return;
                    }
                }
            } catch (\Throwable) {
                // fall through to normal auth behavior
            }
        }

        parent::authenticate($request, $guards);
    }
}
