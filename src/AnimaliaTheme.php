<?php

namespace ThalysJuvenal\FilamentAnimalia;

use Filament\Contracts\Plugin;
use Filament\Panel;

final class AnimaliaTheme implements Plugin
{
    public const PACKAGE = 'thalysjuvenal/filament-animalia-theme';

    public static function make(): static
    {
        return app(self::class);
    }

    public function getId(): string
    {
        return 'animalia-theme';
    }

    public function register(Panel $panel): void {}

    public function boot(Panel $panel): void {}
}
