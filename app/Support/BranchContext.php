<?php

declare(strict_types=1);

namespace App\Support;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\PermissionRegistrar;

class BranchContext
{
    public const SESSION_KEY = 'active_branch_id';

    public const ALL_BRANCHES = 'all';

    public static function currentId(): ?int
    {
        $value = session(self::SESSION_KEY);

        if ($value === self::ALL_BRANCHES) {
            return null;
        }

        if ($value) {
            return (int) $value;
        }

        $user = Auth::user();

        if (! $user instanceof User) {
            return null;
        }

        return $user->active_branch_id ? (int) $user->active_branch_id : null;
    }

    public static function isAllBranches(): bool
    {
        $value = session(self::SESSION_KEY);

        if ($value === self::ALL_BRANCHES) {
            return true;
        }

        $user = Auth::user();

        if (! $user instanceof User) {
            return false;
        }

        return $user->active_branch_id === null
            && $value === null
            && $user->can('platform.branches.view_all');
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
        } else {
            session([self::SESSION_KEY => $branchId]);
        }

        self::syncPermissionTeam($branchId);
    }

    public static function setAllBranches(): void
    {
        session([self::SESSION_KEY => self::ALL_BRANCHES]);
        self::syncPermissionTeam(null);
    }

    public static function syncPermissionTeam(?int $branchId): void
    {
        $registrar = app(PermissionRegistrar::class);
        $registrar->setPermissionsTeamId($branchId);
    }
}
