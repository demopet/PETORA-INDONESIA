<?php

declare(strict_types=1);

use App\Models\Branch;
use App\Models\DocumentSequence;
use App\Services\DocumentNumberService;
use App\Support\BranchContext;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('scopes document sequences by active branch but allows explicit all-branches reads', function (): void {
    $branchA = Branch::factory()->create(['code' => 'AAA']);
    $branchB = Branch::factory()->create(['code' => 'BBB']);

    DocumentSequence::query()->create([
        'document_type' => 'INV',
        'branch_id' => $branchA->id,
        'period' => '202611',
        'last_number' => 3,
    ]);
    DocumentSequence::query()->create([
        'document_type' => 'INV',
        'branch_id' => $branchB->id,
        'period' => '202611',
        'last_number' => 7,
    ]);
    DocumentSequence::query()->create([
        'document_type' => 'INV',
        'branch_id' => null,
        'period' => '202611',
        'last_number' => 1,
    ]);

    BranchContext::set($branchA->id);
    expect(DocumentSequence::query()->pluck('branch_id')->all())->toBe([$branchA->id]);

    BranchContext::setAllBranches();
    expect(DocumentSequence::query()->count())->toBe(3);
});

it('generates numbers for explicit branch regardless of active branch context', function (): void {
    $branchA = Branch::factory()->create(['code' => 'AAA']);
    $branchB = Branch::factory()->create(['code' => 'BBB']);
    $service = app(DocumentNumberService::class);

    BranchContext::set($branchA->id);
    expect($service->next('INV', $branchB->id, '202611'))->toBe('INV/202611/00001')
        ->and($service->next('INV', $branchB->id, '202611'))->toBe('INV/202611/00002')
        ->and($service->next('INV', $branchA->id, '202611'))->toBe('INV/202611/00001');

    BranchContext::set($branchB->id);
    expect(DocumentSequence::query()->where('document_type', 'INV')->where('period', '202611')->value('last_number'))->toBe(2);

    BranchContext::setAllBranches();
    expect(DocumentSequence::query()->where('document_type', 'INV')->where('period', '202611')->count())->toBe(2);
});
