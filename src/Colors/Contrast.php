<?php

namespace ThalysJuvenal\FilamentAnimalia\Colors;

use InvalidArgumentException;

final class Contrast
{
    /**
     * WCAG 2.x contrast ratio between two #RRGGBB colors (symmetric, 1.0 to 21.0).
     */
    public static function ratio(string $hexA, string $hexB): float
    {
        $a = self::luminance($hexA);
        $b = self::luminance($hexB);

        return (max($a, $b) + 0.05) / (min($a, $b) + 0.05);
    }

    private static function luminance(string $hex): float
    {
        if (! preg_match('/^#([0-9a-f]{2})([0-9a-f]{2})([0-9a-f]{2})\z/i', $hex, $m)) {
            throw new InvalidArgumentException("Invalid hex color '{$hex}', expected #RRGGBB.");
        }

        [$r, $g, $b] = array_map(
            static function (string $channel): float {
                $c = hexdec($channel) / 255;

                return $c <= 0.04045 ? $c / 12.92 : (($c + 0.055) / 1.055) ** 2.4;
            },
            [$m[1], $m[2], $m[3]],
        );

        return 0.2126 * $r + 0.7152 * $g + 0.0722 * $b;
    }
}
