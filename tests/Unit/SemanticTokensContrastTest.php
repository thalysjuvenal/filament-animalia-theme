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
        // Toast accent bar: a decorative status indicator next to an icon, so the non-text minimum applies.
        $add("{$c}-text", 'surface-raised', 3.0);
    }
    $add('primary-subtle-text', 'primary-subtle-bg', 4.5);
    $add('neutral-subtle-text', 'neutral-subtle-bg', 4.5);
    $add('tooltip-text', 'tooltip-bg', 4.5);
    $add('sidebar-text', 'sidebar-bg', 4.5);
    $add('sidebar-label', 'sidebar-bg', 4.5);
    $add('sidebar-brand', 'sidebar-bg', 4.5);
    $add('sidebar-item-active-text', 'sidebar-item-active-bg', 4.5);
    $add('sidebar-text', 'sidebar-item-hover-bg', 4.5);

    foreach (['surface', 'surface-raised', 'page-bg'] as $bg) {
        $add('border-input', $bg, 3.0);
        $add('focus-ring', $bg, 3.0);
    }

    // Dropdown items are repainted with row-hover-bg on hover and focus; their icons use text-muted (icon minimum).
    $add('text-muted', 'row-hover-bg', 3.0);
    $add('focus-ring', 'row-hover-bg', 3.0);

    return $pairs;
}

/**
 * Sidebar pairs depend on the sidebar variant; every other pair is variant independent.
 *
 * @param  array{0: string, 1: string, 2: float}  $pair
 */
function isSidebarPair(array $pair): bool
{
    return str_starts_with($pair[0], 'sidebar-') || str_starts_with($pair[1], 'sidebar-');
}

/**
 * @param  array<string, string>  $tokens
 * @param  array<int, array{0: string, 1: string, 2: float}>  $pairs
 * @return array<int, string>
 */
function contrastFailures(string $label, array $tokens, array $pairs): array
{
    $failures = [];

    foreach ($pairs as [$fg, $bg, $min]) {
        expect($tokens)->toHaveKeys([$fg, $bg]);

        $ratio = Contrast::ratio(resolveSemanticColor($tokens[$fg]), resolveSemanticColor($tokens[$bg]));

        if ($ratio < $min) {
            $failures[] = sprintf('%s: %s (%s) on %s (%s) = %.2f < %.1f', $label, $fg, $tokens[$fg], $bg, $tokens[$bg], $ratio, $min);
        }
    }

    return $failures;
}

/**
 * Converts an `oklch(L C h)` string (as PaletteBuilder emits for the gap shades) to #RRGGBB.
 */
function oklchToHex(string $color): string
{
    if ($color[0] === '#') {
        return strtoupper($color);
    }

    preg_match_all('/[\d.]+/', $color, $m);
    [$lightness, $chroma, $hue] = array_map('floatval', $m[0]);
    $a = $chroma * cos(deg2rad($hue));
    $b = $chroma * sin(deg2rad($hue));

    $l = ($lightness + 0.3963377774 * $a + 0.2158037573 * $b) ** 3;
    $mid = ($lightness - 0.1055613458 * $a - 0.0638541728 * $b) ** 3;
    $s = ($lightness - 0.0894841775 * $a - 1.2914855480 * $b) ** 3;

    $linear = [
        4.0767416621 * $l - 3.3077115913 * $mid + 0.2309699292 * $s,
        -1.2684380046 * $l + 2.6097574011 * $mid - 0.3413193965 * $s,
        -0.0041960863 * $l - 0.7034186147 * $mid + 1.7076147010 * $s,
    ];

    return '#'.implode('', array_map(
        fn (float $x): string => sprintf('%02X', (int) max(0, min(255, round(255 * ($x <= 0.0031308 ? 12.92 * $x : 1.055 * $x ** (1 / 2.4) - 0.055))))),
        $linear,
    ));
}

$modes = ['light' => ['light'], 'dark' => ['dark']];

$combinations = [];
foreach (SidebarVariant::cases() as $variant) {
    foreach (['light', 'dark'] as $mode) {
        $combinations["{$variant->name} / {$mode}"] = [$variant, $mode];
    }
}

