<?php

declare(strict_types=1);

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;
use Spatie\Permission\PermissionRegistrar;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function afterCreate(): void
    {
        /** @var User $record */
        $record = $this->getRecord();

        if (! $record->active_branch_id) {
            $firstBranchId = $record->branches()->value('branches.id');
            if ($firstBranchId) {
                $record->forceFill(['active_branch_id' => $firstBranchId])->save();
            }
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
