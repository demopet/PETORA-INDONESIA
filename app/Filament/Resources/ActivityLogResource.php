<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\ActivityLogResource\Pages;
use BackedEnum;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
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
                TextColumn::make('created_at')->label('Waktu')->dateTime()->sortable()->searchable(),
                TextColumn::make('log_name')->label('Log')->badge()->sortable()->searchable(),
                TextColumn::make('event')->label('Peristiwa')->badge()->sortable()->searchable(),
                TextColumn::make('description')->label('Deskripsi')->wrap()->limit(80)->searchable(),
                TextColumn::make('causer.name')->label('Pelaku')->placeholder('-')->searchable(),
                TextColumn::make('subject_type')->label('Subjek')->formatStateUsing(fn (?string $state): string => $state ? class_basename($state) : '-')->searchable()->toggleable(),
                TextColumn::make('subject_id')->label('ID Subjek')->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('log_name')
                    ->label('Log')
                    ->options(fn (): array => Activity::query()->whereNotNull('log_name')->distinct()->orderBy('log_name')->pluck('log_name', 'log_name')->all()),
                SelectFilter::make('event')
                    ->label('Peristiwa')
                    ->options(fn (): array => Activity::query()->whereNotNull('event')->distinct()->orderBy('event')->pluck('event', 'event')->all()),
                Filter::make('created_at')
                    ->form([
                        DatePicker::make('from')->label('Dari'),
                        DatePicker::make('until')->label('Sampai'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['from'] ?? null, fn (Builder $q, $date): Builder => $q->whereDate('created_at', '>=', $date))
                            ->when($data['until'] ?? null, fn (Builder $q, $date): Builder => $q->whereDate('created_at', '<=', $date));
                    }),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                ViewAction::make(),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Detail Log')
                ->schema([
                    TextEntry::make('created_at')->label('Waktu')->dateTime(),
                    TextEntry::make('log_name')->label('Log')->placeholder('-'),
                    TextEntry::make('event')->label('Peristiwa')->placeholder('-'),
                    TextEntry::make('description')->label('Deskripsi')->placeholder('-')->columnSpanFull(),
                    TextEntry::make('causer.name')->label('Pelaku')->placeholder('-'),
                    TextEntry::make('causer_type')->label('Tipe Pelaku')->formatStateUsing(fn (?string $state): string => $state ? class_basename($state) : '-')->placeholder('-'),
                    TextEntry::make('causer_id')->label('ID Pelaku')->placeholder('-'),
                    TextEntry::make('subject_type')->label('Tipe Subjek')->formatStateUsing(fn (?string $state): string => $state ? class_basename($state) : '-')->placeholder('-'),
                    TextEntry::make('subject_id')->label('ID Subjek')->placeholder('-'),
                ])->columns(2),
            Section::make('Properti')
                ->schema([
                    KeyValueEntry::make('properties')
                        ->label('Data')
                        ->state(fn (Activity $record): array => self::normalizeProperties($record))
                        ->columnSpanFull(),
                ]),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    protected static function normalizeProperties(Activity $record): array
    {
        $properties = $record->properties?->toArray() ?? [];
        $flat = [];

        foreach ($properties as $key => $value) {
            if (is_array($value)) {
                $flat[(string) $key] = json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            } elseif (is_bool($value)) {
                $flat[(string) $key] = $value ? 'true' : 'false';
            } elseif ($value === null) {
                $flat[(string) $key] = 'null';
            } else {
                $flat[(string) $key] = (string) $value;
            }
        }

        return $flat === [] ? ['info' => 'Tidak ada properti.'] : $flat;
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

    public static function getGloballySearchableAttributes(): array
    {
        return ['description', 'log_name', 'event'];
    }
}