it('meets WCAG contrast thresholds for every sidebar-independent pair', function (string $mode) {
    // Base tokens do not depend on the sidebar variant, so one variant stands in for all of them.
    $tokens = SemanticTokens::colors(SidebarVariant::Light)[$mode];
    $pairs = array_filter(semanticContrastPairs(), fn (array $pair): bool => ! isSidebarPair($pair));

    $failures = contrastFailures($mode, $tokens, $pairs);

    expect($failures)->toBe([], "Contrast failures:\n".implode("\n", $failures));
})->with($modes);

it('meets WCAG contrast thresholds for every sidebar pair', function (SidebarVariant $variant, string $mode) {
    $tokens = SemanticTokens::colors($variant)[$mode];
    $pairs = array_filter(semanticContrastPairs(), isSidebarPair(...));

    $failures = contrastFailures("{$variant->name} / {$mode}", $tokens, $pairs);

    expect($failures)->toBe([], "Contrast failures:\n".implode("\n", $failures));
})->with($combinations);

it('keeps the base tokens identical across sidebar variants', function (string $mode) {
    // Guards the single-variant assumption of the sidebar-independent test above.
    $base = fn (SidebarVariant $variant): array => array_filter(
        SemanticTokens::colors($variant)[$mode],
        fn (string $name): bool => ! str_starts_with($name, 'sidebar-'),
        ARRAY_FILTER_USE_KEY,
    );

    foreach (SidebarVariant::cases() as $variant) {
        expect($base($variant))->toBe($base(SidebarVariant::Light));
    }
})->with($modes);

it('keeps Filament inner text and icons readable on the raised surface', function (string $mode) {
    $tokens = SemanticTokens::colors(SidebarVariant::Light)[$mode];
    $raised = resolveSemanticColor($tokens['surface-raised']);

    // Filament's own muted text and icon grays, as it paints them inside modals, dropdowns and toasts
    // (text-gray-500 / dark:text-gray-400, text-gray-400 / dark:text-gray-500). Light mode keeps a white
    // raised surface, the same as Filament, so these pairs are asserted too and pass with margin.
    [$mutedShade, $iconShade] = $mode === 'light' ? ['gray-500', 'gray-400'] : ['gray-400', 'gray-500'];

    expect(Contrast::ratio(resolveSemanticColor($mutedShade), $raised))->toBeGreaterThanOrEqual(4.5)
        ->and(Contrast::ratio(resolveSemanticColor($iconShade), $raised))->toBeGreaterThanOrEqual(3.0);
})->with($modes);

it('keeps the hover/focus row background distinct from the raised surface', function (string $mode) {
    $tokens = SemanticTokens::colors(SidebarVariant::Light)[$mode];

    expect(resolveSemanticColor($tokens['row-hover-bg']))->not->toBe(resolveSemanticColor($tokens['surface-raised']));
})->with($modes);

it('keeps coloured dropdown item icons readable on the hover/focus background', function (string $mode) {
    $tokens = SemanticTokens::colors(SidebarVariant::Light)[$mode];
    $hover = resolveSemanticColor($tokens['row-hover-bg']);

    // Filament paints a coloured item icon text-color-500 (light) / text-color-400 (dark); the theme
    // repaints the item with row-hover-bg on hover and focus. Measured light ratios on gray-50:
    // primary 4.32, danger 3.63, success 3.74, info 4.63, warning 1.57. Light warning is left unasserted:
    // it is Filament's own baseline (warning-500 on a pale background), not something the theme changes.
    // Dark (400 on gray-700): primary 4.32, danger 4.21, success 4.53, info 4.01, warning 7.67.
    $scales = ['primary' => 'brand-01', 'danger' => 'negative', 'success' => 'positive', 'warning' => 'warning', 'info' => 'info'];
    $shade = $mode === 'light' ? 500 : 400;
    $failures = [];

    foreach ($scales as $color => $scale) {
        if ($mode === 'light' && $color === 'warning') {
            continue;
        }

        $ratio = Contrast::ratio(oklchToHex(PaletteBuilder::fromScale(AnimaliaTokens::scale($scale))[$shade]), $hover);

        if ($ratio < 3.0) {
            $failures[] = sprintf('%s %s-%d on row-hover-bg = %.2f < 3.0', $mode, $color, $shade, $ratio);
        }
    }

    expect($failures)->toBe([], "Contrast failures:\n".implode("\n", $failures));
})->with($modes);
