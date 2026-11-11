<?php

use ThalysJuvenal\FilamentAnimalia\Enums\RestyleGroup;

use function Pest\Laravel\artisan;
use function Pest\Laravel\get;

it('renders the admin login page with the plugin registered', function () {
    get('/admin/login')->assertOk();

    expect(filament()->getPanel('admin')->hasPlugin('animalia-theme'))->toBeTrue();
});

const ANIMALIA_CORE_CSS = 'css/thalysjuvenal/filament-animalia-theme/animalia-core.css';

/**
 * Public paths of every stylesheet the login page links, derived from the rendered hrefs.
 *
 * @return list<string>
 */
function animaliaLinkedStylesheets(): array
{
    $html = get('/admin/login')->assertOk()->getContent();

    preg_match_all('/<link rel="stylesheet" href="([^"]*thalysjuvenal\/filament-animalia-theme\/[^"]+\.css)[^"]*"/', (string) $html, $matches);

    return array_map(
        fn (string $href): string => ltrim((string) parse_url(html_entity_decode($href), PHP_URL_PATH), '/'),
        $matches[1],
    );
}

afterEach(function () {
    foreach ([ANIMALIA_CORE_CSS, ...array_map(
        fn (RestyleGroup $group): string => 'css/thalysjuvenal/filament-animalia-theme/'.$group->assetId().'.css',
        RestyleGroup::cases(),
    )] as $path) {
        $published = public_path($path);

        if (is_file($published)) {
            unlink($published);
        }
    }

    @rmdir(public_path('css/thalysjuvenal/filament-animalia-theme'));
    @rmdir(public_path('css/thalysjuvenal'));
});

it('links the core stylesheet', function () {
    get('/admin/login')->assertOk()->assertSee(ANIMALIA_CORE_CSS, false);
});

it('publishes the stylesheet where the link points', function () {
    artisan('filament:assets')->assertSuccessful();

    $linked = animaliaLinkedStylesheets();

    expect($linked)->toContain(ANIMALIA_CORE_CSS)
        ->and($linked)->toHaveCount(1 + count(RestyleGroup::cases()));

    foreach ($linked as $path) {
        expect(public_path($path))->toBeFile();
    }
});

it('renders Animalia tokens, palette and the local Inter font', function () {
    get('/admin/login')->assertOk()
        ->assertSee('data-animalia-tokens', false)
        ->assertSee('--primary-600:oklch(0.444 0.145 278.692)', false)
        ->assertSee("--font-family: 'Inter Variable'", false)
        ->assertDontSee('fonts.bunny.net', false);
});
