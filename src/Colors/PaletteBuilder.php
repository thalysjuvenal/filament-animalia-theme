<?php

namespace ThalysJuvenal\FilamentAnimalia\Colors;

use Filament\Support\Colors\Color;

final class PaletteBuilder
{
    public const ANCHORS = [50 => 'lightest', 200 => 'lighter', 400 => 'light', 600 => 'base', 700 => 'dark', 800 => 'darker', 950 => 'darkest'];

    public const NEUTRAL_ANCHORS = [50 => '05', 100 => '10', 200 => '20', 300 => '30', 400 => '40', 500 => '60', 600 => '70', 700 => '80', 800 => '90', 950 => '95'];

    /** Shades missing from ANCHORS, with the neighbouring shades they sit between. */
    private const GAPS = [100 => [50, 200], 300 => [200, 400], 500 => [400, 600], 900 => [800, 950]];

    /**
     * @param  array<string, string>  $scale
     * @return array<int, string>
     */
    public static function fromScale(array $scale): array
    {
        $anchors = [];

        foreach (self::ANCHORS as $shade => $key) {
            $anchors[$shade] = $scale[$key];
        }

        return self::build($anchors, self::GAPS);
    }

    /**
     * @param  array<string, string>  $neutrals
     * @return array<int, string>
     */
    public static function fromNeutrals(array $neutrals): array
    {
        $anchors = [];

        foreach (self::NEUTRAL_ANCHORS as $shade => $key) {
            $anchors[$shade] = $neutrals[$key];
        }

        return self::build($anchors, [900 => [800, 950]]);
    }

    /**
     * @param  array<int, string>  $anchors
     * @param  array<int, array{int, int}>  $gaps
     * @return array<int, string>
     */
    private static function build(array $anchors, array $gaps): array
    {
        $palette = $anchors;

        foreach ($gaps as $shade => [$from, $to]) {
            $palette[$shade] = self::midpoint($anchors[$from], $anchors[$to]);
        }

        ksort($palette);

        return $palette;
    }

    private static function midpoint(string $a, string $b): string
    {
        [$l1, $c1, $h1] = self::oklch($a);
        [$l2, $c2, $h2] = self::oklch($b);

        $delta = fmod(fmod($h2 - $h1 + 180, 360) + 360, 360) - 180;
        $hue = $h1 + $delta / 2;

        if ($hue < 0) {
            $hue += 360;
        } elseif ($hue >= 360) {
            $hue -= 360;
        }

        return 'oklch('.self::format(($l1 + $l2) / 2).' '.self::format(($c1 + $c2) / 2).' '.self::format($hue).')';
    }

    /**
     * Rounds to 3 decimals from the exact binary value (like JS toFixed) and drops trailing zeros.
     */
    private static function format(float $value): string
    {
        return (string) (float) sprintf('%.3f', $value);
    }

    /**
     * @return array{float, float, float}
     */
    private static function oklch(string $color): array
    {
        $parsed = sscanf(Color::convertToOklch($color), 'oklch(%f %f %f)');

        return [(float) $parsed[0], (float) $parsed[1], (float) $parsed[2]];
    }
}
