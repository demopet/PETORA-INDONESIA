<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Support\BranchContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetPermissionTeamContext
{
    /** @param  Closure(Request): Response  $next */
    public function handle(Request $request, Closure $next): Response
    {
        BranchContext::syncPermissionTeam(BranchContext::permissionTeamId());

        return $next($request);
    }
}
