<?php

namespace ThalysJuvenal\FilamentAnimalia\Support;

use ThalysJuvenal\FilamentAnimalia\Enums\Density;
use ThalysJuvenal\FilamentAnimalia\Enums\Radius;
use ThalysJuvenal\FilamentAnimalia\Enums\SidebarVariant;
use ThalysJuvenal\FilamentAnimalia\Tokens\SemanticTokens;

final class TokenStylesheet
{
    public static function render(SidebarVariant $sidebar, Density $density, Radius $radius): string
    {
        $colors = SemanticTokens::colors($sidebar);

        $light = self::declarations($colors['light']).self::declarations(SemanticTokens::dimensions($density, $radius), false);
        $dark = self::declarations($colors['dark']);

        return '<style data-animalia-tokens>:root{'.$light.'}:root.dark{'.$dark.'}</style>';
    }

    /**
     * @param  array<string, string>  $tokens
     */
    private static function declarations(array $tokens, bool $resolve = true): string
    {
        $css = '';

        foreach ($tokens as $name => $value) {
            $css .= '--ani-'.$name.':'.($resolve ? self::resolve($value) : $value).';';
        }

        return $css;
    }

    private static function resolve(string $value): string
    {
        return in_array($value, ['white', 'transparent'], true) ? $value : 'var(--'.$value.')';
    }
}
