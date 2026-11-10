<?php

use ThalysJuvenal\FilamentAnimalia\Enums\Density;
use ThalysJuvenal\FilamentAnimalia\Enums\Radius;
use ThalysJuvenal\FilamentAnimalia\Enums\SidebarVariant;
use ThalysJuvenal\FilamentAnimalia\Support\TokenStylesheet;

it('renders light and dark blocks', function () {
    $css = TokenStylesheet::render(SidebarVariant::DeepIndigo, Density::Comfortable, Radius::Default);

    expect($css)->toStartWith('<style data-animalia-tokens>:root{')
        ->toContain('--ani-sidebar-bg:var(--primary-950);')
        ->toContain('--ani-surface:white;')
        ->toContain(':root.dark{')
        ->toContain('--ani-radius-control:4px;')
        ->toEndWith('}</style>');
});

it('emits dimensions only in the light block', function () {
    $css = TokenStylesheet::render(SidebarVariant::DeepIndigo, Density::Comfortable, Radius::Default);

    expect(explode(':root.dark{', $css)[1])->not->toContain('--ani-radius-control');
});
