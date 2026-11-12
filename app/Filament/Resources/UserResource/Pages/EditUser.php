<?php

declare(strict_types=1);

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\User;
use App\Support\OwnerProtection;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

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

    protected function mutateFormDataBeforeSave(array $data): array
    {
        /** @var User $record */
        $record = $this->getRecord();

        $wasOwner = OwnerProtection::isOwner($record);
        $willBeActive = (bool) ($data['is_active'] ?? $record->is_active);

        if ($wasOwner && $record->is_active && ! $willBeActive && ! OwnerProtection::canDeactivate($record)) {
            Notification::make()->title('Minimal harus ada satu Owner aktif')->danger()->send();
            $this->halt();
        }

        $selectedRoles = $data['roles'] ?? null;
        if (is_array($selectedRoles)) {
            $willHaveOwner = in_array('Owner', $selectedRoles, true);
            if (! $willHaveOwner) {
                // relationship field may pass ids; check by record after save is complex, so ensure via name query fallback
                $ownerIds = Role::query()->where('name', 'Owner')->pluck('id')->map(fn ($id) => (int) $id)->all();
                $willHaveOwner = count(array_intersect(array_map('intval', $selectedRoles), $ownerIds)) > 0;
            }
            if ($wasOwner && ! $willHaveOwner && ! OwnerProtection::canRemoveOwnerRole($record)) {
                Notification::make()->title('Minimal harus ada satu Owner aktif')->danger()->send();
                $this->halt();
            }
        }

        return $data;
    }

    protected function afterSave(): void
    {
        $record = $this->getRecord()->fresh();
        if (! $record instanceof User) {
            return;
        }

        if (! $record->active_branch_id) {
            $firstBranchId = $record->branches()->value('branches.id');
            if ($firstBranchId) {
                $record->forceFill(['active_branch_id' => $firstBranchId])->save();
            }
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
