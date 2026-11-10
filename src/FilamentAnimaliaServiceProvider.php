<?php

namespace ThalysJuvenal\FilamentAnimalia;

use Filament\Support\Assets\Css;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Support\ServiceProvider;

class FilamentAnimaliaServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Loaded on request: only the plugin's render hook emits the link,
        // so panels without the plugin are not affected.
        FilamentAsset::register([
            Css::make('animalia-core', __DIR__.'/../resources/css/core.css')->loadedOnRequest(),
        ], AnimaliaTheme::PACKAGE);
    }
}
