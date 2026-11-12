<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\ActivityLogResource\Pages;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Spatie\Activitylog\Models\Activity;
use UnitEnum;

class ActivityLogResource extends Resource
{
    protected static ?string $model = Activity::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static string|UnitEnum|null $navigationGroup = 'Platform';

    protected static ?string $navigationLabel = 'Log Audit';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')->label('Waktu')->dateTime()->sortable(),
                TextColumn::make('log_name')->label('Log')->badge()->toggleable(),
                TextColumn::make('event')->label('Peristiwa')->badge(),
                TextColumn::make('description')->label('Deskripsi')->wrap()->limit(80),
                TextColumn::make('causer.name')->label('Pelaku')->placeholder('-'),
                TextColumn::make('subject_type')->label('Subjek')->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('subject_id')->label('ID Subjek')->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListActivityLogs::route('/'),
        ];
    }

    public static function canViewAny(): bool
    {
        return (bool) auth()->user()?->can('platform.audit.view');
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
