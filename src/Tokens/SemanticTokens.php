<?php

namespace ThalysJuvenal\FilamentAnimalia\Tokens;

use ThalysJuvenal\FilamentAnimalia\Enums\Density;
use ThalysJuvenal\FilamentAnimalia\Enums\Radius;
use ThalysJuvenal\FilamentAnimalia\Enums\SidebarVariant;

final class SemanticTokens
{
    /**
     * Color tokens (without the --ani- prefix) per mode, values as '{color}-{shade}', 'white' or 'transparent'.
     *
     * @return array{light: array<string, string>, dark: array<string, string>}
     */
    public static function colors(SidebarVariant $sidebar): array
    {
        $sidebarTokens = self::sidebar($sidebar);

        return [
            'light' => [...self::lightBase(), ...$sidebarTokens['light']],
            'dark' => [...self::darkBase(), ...$sidebarTokens['dark']],
        ];
    }

    /**
     * Dimension tokens as literal CSS values.
     *
     * @return array<string, string>
     */
    public static function dimensions(Density $density, Radius $radius): array
    {
        [$radiusControl, $radiusContainer] = match ($radius) {
            Radius::None => ['0', '0'],
            Radius::Sm => ['2px', '2px'],
            Radius::Default => ['4px', '8px'],
            Radius::Lg => ['8px', '8px'],
        };

        $compact = $density === Density::Compact;
        [$controlY, $controlX] = explode(' ', AnimaliaTokens::squish()[$compact ? 'sm' : 'md']);

        $dimensions = [
            'radius-control' => $radiusControl,
            'radius-container' => $radiusContainer,
            'radius-pill' => '400px',
            'space-control-y' => $controlY,
            'space-control-x' => $controlX,
            'space-cell-y' => $compact ? '8px' : '12px',
            'space-cell-x' => $compact ? '12px' : '16px',
            'space-section' => $compact ? '1rem' : '1.5rem',
            'border-width-control' => '1px',
            'border-width-button' => '2px',
        ];

        foreach (AnimaliaTokens::shadows() as $name => $value) {
            $dimensions["shadow-{$name}"] = $value;
        }

        return $dimensions;
    }

    /**
     * @return array<string, string>
     */
    private static function lightBase(): array
    {
        return [
            'page-bg' => 'gray-50',
            'surface' => 'white',
            'surface-raised' => 'white',
            'border' => 'gray-100',
            'border-strong' => 'gray-200',
            'border-input' => 'gray-500',
            'text' => 'gray-800',
            'text-muted' => 'gray-500',
            'primary' => 'primary-600',
            'primary-hover' => 'primary-700',
            'primary-pressed' => 'primary-800',
            'on-primary' => 'white',
            'primary-subtle-bg' => 'primary-50',
            'primary-subtle-text' => 'primary-600',
            'link' => 'primary-600',
            'focus-ring' => 'primary-800',
            'disabled-bg' => 'gray-100',
            'disabled-text' => 'gray-400',
            'neutral-subtle-bg' => 'gray-100',
            'neutral-subtle-text' => 'gray-800',
            'row-hover-bg' => 'gray-50',
            'row-selected-bg' => 'primary-50',
            'tooltip-bg' => 'gray-800',
            'tooltip-text' => 'gray-50',
            'success' => 'success-600',
            'on-success' => 'white',
            'danger' => 'danger-600',
            'on-danger' => 'white',
            'warning' => 'warning-600',
            'on-warning' => 'warning-950',
            'info' => 'info-600',
            'on-info' => 'white',
            'success-bg' => 'success-50',
            'success-text' => 'success-600',
            'danger-bg' => 'danger-50',
            'danger-text' => 'danger-700',
            'warning-bg' => 'warning-50',
            'warning-text' => 'warning-800',
            'info-bg' => 'info-50',
            'info-text' => 'info-600',
        ];
    }

    /**
     * @return array<string, string>
     */
    private static function darkBase(): array
    {
        return [
            'page-bg' => 'gray-950',
            'surface' => 'gray-800',
            'surface-raised' => 'gray-800',
            'border' => 'gray-700',
            'border-strong' => 'gray-600',
            'border-input' => 'gray-500',
            'text' => 'gray-50',
            'text-muted' => 'gray-300',
            'primary' => 'primary-400',
            'primary-hover' => 'primary-200',
            'primary-pressed' => 'primary-50',
            'on-primary' => 'primary-950',
            'primary-subtle-bg' => 'primary-950',
            'primary-subtle-text' => 'primary-200',
            'link' => 'primary-200',
            'focus-ring' => 'primary-200',
            'disabled-bg' => 'gray-700',
            'disabled-text' => 'gray-500',
            'neutral-subtle-bg' => 'gray-700',
            'neutral-subtle-text' => 'gray-50',
            'row-hover-bg' => 'gray-700',
            'row-selected-bg' => 'primary-950',
            'tooltip-bg' => 'gray-50',
            'tooltip-text' => 'gray-800',
            'success' => 'success-400',
            'on-success' => 'success-950',
            'danger' => 'danger-400',
            'on-danger' => 'danger-950',
            'warning' => 'warning-400',
            'on-warning' => 'warning-950',
            'info' => 'info-400',
            'on-info' => 'info-950',
            'success-bg' => 'success-950',
            'success-text' => 'success-200',
            'danger-bg' => 'danger-950',
            'danger-text' => 'danger-200',
            'warning-bg' => 'warning-950',
            'warning-text' => 'warning-200',
            'info-bg' => 'info-950',
            'info-text' => 'info-200',
        ];
    }

    /**
     * @return array{light: array<string, string>, dark: array<string, string>}
     */
    private static function sidebar(SidebarVariant $variant): array
    {
        switch ($variant) {
            case SidebarVariant::DeepIndigo:
                $light = [
                    'sidebar-bg' => 'primary-950',
                    'sidebar-text' => 'primary-200',
                    'sidebar-label' => 'primary-400',
                    'sidebar-brand' => 'white',
                    'sidebar-item-hover-bg' => 'primary-800',
                    'sidebar-item-active-bg' => 'primary-600',
                    'sidebar-item-active-text' => 'white',
                    'sidebar-border' => 'transparent',
                ];

                return ['light' => $light, 'dark' => [...$light, 'sidebar-border' => 'gray-700']];

            case SidebarVariant::Light:
                return [
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
                ];

            case SidebarVariant::Indigo:
                $both = [
                    'sidebar-bg' => 'primary-600',
                    'sidebar-text' => 'primary-50',
                    'sidebar-label' => 'primary-200',
                    'sidebar-brand' => 'white',
                    'sidebar-item-hover-bg' => 'primary-700',
                    'sidebar-item-active-bg' => 'primary-800',
                    'sidebar-item-active-text' => 'white',
                    'sidebar-border' => 'transparent',
                ];

                return ['light' => $both, 'dark' => $both];
        }
    }
}
