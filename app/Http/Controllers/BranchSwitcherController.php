<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use App\Support\BranchContext;
use Illuminate\Http\RedirectResponse;
use Spatie\Permission\PermissionRegistrar;

class BranchSwitcherController extends Controller
{
    public function __invoke(int $branch): RedirectResponse
    {
        $user = auth()->user();

        if (! $user instanceof User) {
            abort(403);
        }

        $hasAccess = $user->branches()->whereKey($branch)->where('is_active', true)->exists();

        if (! $hasAccess) {
            abort(403);
        }

        BranchContext::set($branch);
        $user->forceFill(['active_branch_id' => $branch])->save();

        app(PermissionRegistrar::class)->setPermissionsTeamId($branch);
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return back(fallback: '/admin');
    }
}
