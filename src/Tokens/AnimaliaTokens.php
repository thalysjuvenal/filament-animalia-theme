<?php

namespace ThalysJuvenal\FilamentAnimalia\Tokens;

use InvalidArgumentException;

/**
 * @phpstan-type Scale array{lightest: string, lighter: string, light: string, base: string, dark: string, darker: string, darkest: string}
 * @phpstan-type TokenData array{
 *     source: array<string, string>,
 *     color: array<string, array<string, string>>,
 *     spacing: array<string, string>,
 *     squish: array<string, string>,
 *     shadow: array<string, string>,
 * }
 */
final class AnimaliaTokens
{
    /** @var TokenData|null */
    private static ?array $data = null;

    /**
     * @return Scale
     */
    public static function scale(string $name): array
    {
        $colors = self::data()['color'];

        if ($name === 'neutral' || ! isset($colors[$name])) {
            throw new InvalidArgumentException("Unknown Animalia color scale [{$name}].");
        }

        /** @var Scale */
        return $colors[$name];
    }

    /**
     * @return array<string, string>
     */
    public static function neutrals(): array
    {
        return self::data()['color']['neutral'];
    }

    /**
     * @return array<string, string>
     */
    public static function shadows(): array
    {
        return self::data()['shadow'];
    }

    /**
     * @return array<string, string>
     */
    public static function squish(): array
    {
        return self::data()['squish'];
    }

    /**
     * @return array<string, string>
     */
    public static function spacing(): array
    {
        return self::data()['spacing'];
    }

    /**
     * @return array<string, string>
     */
    public static function source(): array
    {
        return self::data()['source'];
    }

    /**
     * @return TokenData
     */
    private static function data(): array
    {
        if (self::$data === null) {
            /** @var TokenData $data */
            $data = require dirname(__DIR__, 2).'/resources/tokens/animalia.php';
            self::$data = $data;
        }

        return self::$data;
    }
}
