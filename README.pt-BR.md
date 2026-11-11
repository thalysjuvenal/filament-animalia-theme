# Filament AnimaliaDS Theme

> Pacote da comunidade baseado no AnimaliaDS — sem afiliação nem endosso da TOTVS ou da equipe do AnimaliaDS

[Read in English](README.md)

Plugin de painel para Filament 4/5 que reestiliza o painel administrativo com os tokens de design do AnimaliaDS: paletas, tokens semânticos, variantes de sidebar, densidade, raio de borda e estilo de componentes (botões, formulários, tabelas, navegação, sobreposições, feedback e layout).

Os ícones padrão do Filament são mantidos. O suporte a ícones Animalia está planejado para depois da confirmação da licença do conjunto de ícones.

## Requisitos

- PHP 8.2+
- Filament 4.x ou 5.x (`filament/filament: ^4.0|^5.0`)

## Instalação

```bash
composer require thalysjuvenal/filament-animalia-theme
```

Registre o plugin no provider do painel:

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

Os estilos são registrados como assets do Filament, então publique-os:

```bash
php artisan filament:assets
```

Execute o comando novamente após atualizar o pacote.

## Configuração

Todas as opções são opcionais e encadeáveis em `AnimaliaTheme::make()`.

| Método | Aceita | Padrão | Descrição |
|---|---|---|---|
| `primaryColor()` | `'brand-01'`, `'brand-02'` ou um hex `#RRGGBB` | `'brand-01'` | Cor primária do painel. Qualquer outro valor lança `InvalidArgumentException`. |
| `sidebar()` | `SidebarVariant` ou `'deep-indigo'`, `'light'`, `'indigo'` | `deep-indigo` | Esquema de cores da sidebar. |
| `density()` | `Density` ou `'comfortable'`, `'compact'` | `comfortable` | Densidade de controles e espaçamentos. |
| `radius()` | `Radius` ou `'none'`, `'sm'`, `'default'`, `'lg'` | `default` | Escala de raio de borda. |
| `font()` | `?string $family, ?string $url = null, ?string $provider = null` | não definido | Repassado ao `$panel->font()` do Filament. Sem chamá-lo, a fonte do próprio Filament (Inter) é mantida. |
| `restyle()` | `?array $only = null, array $except = []` de `RestyleGroup` ou string | todos os grupos | Escolhe quais grupos de componentes são reestilizados: `buttons`, `forms`, `tables`, `navigation`, `overlays`, `feedback`, `layout`. |
| `withoutPrecompiledCss()` | `bool $condition = true` | CSS pré-compilado ligado | Deixa de vincular os estilos pré-compilados. Veja [Modo Vite](#modo-vite). |

Exemplo:

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

## Modo Vite

Por padrão o plugin vincula seus estilos pré-compilados (`core.css` mais um arquivo por grupo de restyle). Se você compila um tema próprio do Filament com Vite, chame `withoutPrecompiledCss()` e importe os fontes nesse tema:

```php
$panel->plugin(AnimaliaTheme::make()->withoutPrecompiledCss());
```

Crie o tema com `php artisan make:filament-theme` e adicione os imports ao `resources/css/filament/{panel}/theme.css` gerado, abaixo do `@import` do próprio Filament:

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

Registre o tema compilado com `->viteTheme('resources/css/filament/admin/theme.css')`, como de costume.

O que `withoutPrecompiledCss()` muda:

- Desliga apenas as tags `<link>` do `core.css` e dos estilos dos grupos.
- Não desliga o bloco `<style data-animalia-tokens>` de tokens (as propriedades `--ani-*` de sidebar, densidade e raio), nem as paletas de cores, nem a opção `font()`. Esses continuam vindo do plugin, então o CSS importado segue funcionando.
- `restyle()` só controla quais links de grupos pré-compilados são renderizados, então não tem efeito no modo Vite. Escolha os grupos importando apenas os arquivos `groups/*.css` desejados; mantenha o `core.css`.

## Sobrescrever cores

O plugin define as cores do painel (`primary`, `secondary`, `gray`, `success`, `danger`, `warning`, `info`) ao ser registrado. O Filament mescla todas as chamadas a `->colors()` em ordem e os valores posteriores vencem por chave, então chame `->colors()` depois de `->plugin()`:

```php
$panel
    ->plugin(AnimaliaTheme::make())
    ->colors([
        'danger' => \Filament\Support\Colors\Color::Rose,
    ]);
```

Apenas as chaves informadas são substituídas. `colors()` altera as paletas do Filament; os tokens `--ani-*` usados pelos estilos de restyle (sidebar, superfícies, bordas) não são afetados. Para mudar a cor primária de forma consistente, prefira `primaryColor()`.

## Escopo da acessibilidade

A garantia de contraste WCAG AA cobre apenas os tokens semânticos do próprio tema; a suíte de testes os verifica. Cores que você define, via `primaryColor('#RRGGBB')` ou `->colors()`, são de sua responsabilidade. Por exemplo, a cor `brand-02` do AnimaliaDS, `#8DC897`, sobre branco tem apenas 1,94:1 (o WCAG AA exige 4,5:1 para texto normal), então não serve como texto sobre fundo branco.

## RTL

Layouts da direita para a esquerda são suportados. Os estilos usam somente propriedades lógicas do CSS (sem `margin-left`, `padding-right`, `text-align: left` e similares), e o teste `tests/Unit/CssConventionsTest.php` garante isso.

## Compatibilidade testada

| Ambiente | Filament 4 | Filament 5 |
|---|---|---|
| PHP 8.2 (CI: Pint, PHPStan, Pest) | na matriz de CI | na matriz de CI |
| PHP 8.4 (CI: Pint, PHPStan, Pest) | na matriz de CI | na matriz de CI |
| PHP 8.4 (CI: regressão visual com Playwright) | na matriz de CI | na matriz de CI |

A tabela lista o que os workflows de CI cobrem; consulte o status de CI do repositório para os resultados atuais. O CI (`.github/workflows/tests.yml`, `.github/workflows/visual.yml`) instala a versão mais recente que satisfaz `^4.0` e `^5.0`. As execuções locais de desenvolvimento usaram PHP 8.4 com Filament 5.11.2 e Filament 4.16.2. Outras combinações são permitidas pelo `composer.json`, mas não são exercitadas.

## Créditos

- Tokens de design e marca do [AnimaliaDS](https://github.com/animaliads): [animalia-brand](https://github.com/animaliads/animalia-brand) e [animalia-design-tokens](https://github.com/animaliads/animalia-design-tokens), licenciados sob MIT. Veja o [NOTICE](NOTICE).
- O [Filament](https://filamentphp.com) fornece o framework de painéis.
- A fonte padrão é a Inter, que vem do próprio Filament (provedor de fontes local); este pacote não inclui fontes.

## Licença

MIT, veja [LICENSE](LICENSE). A atribuição a terceiros está no [NOTICE](NOTICE). As mudanças estão em [CHANGELOG.md](CHANGELOG.md).
