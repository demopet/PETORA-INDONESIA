<?php

declare(strict_types=1);

namespace App\Models\Concerns;

use App\Models\Branch;
use App\Models\Scopes\BranchScope;
use App\Support\BranchContext;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin Model
 */
trait BelongsToBranch
{
    public static function bootBelongsToBranch(): void
    {
        static::addGlobalScope(new BranchScope);

        static::creating(function (Model $model): void {
            if (! $model->getAttribute('branch_id') && BranchContext::currentId()) {
                $model->setAttribute('branch_id', BranchContext::currentId());
            }
        });
    }

    /** @return BelongsTo<Branch, $this> */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }
}
