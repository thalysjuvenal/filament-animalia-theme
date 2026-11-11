# Filament AnimaliaDS Theme

> Community package based on AnimaliaDS — not affiliated with or endorsed by TOTVS or the AnimaliaDS team

[Leia em português](README.pt-BR.md)

A Filament 4/5 panel plugin that restyles the admin panel with the AnimaliaDS design tokens: palettes, semantic tokens, sidebar variants, density, corner radius and component styling (buttons, forms, tables, navigation, overlays, feedback and layout).

Filament's default icons are kept. Animalia icon support is planned once the icon set's license is confirmed.

## Requirements

- PHP 8.2+
- Filament 4.x or 5.x (`filament/filament: ^4.0|^5.0`)

## Installation

```bash
composer require thalysjuvenal/filament-animalia-theme
```

Register the plugin in your panel provider:

```php
<?php

namespace App\Providers\Filament;

use Filament\Panel;
use Filament\PanelProvider;
use ThalysJuvenal\FilamentAnimalia\AnimaliaTheme;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('admin')
            ->path('admin')
            ->login()
            ->plugin(AnimaliaTheme::make());
    }
}
```

The stylesheets are registered as Filament assets, so publish them:

```bash
php artisan filament:assets
```

Run this again after upgrading the package.

## Configuration

All options are optional and chainable on `AnimaliaTheme::make()`.

