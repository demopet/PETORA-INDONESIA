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

        if (is_numeric($value) && (int) $value > 0) {
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
            $user = Auth::user();

            return $user instanceof User && $user->can('platform.branches.view_all');
        }

        return false;
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

        self::syncPermissionTeam(self::permissionTeamId());
    }

    public static function setAllBranches(): void
    {
        session([self::SESSION_KEY => self::ALL_BRANCHES]);
        self::syncPermissionTeam(self::permissionTeamId());
    }

    /**
     * Resolve team id for Spatie permission checks.
     * In all-branches mode, use the user's active/first branch so team-scoped roles remain effective
     * while BranchScope bypasses data scoping.
     */
    public static function permissionTeamId(): ?int
    {
        $current = self::currentId();
        if ($current !== null) {
            return $current;
        }

        if (! self::isAllBranches()) {
            return null;
        }

        $user = Auth::user();
        if (! $user instanceof User) {
            return null;
        }

        if ($user->active_branch_id) {
            return (int) $user->active_branch_id;
        }

        $id = $user->branches()->where('is_active', true)->value('branches.id');

        return $id ? (int) $id : null;
    }

    public static function syncPermissionTeam(?int $branchId): void
    {
        app(PermissionRegistrar::class)->setPermissionsTeamId($branchId);
    }
}
