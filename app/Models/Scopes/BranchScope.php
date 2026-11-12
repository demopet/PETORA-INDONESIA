<?php

declare(strict_types=1);

namespace App\Models\Scopes;

use App\Support\BranchContext;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

/** @implements Scope<Model> */
class BranchScope implements Scope
{
    public static bool $bypass = false;

    /**
     * @param  Builder<Model>  $builder
     */
    public function apply(Builder $builder, Model $model): void
    {
        if (self::$bypass || BranchContext::isAllBranches()) {
            return;
        }

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
            return $builder->withoutGlobalScope($scope);
        });
    }
}
