<?php

declare(strict_types=1);

namespace App\Support;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class BranchContext
{
    public const SESSION_KEY = 'active_branch_id';

    public static function currentId(): ?int
    {
        $id = session(self::SESSION_KEY);

        if ($id) {
            return (int) $id;
        }

        $user = Auth::user();

        return $user instanceof User ? $user->active_branch_id : null;
    }

    public static function current(): ?Branch
    {
        $id = self::currentId();

        return $id ? Branch::query()->find($id) : null;
    }

    public static function set(?int $branchId): void
    {
        if ($branchId === null) {
            session()->forget(self::SESSION_KEY);

            return;
        }

        session([self::SESSION_KEY => $branchId]);
    }
}
