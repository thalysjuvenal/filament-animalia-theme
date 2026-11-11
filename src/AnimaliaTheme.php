<?php

namespace ThalysJuvenal\FilamentAnimalia;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Filament\Support\Facades\FilamentAsset;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\HtmlString;
use InvalidArgumentException;
use ThalysJuvenal\FilamentAnimalia\Colors\PaletteBuilder;
use ThalysJuvenal\FilamentAnimalia\Enums\Density;
use ThalysJuvenal\FilamentAnimalia\Enums\Radius;
use ThalysJuvenal\FilamentAnimalia\Enums\RestyleGroup;
use ThalysJuvenal\FilamentAnimalia\Enums\SidebarVariant;
use ThalysJuvenal\FilamentAnimalia\Support\TokenStylesheet;
use ThalysJuvenal\FilamentAnimalia\Tokens\AnimaliaTokens;

final class AnimaliaTheme implements Plugin
{
    public const PACKAGE = 'thalysjuvenal/filament-animalia-theme';

    private string $primaryColor = 'brand-01';

    private SidebarVariant $sidebar = SidebarVariant::DeepIndigo;

    private Density $density = Density::Comfortable;

    private Radius $radius = Radius::Default;

    private ?string $fontFamily = null;

    private ?string $fontUrl = null;

    private ?string $fontProvider = null;

    private bool $precompiledCss = true;

    /** @var list<RestyleGroup> */
    private array $restyleGroups;

    public function __construct()
    {
        $this->restyleGroups = RestyleGroup::cases();
    }

    public static function make(): static
    {
        return app(self::class);
    }

    public function getId(): string
    {
        return 'animalia-theme';
    }

    public function primaryColor(string $color): static
    {
        if (! in_array($color, ['brand-01', 'brand-02'], true) && preg_match('/^#[0-9A-Fa-f]{6}\z/', $color) !== 1) {
            throw new InvalidArgumentException("Invalid AnimaliaDS primary color [{$color}]. Use 'brand-01', 'brand-02' or a #RRGGBB hex.");
        }

        $this->primaryColor = $color;

        return $this;
    }

    public function sidebar(SidebarVariant|string $variant): static
    {
        $this->sidebar = $variant instanceof SidebarVariant ? $variant : self::parse(SidebarVariant::class, $variant, 'sidebar variant');

        return $this;
    }

    public function density(Density|string $density): static
    {
        $this->density = $density instanceof Density ? $density : self::parse(Density::class, $density, 'density');

        return $this;
    }

    public function radius(Radius|string $radius): static
    {
        $this->radius = $radius instanceof Radius ? $radius : self::parse(Radius::class, $radius, 'radius');

        return $this;
    }

    public function font(?string $family, ?string $url = null, ?string $provider = null): static
    {
        $this->fontFamily = $family;
        $this->fontUrl = $url;
        $this->fontProvider = $provider;

        return $this;
    }

    /**
     * Select which component groups are restyled to AnimaliaDS.
     *
     * @param  array<int, RestyleGroup|string>|null  $only  Defaults to every group.
     * @param  array<int, RestyleGroup|string>  $except
     */
    public function restyle(?array $only = null, array $except = []): static
    {
        $onlyGroups = $only === null ? RestyleGroup::cases() : array_map(self::parseGroup(...), $only);
        $exceptGroups = array_map(self::parseGroup(...), $except);

        $this->restyleGroups = [...array_filter(
            RestyleGroup::cases(),
            fn (RestyleGroup $group): bool => in_array($group, $onlyGroups, true) && ! in_array($group, $exceptGroups, true),
        )];

        return $this;
    }

    /**
     * @return list<RestyleGroup>
     */
    public function getRestyleGroups(): array
    {
        return $this->restyleGroups;
    }

    public function withoutPrecompiledCss(bool $condition = true): static
    {
        $this->precompiledCss = ! $condition;

        return $this;
    }

    public function usesPrecompiledCss(): bool
    {
        return $this->precompiledCss;
    }

    public function getPrimaryColor(): string
    {
        return $this->primaryColor;
    }

    public function getSidebar(): SidebarVariant
    {
        return $this->sidebar;
    }

    public function getDensity(): Density
    {
        return $this->density;
    }

    public function getRadius(): Radius
    {
        return $this->radius;
    }

    /**
     * @return array<string, array<int, string>|string>
     */
    public function getColors(): array
    {
        $primary = match ($this->primaryColor) {
            'brand-01', 'brand-02' => PaletteBuilder::fromScale(AnimaliaTokens::scale($this->primaryColor)),
            default => $this->primaryColor,
        };

        return [
            'primary' => $primary,
            'secondary' => PaletteBuilder::fromScale(AnimaliaTokens::scale('brand-02')),
            'gray' => PaletteBuilder::fromNeutrals(AnimaliaTokens::neutrals()),
            'success' => PaletteBuilder::fromScale(AnimaliaTokens::scale('positive')),
            'danger' => PaletteBuilder::fromScale(AnimaliaTokens::scale('negative')),
            'warning' => PaletteBuilder::fromScale(AnimaliaTokens::scale('warning')),
            'info' => PaletteBuilder::fromScale(AnimaliaTokens::scale('info')),
        ];
    }

    public function renderHead(): string
    {
        $head = TokenStylesheet::render($this->sidebar, $this->density, $this->radius);

        if ($this->usesPrecompiledCss()) {
            $href = e(FilamentAsset::getStyleHref('animalia-core', self::PACKAGE));
            $head .= '<link rel="stylesheet" href="'.$href.'" data-navigate-track>';

            foreach ($this->restyleGroups as $group) {
                $groupHref = e(FilamentAsset::getStyleHref($group->assetId(), self::PACKAGE));
                $head .= '<link rel="stylesheet" href="'.$groupHref.'" data-navigate-track>';
            }
        }

        return $head;
    }

    public function register(Panel $panel): void
    {
        $panel->colors(fn (): array => $this->getColors());

        if ($this->fontFamily !== null) {
            $panel->font($this->fontFamily, $this->fontUrl, $this->fontProvider);
        }

        $panel->renderHook(PanelsRenderHook::STYLES_AFTER, fn (): HtmlString => new HtmlString($this->renderHead()));
    }

    public function boot(Panel $panel): void {}

    private static function parseGroup(RestyleGroup|string $group): RestyleGroup
    {
        if ($group instanceof RestyleGroup) {
            return $group;
        }

        $case = RestyleGroup::tryFrom($group);

        if ($case === null) {
            $valid = implode(', ', array_map(fn (RestyleGroup $g): string => $g->value, RestyleGroup::cases()));

            throw new InvalidArgumentException("Unknown AnimaliaDS restyle group [{$group}]. Valid groups: {$valid}.");
        }

        return $case;
    }

    /**
     * @template T of \BackedEnum
     *
     * @param  class-string<T>  $enum
     * @return T
     */
    private static function parse(string $enum, string $value, string $label): \BackedEnum
    {
        $case = $enum::tryFrom($value);

        if ($case === null) {
            $valid = implode(', ', array_map(fn (\BackedEnum $c): string => (string) $c->value, $enum::cases()));

            throw new InvalidArgumentException("Invalid AnimaliaDS {$label} [{$value}]. Valid values: {$valid}.");
        }

        return $case;
    }
}
