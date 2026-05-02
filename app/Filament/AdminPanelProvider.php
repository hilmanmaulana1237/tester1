<?php

namespace App\Filament;

use Filament\Panel;
use Filament\PanelProvider;
use App\Filament\Pages\Dashboard;
use Filament\Widgets\AccountWidget;
use App\Filament\Resources\Categories\CategoryResource;
use App\Filament\Resources\UserTasks\UserTaskResource;
use Filament\Support\Colors\Color;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('admin')
            ->path('admin')
            ->login()
            ->colors([
                'primary' => Color::Blue,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                AccountWidget::class,
            ])
            ->middleware([
                'auth',
            ])
            ->authGuard('web')
            // Force light mode only
            ->darkMode(false);
    }
}
