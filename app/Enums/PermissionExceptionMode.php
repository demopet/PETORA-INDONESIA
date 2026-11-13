<?php

declare(strict_types=1);

namespace App\Enums;

enum PermissionExceptionMode: string
{
    case Grant = 'grant';
    case Revoke = 'revoke';

    public function label(): string
    {
        return match ($this) {
            self::Grant => 'Berikan',
            self::Revoke => 'Cabut',
        };
    }

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        $options = [];
        foreach (self::cases() as $case) {
            $options[$case->value] = $case->label();
        }

        return $options;
    }
}
