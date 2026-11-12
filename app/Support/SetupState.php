<?php

declare(strict_types=1);

namespace App\Support;

class SetupState
{
    public const KEY = 'platform.setup_completed';

    public static function isCompleted(): bool
    {
        return (bool) Settings::get(self::KEY, false);
    }

    public static function markCompleted(): void
    {
        Settings::set(self::KEY, true, 'bool', 'platform');
    }
}
