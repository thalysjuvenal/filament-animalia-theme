<?php

use ThalysJuvenal\FilamentAnimalia\Colors\Contrast;
use ThalysJuvenal\FilamentAnimalia\Colors\PaletteBuilder;
use ThalysJuvenal\FilamentAnimalia\Enums\SidebarVariant;
use ThalysJuvenal\FilamentAnimalia\Tokens\AnimaliaTokens;
use ThalysJuvenal\FilamentAnimalia\Tokens\SemanticTokens;

/**
 * Resolves a semantic token value ('{color}-{shade}' or 'white') to a hex color.
 */
function resolveSemanticColor(string $value): string
{
    if ($value === 'white') {
        return '#FFFFFF';
    }

    if ($value === 'transparent') {
        throw new RuntimeException('A transparent token must never be part of a contrast pair.');
    }

    [$color, $shade] = explode('-', $value) + [1 => null];
    $shade = (int) $shade;

    if ($color === 'gray') {
        return AnimaliaTokens::neutrals()[PaletteBuilder::NEUTRAL_ANCHORS[$shade]];
    }

    $scale = match ($color) {
        'primary' => 'brand-01',
        'success' => 'positive',
        'danger' => 'negative',
        'warning' => 'warning',
        'info' => 'info',
        default => throw new RuntimeException("Unknown color '{$color}' in token value '{$value}'."),
    };

    return AnimaliaTokens::scale($scale)[PaletteBuilder::ANCHORS[$shade]];
}

/**
 * @return array<int, array{0: string, 1: string, 2: float}>
 */
function semanticContrastPairs(): array
{
    $pairs = [];
    $add = function (string $fg, string $bg, float $min) use (&$pairs): void {
        $pairs[] = [$fg, $bg, $min];
    };

    foreach (['text', 'text-muted'] as $fg) {
        foreach (['page-bg', 'surface', 'surface-raised'] as $bg) {
            $add($fg, $bg, 4.5);
        }
    }
    foreach (['surface', 'surface-raised', 'page-bg'] as $bg) {
        $add('link', $bg, 4.5);
    }
    foreach (['primary', 'primary-hover', 'primary-pressed'] as $bg) {
        $add('on-primary', $bg, 4.5);
    }
    foreach (['success', 'danger', 'warning', 'info'] as $c) {
        $add("on-{$c}", $c, 4.5);
        $add("{$c}-text", "{$c}-bg", 4.5);
        $add("{$c}-text", 'surface', 4.5);
    }
    $add('primary-subtle-text', 'primary-subtle-bg', 4.5);
    $add('neutral-subtle-text', 'neutral-subtle-bg', 4.5);
    $add('tooltip-text', 'tooltip-bg', 4.5);
    $add('sidebar-text', 'sidebar-bg', 4.5);
    $add('sidebar-label', 'sidebar-bg', 4.5);
    $add('sidebar-brand', 'sidebar-bg', 4.5);
    $add('sidebar-item-active-text', 'sidebar-item-active-bg', 4.5);
    $add('sidebar-text', 'sidebar-item-hover-bg', 4.5);

    $add('border-input', 'surface', 3.0);
    $add('focus-ring', 'surface', 3.0);
    $add('focus-ring', 'page-bg', 3.0);

    return $pairs;
}

$combinations = [];
foreach (SidebarVariant::cases() as $variant) {
    foreach (['light', 'dark'] as $mode) {
        $combinations["{$variant->name} / {$mode}"] = [$variant, $mode];
    }
}

it('meets WCAG contrast thresholds for every semantic pair', function (SidebarVariant $variant, string $mode) {
    $tokens = SemanticTokens::colors($variant)[$mode];
    $failures = [];

    foreach (semanticContrastPairs() as [$fg, $bg, $min]) {
        expect($tokens)->toHaveKeys([$fg, $bg]);

        $ratio = Contrast::ratio(resolveSemanticColor($tokens[$fg]), resolveSemanticColor($tokens[$bg]));

        if ($ratio < $min) {
            $failures[] = sprintf(
                '%s / %s: %s (%s) on %s (%s) = %.2f < %.1f',
                $variant->name, $mode, $fg, $tokens[$fg], $bg, $tokens[$bg], $ratio, $min,
            );
        }
    }

    expect($failures)->toBe([], "Contrast failures:\n".implode("\n", $failures));
})->with($combinations);
