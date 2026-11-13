<?php

declare(strict_types=1);

namespace App\Listeners;

use Illuminate\Auth\Events\Failed;
use Illuminate\Database\Eloquent\Model;

class LogFailedLogin
{
    public function handle(Failed $event): void
    {
        $credentials = $event->credentials;
        $email = $credentials['email'] ?? null;

        $properties = [
            'guard' => $event->guard,
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'email' => $email,
        ];

        $logger = activity('auth')->withProperties($properties);

        if ($event->user instanceof Model) {
            $logger->causedBy($event->user);
        }

        $logger->log('auth.login_failed');
    }
}
