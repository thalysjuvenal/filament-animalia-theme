<?php

use Filament\Panel;
use ThalysJuvenal\FilamentAnimalia\AnimaliaTheme;

it('registers Animalia palettes on the panel', function () {
    $panel = Panel::make()->id('t')->plugin(AnimaliaTheme::make());

    expect($panel->getColors()['primary'][600])->toBe('#4545A1')
        ->and($panel->getColors()['gray'][800])->toBe('#1D1D30')
        ->and($panel->getColors()['secondary'][600])->toBe('#8DC897');
});

it('accepts brand-02 and custom hex', function () {
    expect(AnimaliaTheme::make()->primaryColor('brand-02')->getColors()['primary'][600])->toBe('#8DC897')
        ->and(AnimaliaTheme::make()->primaryColor('#FF6600')->getColors()['primary'])->toBe('#FF6600');
});

it('rejects invalid configuration', function (string $method, string $value) {
    AnimaliaTheme::make()->{$method}($value);
})->with([
    'short hex' => ['primaryColor', '#12'],
    'named color' => ['primaryColor', 'blue'],
    'sidebar' => ['sidebar', 'dark'],
    'density' => ['density', 'cozy'],
    'radius' => ['radius', 'round'],
])->throws(InvalidArgumentException::class);

it('lets user colors registered after the plugin win', function () {
    $panel = Panel::make()->id('t')->plugin(AnimaliaTheme::make())->colors(['primary' => '#FF0000']);

    expect($panel->getColors()['primary'])->toBe('#FF0000');
});

it('accepts enums or strings for appearance options', function () {
    $theme = AnimaliaTheme::make()->sidebar('light')->density('compact')->radius('lg');

    expect($theme->getSidebar()->value)->toBe('light')
        ->and($theme->getDensity()->value)->toBe('compact')
        ->and($theme->getRadius()->value)->toBe('lg')
        ->and($theme->getPrimaryColor())->toBe('brand-01');
});

it('omits links but keeps tokens without precompiled CSS', function () {
    $head = AnimaliaTheme::make()->withoutPrecompiledCss()->renderHead();

    expect($head)->toContain('data-animalia-tokens');
    expect($head)->not->toContain('<link');
});
