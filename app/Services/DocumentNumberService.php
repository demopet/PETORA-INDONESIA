<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\DocumentSequence;
use App\Models\Scopes\BranchScope;
use Illuminate\Support\Facades\DB;

class DocumentNumberService
{
    /**
     * Generate next document number atomically for type/branch/period.
     *
     * @param  array{prefix?: string, separator?: string, pad?: int, include_period?: bool}  $options
     */
    public function next(string $documentType, ?int $branchId = null, ?string $period = null, array $options = []): string
    {
        $period ??= now()->format('Ym');
        $prefix = $options['prefix'] ?? strtoupper($documentType);
        $separator = $options['separator'] ?? '/';
        $pad = (int) ($options['pad'] ?? 5);
        $includePeriod = (bool) ($options['include_period'] ?? true);

        return DB::transaction(function () use ($documentType, $branchId, $period, $prefix, $separator, $pad, $includePeriod): string {
            // Bypass branch global scope: sequence allocation must target the explicit branch_id.
            $sequence = DocumentSequence::query()
                ->withoutGlobalScope(BranchScope::class)
                ->where('document_type', $documentType)
                ->where('branch_id', $branchId)
                ->where('period', $period)
                ->lockForUpdate()
                ->first();

            if (! $sequence) {
                $sequence = new DocumentSequence([
                    'document_type' => $documentType,
                    'branch_id' => $branchId,
                    'period' => $period,
                    'last_number' => 0,
                    'created_by' => auth()->id(),
                ]);
                $sequence->saveQuietly();
                $sequence = DocumentSequence::query()
                    ->withoutGlobalScope(BranchScope::class)
                    ->whereKey($sequence->id)
                    ->lockForUpdate()
                    ->firstOrFail();
            }

            $sequence->last_number++;
            $sequence->save();

            $parts = [$prefix];
            if ($includePeriod) {
                $parts[] = $period;
            }
            $parts[] = str_pad((string) $sequence->last_number, $pad, '0', STR_PAD_LEFT);

            return implode($separator, $parts);
        });
    }
}
