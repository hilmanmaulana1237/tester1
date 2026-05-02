<?php

namespace App\Providers\Filament;

use Filament\Panel;
use Filament\PanelProvider;
use App\Filament\Pages\Dashboard;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use App\Http\Middleware\AdminMiddleware;
use Filament\Widgets\FilamentInfoWidget;
use Filament\Http\Middleware\Authenticate;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Filament\Http\Middleware\AuthenticateSession;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\Facades\Blade;

class AdminPanelProvider extends PanelProvider
{
    public function boot(): void
    {
        // Inject Echo via render hook di Filament
        FilamentView::registerRenderHook(
            PanelsRenderHook::BODY_END,
            fn(): string => Blade::render('@vite("resources/js/app.js")')
        );
    }

    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->favicon(asset('favicon.svg'))
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->colors([
                'primary' => Color::hex('#af1ddbff'), // Custom blue color
                'danger' => Color::Rose,
                'success' => Color::Emerald,
                'warning' => Color::Orange,
                'info' => Color::Sky,
            ])
            ->navigationGroups([
                NavigationGroup::make()
                    ->label('Manajemen Tugas')
                    ->icon('heroicon-o-clipboard-document-list')
                    ->collapsed(false),
                NavigationGroup::make()
                    ->label('Master Data')
                    ->icon('heroicon-o-circle-stack')
                    ->collapsed(false),
                NavigationGroup::make()
                    ->label('Manajemen User')
                    ->icon('heroicon-o-user-group')
                    ->collapsed(false),
            ])
            ->navigationItems([
                NavigationItem::make('Support Chat')
                    ->url(fn () => route('admin.support-chat'))
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->group('Manajemen User')
                    ->visible(fn () => auth()->user()?->isAdmin()),
            ])
            ->sidebarCollapsibleOnDesktop()
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
            ])
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
                AdminMiddleware::class,
            ]);
    }
}
