<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Models\Branch;
use App\Models\User;
use App\Support\BranchContext;
use App\Support\SampleData;
use App\Support\Settings;
use App\Support\SetupState;
use BackedEnum;
use Database\Seeders\RolePermissionSeeder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
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

    /** @var array<string, array{ok: bool, message: string}> */
    public array $checks = [];

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

        $this->runChecks();

        $this->form->fill([
            'business_name' => Settings::get('app.name', config('app.name', 'PETORA INDONESIA')),
            'timezone' => 'Asia/Jakarta',
            'locale' => 'id',
            'branch_name' => 'Cabang Utama',
            'branch_code' => 'HQ',
            'load_sample_data' => false,
        ]);
    }

    public function runChecks(): void
    {
        $checks = [
            'app' => ['ok' => true, 'message' => 'Aplikasi siap.'],
        ];

        try {
            DB::connection()->getPdo();
            $checks['database'] = ['ok' => true, 'message' => 'Koneksi basis data OK.'];
        } catch (\Throwable $e) {
            $checks['database'] = ['ok' => false, 'message' => 'Basis data tidak tersedia: '.$e->getMessage()];
        }

        $url = (string) config('app.url');
        if ($url !== '' && str_starts_with($url, 'https://')) {
            $checks['public_url'] = ['ok' => true, 'message' => 'APP_URL HTTPS terkonfigurasi (pemeriksaan endpoint publik tersedia di Kesehatan Sistem; bukan uji dari jaringan penyedia webhook).'];
        } else {
            $checks['public_url'] = ['ok' => false, 'message' => 'APP_URL bukan HTTPS publik. Pemeriksaan alamat publik penyedia webhook belum dapat dibuktikan.'];
        }

        $this->checks = $checks;
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Akun Owner')
                    ->schema([
                        TextInput::make('owner_name')->label('Nama Owner')->required()->maxLength(255),
                        TextInput::make('owner_email')->label('Email Owner')->email()->required()->maxLength(255),
                        TextInput::make('owner_password')->label('Kata Sandi')->password()->required()->minLength(8)->revealable(),
                    ])->columns(2),
                Section::make('Profil Bisnis & Cabang')
                    ->schema([
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
                    ])->columns(2),
                Section::make('Opsi')
                    ->schema([
                        Toggle::make('load_sample_data')->label('Muat data contoh setelah selesai (dapat dihapus kemudian)')->default(false),
                    ]),
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

        if (! empty($data['load_sample_data'])) {
            try {
                SampleData::load();
            } catch (\Throwable $e) {
                Notification::make()->title('Pengaturan awal selesai, namun data contoh gagal dimuat')->body($e->getMessage())->warning()->send();
                $this->redirect('/admin');

                return;
            }
        }

        Notification::make()->title('Pengaturan awal selesai')->success()->send();
        $this->redirect('/admin');
    }
}
