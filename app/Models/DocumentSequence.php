<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentSequence extends Model
{
    /** @var list<string> */
    protected $fillable = [
        'document_type',
        'branch_id',
        'period',
        'last_number',
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
}
