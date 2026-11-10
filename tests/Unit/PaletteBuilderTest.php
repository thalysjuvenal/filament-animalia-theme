<?php

use ThalysJuvenal\FilamentAnimalia\Colors\PaletteBuilder;
use ThalysJuvenal\FilamentAnimalia\Tokens\AnimaliaTokens;

it('keeps Animalia anchors exact', function () {
    $p = PaletteBuilder::fromScale(AnimaliaTokens::scale('brand-01'));
    expect(array_keys($p))->toBe([50, 100, 200, 300, 400, 500, 600, 700, 800, 900, 950])
        ->and($p[50])->toBe('#EBEBF5')->and($p[600])->toBe('#4545A1')->and($p[950])->toBe('#030330');
});

it('interpolates missing shades as OKLCH midpoints', function () {
    $p = PaletteBuilder::fromScale(AnimaliaTokens::scale('brand-01'));
    expect($p[100])->toBe('oklch(0.884 0.031 285.711)')
        ->and($p[300])->toBe('oklch(0.762 0.063 284.754)')
        ->and($p[500])->toBe('oklch(0.572 0.111 281.459)')
        ->and($p[900])->toBe('oklch(0.194 0.108 270.137)');
});

it('maps neutrals to gray and interpolates 900', function () {
    $p = PaletteBuilder::fromNeutrals(AnimaliaTokens::neutrals());
    expect($p[50])->toBe('#F9F9FA')->and($p[500])->toBe('#69697C')->and($p[800])->toBe('#1D1D30')
        ->and($p[900])->toBe('oklch(0.185 0.033 282.421)')->and($p[950])->toBe('#060613');
});
