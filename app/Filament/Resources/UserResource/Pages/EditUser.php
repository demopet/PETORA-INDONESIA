<?php

declare(strict_types=1);

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\User;
use App\Support\BranchRoleSync;
use App\Support\OwnerProtection;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Spatie\Permission\PermissionRegistrar;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    /** @var array<int, array{branch_id?: mixed, roles?: array<mixed>}> */
    protected array $pendingBranchRoles = [];

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->before(function (User $record): void {
                    if (! OwnerProtection::canDelete($record)) {
                        Notification::make()->title('Minimal harus ada satu Owner aktif')->danger()->send();
                        $this->halt();
                    }
                })
                ->visible(fn (): bool => auth()->id() !== $this->getRecord()->getKey()),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        /** @var User $record */
        $record = $this->getRecord();
        $rolesByBranch = BranchRoleSync::rolesByBranch($record);
        $items = [];
        foreach ($rolesByBranch as $branchId => $roles) {
            $items[] = [
                'branch_id' => $branchId,
                'roles' => $roles,
            ];
        }
        usort($items, fn (array $a, array $b): int => ((int) $a['branch_id']) <=> ((int) $b['branch_id']));
        $data['branch_roles'] = $items;

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        /** @var User $record */
        $record = $this->getRecord();

        $wasOwner = OwnerProtection::isOwner($record);
        $willBeActive = (bool) ($data['is_active'] ?? $record->is_active);

        $this->pendingBranchRoles = is_array($data['branch_roles'] ?? null) ? $data['branch_roles'] : [];
        unset($data['branch_roles']);

        $willHaveOwner = $this->willHaveOwnerAfterSave($record);

        if ($wasOwner && $record->is_active && ! $willBeActive && ! OwnerProtection::canDeactivate($record)) {
            Notification::make()->title('Minimal harus ada satu Owner aktif')->danger()->send();
            $this->halt();
        }

        if ($wasOwner && ! $willHaveOwner && ! OwnerProtection::canRemoveOwnerRole($record)) {
            Notification::make()->title('Minimal harus ada satu Owner aktif')->danger()->send();
            $this->halt();
        }

        return $data;
    }

    protected function afterSave(): void
    {
        /** @var User $record */
        $record = $this->getRecord()->fresh() ?? $this->getRecord();

        $branchIds = $record->branches()->pluck('branches.id')->map(fn ($id) => (int) $id)->all();

        if ($record->active_branch_id && ! in_array((int) $record->active_branch_id, $branchIds, true)) {
            $record->forceFill(['active_branch_id' => $branchIds[0] ?? null])->save();
            $record = $record->fresh() ?? $record;
        }

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

        // Also clear roles for branches the user no longer has access to.
        $existingByBranch = BranchRoleSync::rolesByBranch($record->fresh() ?? $record);
        foreach (array_keys($existingByBranch) as $branchId) {
            if (! in_array((int) $branchId, $branchIds, true)) {
                BranchRoleSync::syncRolesForBranch($record, (int) $branchId, []);
            }
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    protected function willHaveOwnerAfterSave(User $record): bool
    {
        $branchIds = $this->effectiveBranchIdsAfterSave($record);
        $assignments = $this->normalizedAssignments();
        foreach ($branchIds as $branchId) {
            $roles = $assignments[$branchId] ?? [];
            if (in_array('Owner', $roles, true)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return list<int>
     */
    protected function effectiveBranchIdsAfterSave(User $record): array
    {
        $state = $this->form->getRawState();
        $branches = $state['branches'] ?? null;
        if (is_array($branches) && $branches !== []) {
            return array_values(array_unique(array_map('intval', $branches)));
        }

        return $record->branches()->pluck('branches.id')->map(fn ($id) => (int) $id)->values()->all();
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
