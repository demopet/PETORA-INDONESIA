<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\BelongsToBranch;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentSequence extends Model
{
    use BelongsToBranch;

    /** @var list<string> */
    protected $fillable = [
        'document_type',
        'branch_id',
        'period',
        'last_number',
        'created_by',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'last_number' => 'integer',
        ];
    }

    /** @return BelongsTo<Branch, $this> */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Allow global (null-branch) sequences to remain visible in branch context.
     *
     * @param  Builder<self>  $query
     * @return Builder<self>
     */
    public function scopeForBranchIncludingGlobal(Builder $query, ?int $branchId): Builder
    {
        return $query->where(function (Builder $inner) use ($branchId): void {
            $inner->where('branch_id', $branchId)->orWhereNull('branch_id');
        });
    }
}
