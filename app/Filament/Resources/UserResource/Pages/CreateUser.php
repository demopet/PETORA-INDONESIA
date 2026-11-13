<?php

declare(strict_types=1);

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\User;
use App\Support\BranchRoleSync;
use Filament\Resources\Pages\CreateRecord;
use Spatie\Permission\PermissionRegistrar;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    /** @var array<int, array{branch_id?: mixed, roles?: array<mixed>}> */
    protected array $pendingBranchRoles = [];

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->pendingBranchRoles = is_array($data['branch_roles'] ?? null) ? $data['branch_roles'] : [];
        unset($data['branch_roles']);

        $willHaveOwner = false;
        foreach ($this->pendingBranchRoles as $item) {
            $roles = array_map('strval', (array) ($item['roles'] ?? []));
            if (in_array('Owner', $roles, true)) {
                $willHaveOwner = true;
                break;
            }
        }

        // New user: only block if this is the very first owner situation is not applicable, but keep consistent.
        // No existing owner state to protect on create; allow.
        unset($willHaveOwner);

        return $data;
    }

    protected function afterCreate(): void
    {
        /** @var User $record */
        $record = $this->getRecord();

        $branchIds = $record->branches()->pluck('branches.id')->map(fn ($id) => (int) $id)->all();

        if (! $record->active_branch_id) {
            $firstBranchId = $branchIds[0] ?? null;
            if ($firstBranchId) {
                $record->forceFill(['active_branch_id' => $firstBranchId])->save();
            }
        }

        $assignments = $this->normalizedAssignments();
        foreach ($branchIds as $branchId) {
            $roles = $assignments[$branchId] ?? [];
            BranchRoleSync::syncRolesForBranch($record, $branchId, $roles);
        }

        // If assignments were provided for branches not yet attached, ignore them (must be in access list).
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    /**
     * @return array<int, list<string>>
     */
    protected function normalizedAssignments(): array
    {
        $out = [];
        foreach ($this->pendingBranchRoles as $item) {
            $branchId = (int) ($item['branch_id'] ?? 0);
            if ($branchId <= 0) {
                continue;
            }
            $roles = array_values(array_unique(array_filter(array_map('strval', (array) ($item['roles'] ?? [])))));
            sort($roles);
            $out[$branchId] = $roles;
        }

        return $out;
    }
}
