<?php

use function Pest\Laravel\artisan;
use function Pest\Laravel\get;

it('renders the admin login page with the plugin registered', function () {
    get('/admin/login')->assertOk();

    expect(filament()->getPanel('admin')->hasPlugin('animalia-theme'))->toBeTrue();
});

const ANIMALIA_CORE_CSS = 'css/thalysjuvenal/filament-animalia-theme/animalia-core.css';

afterEach(function () {
    $published = public_path(ANIMALIA_CORE_CSS);

    if (is_file($published)) {
        unlink($published);
        @rmdir(dirname($published));
        @rmdir(dirname($published, 2));
    }
});

it('links the core stylesheet', function () {
    get('/admin/login')->assertOk()->assertSee(ANIMALIA_CORE_CSS, false);
});

it('publishes the stylesheet where the link points', function () {
    artisan('filament:assets')->assertSuccessful();

    expect(public_path(ANIMALIA_CORE_CSS))->toBeFile();
});

it('renders Animalia tokens, palette and the local Inter font', function () {
    get('/admin/login')->assertOk()
        ->assertSee('data-animalia-tokens', false)
        ->assertSee('--primary-600:oklch(0.444 0.145 278.692)', false)
        ->assertSee("--font-family: 'Inter Variable'", false)
        ->assertDontSee('fonts.bunny.net', false);
});
