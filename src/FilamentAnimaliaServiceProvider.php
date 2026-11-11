<?php

namespace ThalysJuvenal\FilamentAnimalia;

use Filament\Support\Assets\Css;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Support\ServiceProvider;
use ThalysJuvenal\FilamentAnimalia\Enums\RestyleGroup;

class FilamentAnimaliaServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Loaded on request: only the plugin's render hook emits the link,
        // so panels without the plugin are not affected.
        $assets = [
            Css::make('animalia-core', __DIR__.'/../resources/css/core.css')->loadedOnRequest(),
        ];

        foreach (RestyleGroup::cases() as $group) {
            $assets[] = Css::make($group->assetId(), __DIR__.'/../'.$group->stylesheetPath())->loadedOnRequest();
        }

        FilamentAsset::register($assets, AnimaliaTheme::PACKAGE);
    }
}
