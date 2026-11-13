<?php

declare(strict_types=1);

namespace App\Filament\Resources\RoleResource\Pages;

use App\Filament\Resources\RoleResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Spatie\Permission\Models\Role;

class EditRole extends EditRecord
{
    protected static string $resource = RoleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->hidden(fn (): bool => $this->getRecord() instanceof Role && $this->getRecord()->name === 'Owner'),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $record = $this->getRecord();

        if ($record instanceof Role && $record->name === 'Owner' && ($data['name'] ?? 'Owner') !== 'Owner') {
            Notification::make()
                ->title('Peran Owner tidak dapat diubah namanya')
                ->danger()
                ->send();

            $this->halt();
        }

        return $data;
    }
}
