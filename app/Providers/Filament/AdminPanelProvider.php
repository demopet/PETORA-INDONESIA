<?php

declare(strict_types=1);

namespace App\Providers\Filament;

use App\Filament\Pages\SetupWizard;
use App\Http\Middleware\EnsureSetupCompleted;
use App\Http\Middleware\FilamentAuthenticate;
use App\Http\Middleware\SetPermissionTeamContext;
use App\Models\User;
use App\Support\BranchContext;
use App\Support\MultiFactorPolicy;
use Filament\Auth\MultiFactor\App\AppAuthentication;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Filament\Widgets\AccountWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->passwordReset()
            ->profile()
            ->brandName('PETORA INDONESIA')
            ->colors([
                'primary' => Color::Emerald,
            ])
            ->globalSearch()
            ->sidebarCollapsibleOnDesktop()
            ->multiFactorAuthentication([
                AppAuthentication::make()->recoverable(),
            ], isRequired: function (): bool {
                $user = Auth::user();

                return $user instanceof User && MultiFactorPolicy::isRequiredFor($user);
            })
            ->navigationGroups([
                NavigationGroup::make('Platform'),
                NavigationGroup::make('Akses'),
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Dashboard::class,
                SetupWizard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                AccountWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
                SetPermissionTeamContext::class,
                EnsureSetupCompleted::class,
            ])
            ->authMiddleware([
                FilamentAuthenticate::class,
            ], isPersistent: true)
            ->renderHook(
                PanelsRenderHook::FOOTER,
                fn (): string => Blade::render("@include('filament.hooks.attribution')")
            )
            ->renderHook(
                PanelsRenderHook::USER_MENU_BEFORE,
                fn (): string => $this->renderBranchSwitcher()
            );
    }

    protected function renderBranchSwitcher(): string
    {
        $user = Auth::user();
        if (! $user instanceof User) {
            return '';
        }

        try {
            $branches = $user->branches()->where('is_active', true)->orderBy('name')->get();
        } catch (\Throwable) {
            return '';
        }

        if ($branches->count() < 1) {
            return '';
        }

        $currentId = BranchContext::currentId();

        return Blade::render('filament.hooks.branch-switcher', [
            'branches' => $branches,
            'currentId' => $currentId,
        ]);
    }
}
