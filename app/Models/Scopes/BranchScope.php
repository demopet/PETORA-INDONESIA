<?php

declare(strict_types=1);

namespace App\Models\Scopes;

use App\Enums\DataScope;
use App\Models\User;
use App\Support\BranchContext;
use App\Support\DataScopeResolver;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

/** @implements Scope<Model> */
class BranchScope implements Scope
{
    public static bool $bypass = false;

    /**
     * @param  Builder<Model>  $builder
     */
    public function apply(Builder $builder, Model $model): void
    {
        if (self::$bypass) {
            return;
        }

        $user = Auth::user();

        if ($user instanceof User) {
            $scope = DataScopeResolver::effective($user);

            if ($scope === DataScope::All && $user->can('platform.branches.view_all')) {
                return;
            }

            if (BranchContext::isAllBranches()) {
                return;
            }

            $this->applyBranch($builder, $model);

            if ($scope === DataScope::Own
                && method_exists($model, 'hasCreatedByColumn')
                && $model->hasCreatedByColumn()) {
                $builder->where($model->qualifyColumn('created_by'), $user->getKey());
            }

            return;
        }

        // No authenticated user (console/system context): fall back to the
        // active branch context, if any.
        if (BranchContext::isAllBranches()) {
            return;
        }

        $this->applyBranch($builder, $model);
    }

    /**
     * @param  Builder<Model>  $builder
     */
    protected function applyBranch(Builder $builder, Model $model): void
    {
        $branchId = BranchContext::currentId();
        if ($branchId !== null) {
            $builder->where($model->qualifyColumn('branch_id'), $branchId);
        }
    }

    /**
     * @param  Builder<Model>  $builder
     */
    public function extend(Builder $builder): void
    {
        $scope = $this;
        $builder->macro('withAllBranches', function (Builder $builder) use ($scope): Builder {
            $user = Auth::user();
            if ($user instanceof User && ! $user->can('platform.branches.view_all')) {
                return $builder;
            }

            return $builder->withoutGlobalScope($scope);
        });
    }
}
