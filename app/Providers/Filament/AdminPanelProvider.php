<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Login;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Filament\Widgets\AccountWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Blade;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('')
            ->path('')
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->login(Login::class)
            ->colors([
                'primary' => Color::Lime,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->brandLogo(fn () => view('filament.partials.brand'))
            ->brandLogoHeight('3rem')
            ->brandName('')
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
            ])
            ->favicon(asset('favicon.ico'))
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->renderHook(
                PanelsRenderHook::SIDEBAR_NAV_START,
                fn (): string => Blade::render('@livewire(\'period-selector\')') 
            )
            ->renderHook(
                PanelsRenderHook::SIDEBAR_NAV_END,
                fn () => view('filament.partials.logout')
            )
            ->renderHook(
                PanelsRenderHook::BODY_END,
                fn (): string => Blade::render(<<<'HTML'
                    <style>
                        :root {
                            --sidebar-width: 18rem!important;
                        }

                        .fi-ta-cell:has(.sticky-col-name) {
                            position: sticky !important;
                            left: 0 !important;
                            z-index: 10 !important;
                            background-color: white !important; 
                        }
                        
                        .dark .fi-ta-cell:has(.sticky-col-name) {
                            background-color: rgb(24, 24, 27) !important; 
                        }

                        .fi-ta-cell .sticky-col-name .fi-ta-text-description {
                            font-size: smaller;
                            margin-top: -0.3rem;
                        }
                    </style>
                HTML)
            );
    }
}
