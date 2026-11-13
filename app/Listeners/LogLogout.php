<?php

declare(strict_types=1);

namespace App\Listeners;

use Illuminate\Auth\Events\Logout;
use Illuminate\Database\Eloquent\Model;

class LogLogout
{
    public function handle(Logout $event): void
    {
        $user = $event->user;
        if (! $user instanceof Model) {
            return;
        }

        activity('auth')
            ->causedBy($user)
            ->withProperties([
                'guard' => $event->guard,
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ])
            ->log('auth.logout');
    }
}
