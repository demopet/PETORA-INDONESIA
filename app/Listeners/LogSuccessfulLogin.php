<?php

declare(strict_types=1);

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Database\Eloquent\Model;

class LogSuccessfulLogin
{
    public function handle(Login $event): void
    {
        $logger = activity('auth')
            ->withProperties([
                'guard' => $event->guard,
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

        if ($event->user instanceof Model) {
            $logger->causedBy($event->user);
        }

        $logger->log('auth.login');
    }
}
