<?php

namespace Workbench\App\Providers;

use Filament\Facades\Filament;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\Widgets\AccountWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use ThalysJuvenal\FilamentAnimalia\AnimaliaTheme;
use Workbench\App\Models\User;

class WorkbenchPanelProvider extends ServiceProvider
{
    public function register(): void
    {
        config(['auth.providers.users.model' => User::class]);

        Filament::registerPanel(fn (): Panel => $this->demoPanel('admin', AnimaliaTheme::make())->default());
        Filament::registerPanel(fn (): Panel => $this->demoPanel('light', AnimaliaTheme::make()->sidebar('light')));
        Filament::registerPanel(fn (): Panel => $this->demoPanel('indigo', AnimaliaTheme::make()->sidebar('indigo')));
        Filament::registerPanel(fn (): Panel => $this->demoPanel('plain', null));
    }

    private function demoPanel(string $id, ?AnimaliaTheme $theme): Panel
    {
        $panel = Panel::make()
            ->id($id)
            ->path($id)
            ->login()
            ->discoverResources(in: dirname(__DIR__).'/Filament/Resources', for: 'Workbench\\App\\Filament\\Resources')
            ->discoverPages(in: dirname(__DIR__).'/Filament/Pages', for: 'Workbench\\App\\Filament\\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->widgets([
                AccountWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                class_exists(PreventRequestForgery::class) ? PreventRequestForgery::class : VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);

        if ($theme !== null) {
            $panel->plugin($theme);
        }

        return $panel;
    }
}