| Method | Accepts | Default | Description |
|---|---|---|---|
| `primaryColor()` | `'brand-01'`, `'brand-02'` or a `#RRGGBB` hex | `'brand-01'` | Panel primary colour. Anything else throws `InvalidArgumentException`. |
| `sidebar()` | `SidebarVariant` or `'deep-indigo'`, `'light'`, `'indigo'` | `deep-indigo` | Sidebar colour scheme. |
| `density()` | `Density` or `'comfortable'`, `'compact'` | `comfortable` | Control and spacing density. |
| `radius()` | `Radius` or `'none'`, `'sm'`, `'default'`, `'lg'` | `default` | Corner radius scale. |
| `font()` | `?string $family, ?string $url = null, ?string $provider = null` | not set | Passed to Filament's `$panel->font()`. Not calling it leaves Filament's own font (Inter) untouched. |
| `restyle()` | `?array $only = null, array $except = []` of `RestyleGroup` or string | all groups | Choose which component groups are restyled: `buttons`, `forms`, `tables`, `navigation`, `overlays`, `feedback`, `layout`. |
| `withoutPrecompiledCss()` | `bool $condition = true` | precompiled CSS on | Stops linking the precompiled stylesheets. See [Vite mode](#vite-mode). |

Example:

```php
use ThalysJuvenal\FilamentAnimalia\Enums\Density;
use ThalysJuvenal\FilamentAnimalia\Enums\RestyleGroup;
use ThalysJuvenal\FilamentAnimalia\Enums\SidebarVariant;

$panel->plugin(
    AnimaliaTheme::make()
        ->primaryColor('brand-02')
        ->sidebar(SidebarVariant::Light)
        ->density(Density::Compact)
        ->radius('lg')
        ->restyle(except: [RestyleGroup::Tables])
);
```

## Vite mode

By default the plugin links its precompiled stylesheets (`core.css` plus one file per restyle group). If you build your own Filament theme with Vite, call `withoutPrecompiledCss()` and import the sources into that theme instead:

```php
$panel->plugin(AnimaliaTheme::make()->withoutPrecompiledCss());
```

Create the theme with `php artisan make:filament-theme` and add the imports to the generated `resources/css/filament/{panel}/theme.css`, below Filament's own `@import`:

```css
@import '../../../../vendor/filament/filament/resources/css/theme.css';

@import '../../../../vendor/thalysjuvenal/filament-animalia-theme/resources/css/core.css';
@import '../../../../vendor/thalysjuvenal/filament-animalia-theme/resources/css/groups/buttons.css';
@import '../../../../vendor/thalysjuvenal/filament-animalia-theme/resources/css/groups/forms.css';
@import '../../../../vendor/thalysjuvenal/filament-animalia-theme/resources/css/groups/tables.css';
@import '../../../../vendor/thalysjuvenal/filament-animalia-theme/resources/css/groups/navigation.css';
@import '../../../../vendor/thalysjuvenal/filament-animalia-theme/resources/css/groups/overlays.css';
@import '../../../../vendor/thalysjuvenal/filament-animalia-theme/resources/css/groups/feedback.css';
@import '../../../../vendor/thalysjuvenal/filament-animalia-theme/resources/css/groups/layout.css';
```

Register the compiled theme with `->viteTheme('resources/css/filament/admin/theme.css')` as usual.

What `withoutPrecompiledCss()` changes:

- It turns off only the `<link>` tags for `core.css` and the group stylesheets.
- It does not turn off the token `<style data-animalia-tokens>` block (the `--ani-*` custom properties for sidebar, density and radius), the colour palettes, or the `font()` setting. Those still come from the plugin, so the imported CSS keeps working.
- `restyle()` only controls which precompiled group links are rendered, so it has no effect in Vite mode. Choose the groups by importing only the `groups/*.css` files you want; keep `core.css`.

## Overriding colours

The plugin sets the panel colours (`primary`, `secondary`, `gray`, `success`, `danger`, `warning`, `info`) when it is registered. Filament merges every `->colors()` call in order and later values win per key, so call `->colors()` after `->plugin()`:

```php
$panel
    ->plugin(AnimaliaTheme::make())
    ->colors([
        'danger' => \Filament\Support\Colors\Color::Rose,
    ]);
```

Only the keys you pass are replaced. `colors()` changes Filament's colour palettes; the `--ani-*` tokens used by the restyle stylesheets (sidebar, surfaces, borders) are not affected. To change the primary colour consistently, prefer `primaryColor()`.

## Accessibility scope

The WCAG AA contrast guarantee covers the theme's own semantic tokens only; the test suite checks them. Colours you set yourself, through `primaryColor('#RRGGBB')` or `->colors()`, are your responsibility. For example, the AnimaliaDS `brand-02` colour `#8DC897` on white is only 1.94:1 (WCAG AA needs 4.5:1 for normal text), so it is not suitable as text on a white background.

## RTL

Right-to-left layouts are supported. The stylesheets use CSS logical properties only (no `margin-left`, `padding-right`, `text-align: left` and similar), and `tests/Unit/CssConventionsTest.php` enforces this.

## Tested compatibility

| Environment | Filament 4 | Filament 5 |
|---|---|---|
| PHP 8.2 (CI: Pint, PHPStan, Pest) | in CI matrix | in CI matrix |
| PHP 8.4 (CI: Pint, PHPStan, Pest) | in CI matrix | in CI matrix |
| PHP 8.4 (CI: Playwright visual regression) | in CI matrix | in CI matrix |

The table lists what the CI workflows cover; check the repository's CI status for current results. CI (`.github/workflows/tests.yml`, `.github/workflows/visual.yml`) installs the latest release matching `^4.0` and `^5.0`. Local development runs used PHP 8.4 with Filament 5.11.2 and Filament 4.16.2. Other combinations are allowed by `composer.json` but not exercised.

## Credits

- [AnimaliaDS](https://github.com/animaliads) design tokens and brand: [animalia-brand](https://github.com/animaliads/animalia-brand) and [animalia-design-tokens](https://github.com/animaliads/animalia-design-tokens), MIT licensed. See [NOTICE](NOTICE).
- [Filament](https://filamentphp.com) provides the panel framework.
- The default typeface is Inter, which comes from Filament itself (its local font provider); this package does not bundle fonts.

## License

MIT, see [LICENSE](LICENSE). Third-party attribution is in [NOTICE](NOTICE). Changes are listed in [CHANGELOG.md](CHANGELOG.md).
