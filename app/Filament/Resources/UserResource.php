<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\Branch;
use App\Models\User;
use App\Support\BranchRoleSync;
use App\Support\OwnerProtection;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Exceptions\Halt;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use UnitEnum;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-users';

    protected static string|UnitEnum|null $navigationGroup = 'Akses';

    protected static ?string $navigationLabel = 'Pengguna';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Profil Pengguna')
                ->schema([
                    TextInput::make('name')->required()->maxLength(255),
                    TextInput::make('email')->email()->required()->maxLength(255)->unique(ignoreRecord: true),
                    TextInput::make('phone')->maxLength(32),
                    TextInput::make('password')
                        ->password()
                        ->dehydrateStateUsing(fn ($state) => filled($state) ? Hash::make($state) : null)
                        ->dehydrated(fn ($state) => filled($state))
                        ->required(fn (string $context): bool => $context === 'create')
                        ->maxLength(255),
                    Toggle::make('is_active')->default(true),
                    Select::make('active_branch_id')
                        ->label('Cabang Aktif')
                        ->relationship('activeBranch', 'name')
                        ->searchable()
                        ->preload()
                        ->nullable(),
                ])->columns(2),
            Section::make('Cabang & Peran')
                ->description('Penugasan peran bersifat per cabang (konteks tim). Pilih cabang yang dapat diakses pengguna dan tetapkan perannya di tiap cabang.')
                ->schema([
                    Select::make('branches')
                        ->label('Akses Cabang')
                        ->relationship('branches', 'name')
                        ->multiple()
                        ->preload()
                        ->searchable()
                        ->helperText('Pengguna hanya dapat bekerja pada cabang yang dipilih.'),
                    Repeater::make('branch_roles')
                        ->label('Peran per Cabang')
                        ->schema([
                            Select::make('branch_id')
                                ->label('Cabang')
                                ->options(fn (): array => Branch::query()->where('is_active', true)->orderBy('name')->pluck('name', 'id')->all())
                                ->required()
                                ->searchable()
                                ->disableOptionsWhenSelectedInSiblingRepeaterItems(),
                            Select::make('roles')
                                ->label('Peran')
                                ->options(fn (): array => Role::query()->where('guard_name', 'web')->orderBy('name')->pluck('name', 'name')->all())
                                ->multiple()
                                ->preload()
                                ->searchable(),
                        ])
                        ->columns(2)
                        ->reorderable(false)
                        ->addActionLabel('Tambah Penugasan Cabang')
                        ->helperText('Simpan peran per cabang. Untuk cabang tanpa entri, peran akan dikosongkan.'),
                ])->columns(1),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('email')->searchable(),
                IconColumn::make('is_active')->boolean(),
                TextColumn::make('activeBranch.name')->label('Cabang Aktif')->toggleable(),
                TextColumn::make('branches.name')->label('Cabang')->badge()->separator(',')->toggleable(),
                TextColumn::make('roles_summary')
                    ->label('Peran')
                    ->state(fn (User $record): string => self::rolesSummary($record))
                    ->wrap()
                    ->toggleable(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make()
                    ->before(function (User $record): void {
                        if (! OwnerProtection::canDelete($record)) {
                            Notification::make()->title('Minimal harus ada satu Owner aktif')->danger()->send();
                            throw new Halt;
                        }
                    })
                    ->visible(fn (User $record): bool => auth()->id() !== $record->id),
            ]);
    }

    protected static function rolesSummary(User $record): string
    {
        $rows = BranchRoleSync::rolesByBranch($record);
        if ($rows === []) {
            return '-';
        }

        $branchNames = Branch::query()->whereIn('id', array_keys($rows))->pluck('name', 'id');
        $parts = [];
        foreach ($rows as $branchId => $roles) {
            $name = $branchNames[$branchId] ?? ('#'.$branchId);
            $parts[] = $name.': '.(implode(', ', $roles) ?: '-');
        }

        return implode(' | ', $parts);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return (bool) auth()->user()?->can('platform.users.view');
    }
}
