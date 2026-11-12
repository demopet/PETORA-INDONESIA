<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Models\Branch;
use App\Models\User;
use App\Support\BranchContext;
use App\Support\Settings;
use App\Support\SetupState;
use BackedEnum;
use Database\Seeders\RolePermissionSeeder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * @property-read Schema $form
 */
class SetupWizard extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $slug = 'setup';

    protected string $view = 'filament.pages.setup-wizard';

    /** @var array<string, mixed>|null */
    public ?array $data = [];

    public static function canAccess(): bool
    {
        return true;
    }

    public function mount(): void
    {
        if (SetupState::isCompleted()) {
            $this->redirect('/admin');

            return;
        }

        $this->form->fill([
            'business_name' => Settings::get('app.name', config('app.name', 'PETORA INDONESIA')),
            'timezone' => 'Asia/Jakarta',
            'locale' => 'id',
            'branch_name' => 'Cabang Utama',
            'branch_code' => 'HQ',
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('owner_name')->label('Nama Owner')->required()->maxLength(255),
                TextInput::make('owner_email')->label('Email Owner')->email()->required()->maxLength(255),
                TextInput::make('owner_password')->label('Kata Sandi')->password()->required()->minLength(8),
                TextInput::make('business_name')->label('Nama Bisnis')->required()->maxLength(255),
                TextInput::make('branch_name')->label('Nama Cabang Pertama')->required()->maxLength(255),
                TextInput::make('branch_code')->label('Kode Cabang')->required()->maxLength(32),
                Select::make('timezone')->label('Zona Waktu')->options([
                    'Asia/Jakarta' => 'Asia/Jakarta (WIB)',
                    'Asia/Makassar' => 'Asia/Makassar (WITA)',
                    'Asia/Jayapura' => 'Asia/Jayapura (WIT)',
                ])->required(),
                Select::make('locale')->label('Bahasa')->options([
                    'id' => 'Bahasa Indonesia',
                    'en' => 'English',
                ])->required(),
            ])
            ->statePath('data');
    }

    public function submit(): void
    {
        $data = $this->form->getState();

        DB::transaction(function () use ($data): void {
            if (Role::query()->where('name', 'Owner')->where('guard_name', 'web')->doesntExist()) {
                app(RolePermissionSeeder::class)->run();
            }

            $branch = Branch::query()->create([
                'name' => $data['branch_name'],
                'code' => strtoupper((string) $data['branch_code']),
                'timezone' => $data['timezone'],
                'is_active' => true,
            ]);

            $user = User::query()->firstOrNew(['email' => $data['owner_email']]);
            $user->name = $data['owner_name'];
            $user->password = Hash::make((string) $data['owner_password']);
            $user->is_active = true;
            $user->active_branch_id = $branch->id;
            $user->save();

            $user->branches()->syncWithoutDetaching([$branch->id => ['is_primary' => true]]);

            $registrar = app(PermissionRegistrar::class);
            $registrar->setPermissionsTeamId($branch->id);
            $registrar->forgetCachedPermissions();

            if (! $user->hasRole('Owner')) {
                $user->assignRole('Owner');
            }

            Settings::set('app.name', $data['business_name'], 'string', 'branding');
            Settings::set('app.timezone', $data['timezone'], 'string', 'localization');
            Settings::set('app.locale', $data['locale'], 'string', 'localization');
            SetupState::markCompleted();

            BranchContext::set($branch->id);
            Auth::login($user);
        });

        Notification::make()->title('Pengaturan awal selesai')->success()->send();
        $this->redirect('/admin');
    }
}
