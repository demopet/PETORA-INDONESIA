<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use App\Support\BranchContext;
use Illuminate\Http\RedirectResponse;

class BranchSwitcherController extends Controller
{
    public function __invoke(string|int $branch): RedirectResponse
    {
        $user = auth()->user();

        if (! $user instanceof User) {
            abort(403);
        }

        if ($branch === 'all') {
            if (! $user->can('platform.branches.view_all')) {
                abort(403);
            }

            BranchContext::setAllBranches();
            $user->forceFill(['active_branch_id' => null])->save();

            return back(fallback: '/admin');
        }

        $branchId = (int) $branch;
        $hasAccess = $user->branches()->whereKey($branchId)->where('is_active', true)->exists();

        if (! $hasAccess) {
            abort(403);
        }

        BranchContext::set($branchId);
        $user->forceFill(['active_branch_id' => $branchId])->save();

        return back(fallback: '/admin');
    }
}
