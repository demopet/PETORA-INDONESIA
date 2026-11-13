<?php

declare(strict_types=1);

namespace App\Enums;

enum DataScope: string
{
    case All = 'all';
    case Branch = 'branch';
    case Own = 'own';

    public function label(): string
    {
        return match ($this) {
            self::All => 'Semua cabang',
            self::Branch => 'Cabang aktif',
            self::Own => 'Data sendiri',
        };
    }

    /**
     * Higher is broader. Used to resolve the most permissive scope when a user
     * holds several roles with different data scopes.
     */
    public function rank(): int
    {
        return match ($this) {
            self::Own => 1,
            self::Branch => 2,
            self::All => 3,
        };
    }

    public function isBroaderThan(self $other): bool
    {
        return $this->rank() > $other->rank();
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
