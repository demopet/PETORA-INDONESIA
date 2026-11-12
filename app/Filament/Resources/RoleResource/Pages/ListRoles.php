<?php

declare(strict_types=1);

namespace App\Filament\Resources\RoleResource\Pages;

use App\Filament\Resources\RoleResource;
use Filament\Actions;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Spatie\Permission\Models\Role;

class ListRoles extends ListRecords
{
    protected static string $resource = RoleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('cloneRole')
                ->label('Klon Peran')
                ->icon('heroicon-o-document-duplicate')
                ->form([
                    Select::make('source_role_id')
                        ->label('Peran Sumber')
                        ->options(fn () => Role::query()->where('guard_name', 'web')->pluck('name', 'id'))
                        ->required()
                        ->searchable(),
                    TextInput::make('new_name')
                        ->label('Nama Peran Baru')
                        ->required()
                        ->maxLength(255),
                ])
                ->action(function (array $data): void {
                    $source = Role::query()->findOrFail($data['source_role_id']);
                    $new = Role::query()->firstOrCreate([
                        'name' => $data['new_name'],
                        'guard_name' => $source->guard_name,
                    ]);
                    $new->syncPermissions($source->permissions()->pluck('name')->all());

                    Notification::make()
                        ->title('Peran berhasil diklon')
                        ->success()
                        ->send();
                })
                ->visible(fn (): bool => (bool) auth()->user()?->can('platform.roles.manage')),
        ];
    }
}
