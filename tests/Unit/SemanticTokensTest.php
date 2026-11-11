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

dataset('sidebar variants', [
    'light' => [SidebarVariant::Light, [
        'light' => [
            'sidebar-bg' => 'white',
            'sidebar-text' => 'gray-500',
            'sidebar-label' => 'gray-500',
            'sidebar-brand' => 'primary-600',
            'sidebar-item-hover-bg' => 'gray-100',
            'sidebar-item-active-bg' => 'primary-50',
            'sidebar-item-active-text' => 'primary-600',
            'sidebar-border' => 'gray-100',
        ],
        'dark' => [
            'sidebar-bg' => 'gray-950',
            'sidebar-text' => 'gray-300',
            'sidebar-label' => 'gray-400',
            'sidebar-brand' => 'primary-200',
            'sidebar-item-hover-bg' => 'gray-800',
            'sidebar-item-active-bg' => 'primary-800',
            'sidebar-item-active-text' => 'primary-200',
            'sidebar-border' => 'gray-700',
        ],
    ]],
    'indigo' => [SidebarVariant::Indigo, [
        'light' => [
            'sidebar-bg' => 'primary-600',
            'sidebar-text' => 'primary-50',
            'sidebar-label' => 'primary-200',
            'sidebar-brand' => 'white',
            'sidebar-item-hover-bg' => 'primary-700',
            'sidebar-item-active-bg' => 'primary-800',
            'sidebar-item-active-text' => 'white',
            'sidebar-border' => 'transparent',
        ],
        'dark' => [
            'sidebar-bg' => 'primary-600',
            'sidebar-text' => 'primary-50',
            'sidebar-label' => 'primary-200',
            'sidebar-brand' => 'white',
            'sidebar-item-hover-bg' => 'primary-700',
            'sidebar-item-active-bg' => 'primary-800',
            'sidebar-item-active-text' => 'white',
            'sidebar-border' => 'transparent',
        ],
    ]],
    'deep-indigo' => [SidebarVariant::DeepIndigo, [
        'light' => [
            'sidebar-bg' => 'primary-950',
            'sidebar-text' => 'primary-200',
            'sidebar-label' => 'primary-400',
            'sidebar-brand' => 'white',
            'sidebar-item-hover-bg' => 'primary-800',
            'sidebar-item-active-bg' => 'primary-600',
            'sidebar-item-active-text' => 'white',
            'sidebar-border' => 'transparent',
        ],
        'dark' => [
            'sidebar-bg' => 'primary-950',
            'sidebar-text' => 'primary-200',
            'sidebar-label' => 'primary-400',
            'sidebar-brand' => 'white',
            'sidebar-item-hover-bg' => 'primary-800',
            'sidebar-item-active-bg' => 'primary-600',
            'sidebar-item-active-text' => 'white',
            'sidebar-border' => 'gray-700',
        ],
    ]],
]);

it('pins every sidebar token per variant and mode', function (SidebarVariant $variant, array $expected) {
    $colors = SemanticTokens::colors($variant);

    foreach (['light', 'dark'] as $mode) {
        $sidebar = array_filter(
            $colors[$mode],
            fn (string $name): bool => str_starts_with($name, 'sidebar-'),
            ARRAY_FILTER_USE_KEY,
        );

        expect($sidebar)->toBe($expected[$mode]);
    }
})->with('sidebar variants');

dataset('dimension combinations', [
    'comfortable + none' => [Density::Comfortable, Radius::None, [
        'radius-control' => '0',
        'radius-container' => '0',
        'space-control-y' => '8px',
        'space-control-x' => '16px',
        'space-cell-y' => '12px',
        'space-cell-x' => '16px',
        'space-section' => '1.5rem',
    ]],
    'comfortable + sm' => [Density::Comfortable, Radius::Sm, [
        'radius-control' => '2px',
        'radius-container' => '2px',
        'space-control-y' => '8px',
        'space-control-x' => '16px',
        'space-cell-y' => '12px',
        'space-cell-x' => '16px',
        'space-section' => '1.5rem',
    ]],
    'comfortable + default' => [Density::Comfortable, Radius::Default, [
        'radius-control' => '4px',
        'radius-container' => '8px',
        'space-control-y' => '8px',
        'space-control-x' => '16px',
        'space-cell-y' => '12px',
        'space-cell-x' => '16px',
        'space-section' => '1.5rem',
    ]],
    'comfortable + lg' => [Density::Comfortable, Radius::Lg, [
        'radius-control' => '8px',
        'radius-container' => '8px',
        'space-control-y' => '8px',
        'space-control-x' => '16px',
        'space-cell-y' => '12px',
        'space-cell-x' => '16px',
        'space-section' => '1.5rem',
    ]],
    'compact + none' => [Density::Compact, Radius::None, [
        'radius-control' => '0',
        'radius-container' => '0',
        'space-control-y' => '6px',
        'space-control-x' => '12px',
        'space-cell-y' => '8px',
        'space-cell-x' => '12px',
        'space-section' => '1rem',
    ]],
    'compact + sm' => [Density::Compact, Radius::Sm, [
        'radius-control' => '2px',
        'radius-container' => '2px',
        'space-control-y' => '6px',
        'space-control-x' => '12px',
        'space-cell-y' => '8px',
        'space-cell-x' => '12px',
        'space-section' => '1rem',
    ]],
    'compact + default' => [Density::Compact, Radius::Default, [
        'radius-control' => '4px',
        'radius-container' => '8px',
        'space-control-y' => '6px',
        'space-control-x' => '12px',
        'space-cell-y' => '8px',
        'space-cell-x' => '12px',
        'space-section' => '1rem',
    ]],
    'compact + lg' => [Density::Compact, Radius::Lg, [
        'radius-control' => '8px',
        'radius-container' => '8px',
        'space-control-y' => '6px',
        'space-control-x' => '12px',
        'space-cell-y' => '8px',
        'space-cell-x' => '12px',
        'space-section' => '1rem',
    ]],
]);

it('pins radius and density dimensions for every combination', function (Density $density, Radius $radius, array $expected) {
    $dimensions = SemanticTokens::dimensions($density, $radius);

    foreach ($expected as $token => $value) {
        expect($dimensions[$token])->toBe($value, $token);
    }

    expect($dimensions['radius-pill'])->toBe('400px')
        ->and($dimensions['border-width-control'])->toBe('1px')
        ->and($dimensions['border-width-button'])->toBe('2px');
})->with('dimension combinations');
