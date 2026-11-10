<?php

use ThalysJuvenal\FilamentAnimalia\Enums\Density;
use ThalysJuvenal\FilamentAnimalia\Enums\Radius;
use ThalysJuvenal\FilamentAnimalia\Enums\SidebarVariant;
use ThalysJuvenal\FilamentAnimalia\Tokens\SemanticTokens;

it('defines the same token names for light and dark', function (SidebarVariant $v) {
    $c = SemanticTokens::colors($v);
    expect(array_keys($c['light']))->toEqual(array_keys($c['dark']));
})->with(SidebarVariant::cases());

it('only references Animalia anchor shades', function (SidebarVariant $v) {
    $anchors = [50, 200, 400, 600, 700, 800, 950];
    $grayAnchors = [50, 100, 200, 300, 400, 500, 600, 700, 800, 950];
    foreach (SemanticTokens::colors($v) as $mode) {
        foreach ($mode as $value) {
            if (in_array($value, ['white', 'transparent'], true)) {
                continue;
            }
            [$color, $shade] = explode('-', $value);
            expect((int) $shade)->toBeIn($color === 'gray' ? $grayAnchors : $anchors);
        }
    }
})->with(SidebarVariant::cases());

it('uses the deep-indigo sidebar with a dark-mode border', function () {
    $c = SemanticTokens::colors(SidebarVariant::DeepIndigo);
    expect($c['light']['sidebar-bg'])->toBe('primary-950')
        ->and($c['light']['sidebar-border'])->toBe('transparent')
        ->and($c['dark']['sidebar-border'])->toBe('gray-700');
});

it('maps density and radius to Animalia values', function () {
    $d = SemanticTokens::dimensions(Density::Compact, Radius::None);
    expect($d['space-control-y'])->toBe('6px')->and($d['radius-control'])->toBe('0')
        ->and(SemanticTokens::dimensions(Density::Comfortable, Radius::Default)['radius-container'])->toBe('8px')
        ->and($d['shadow-sm'])->toBe('0 1px 4px 0 rgba(0, 0, 0, 0.1), 0 0 3px 0 rgba(0, 0, 0, 0.1)');
});
