<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Support\Settings;
use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use UnitEnum;

/**
 * @property-read Schema $form
 */
class ManageSettings extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-adjustments-horizontal';

    protected static string|UnitEnum|null $navigationGroup = 'Platform';

    protected static ?string $navigationLabel = 'Pengaturan';

    protected static ?string $title = 'Pengaturan';

    protected string $view = 'filament.pages.manage-settings';

    /** @var array<string, mixed>|null */
    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'app_name' => Settings::get('app.name', config('app.name')),
            'timezone' => Settings::get('app.timezone', 'Asia/Jakarta'),
            'locale' => Settings::get('app.locale', 'id'),
            'accent_color' => Settings::get('branding.accent_color', 'emerald'),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('app_name')->label('Nama Aplikasi')->required()->maxLength(255),
                Select::make('timezone')->label('Zona Waktu')->options([
                    'Asia/Jakarta' => 'Asia/Jakarta (WIB)',
                    'Asia/Makassar' => 'Asia/Makassar (WITA)',
                    'Asia/Jayapura' => 'Asia/Jayapura (WIT)',
                ])->required(),
                Select::make('locale')->label('Bahasa')->options([
                    'id' => 'Bahasa Indonesia',
                    'en' => 'English',
                ])->required(),
                Select::make('accent_color')->label('Warna Aksen')->options([
                    'emerald' => 'Emerald',
                    'blue' => 'Blue',
                    'amber' => 'Amber',
                    'rose' => 'Rose',
                ])->required(),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();
        Settings::set('app.name', $data['app_name'], 'string', 'branding');
        Settings::set('app.timezone', $data['timezone'], 'string', 'localization');
        Settings::set('app.locale', $data['locale'], 'string', 'localization');
        Settings::set('branding.accent_color', $data['accent_color'], 'string', 'branding');

        app()->setLocale((string) $data['locale']);
        Notification::make()->title('Pengaturan disimpan')->success()->send();
    }

    public static function canAccess(): bool
    {
        return (bool) auth()->user()?->can('platform.settings.view');
    }
}
