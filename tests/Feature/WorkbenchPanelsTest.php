<?php

use Workbench\App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

it('serves every demo panel login', function (string $panel) {
    get("/{$panel}/login")->assertOk();
})->with(['admin', 'light', 'indigo', 'plain']);

it('applies the configured sidebar variant per panel', function (string $panel, string $bg) {
    actingAs(new User(['name' => 'T', 'email' => 't@example.com', 'password' => bcrypt('x')]));

    get("/{$panel}")->assertOk()->assertSee("--ani-sidebar-bg:{$bg};", false);
})->with([['admin', 'var(--primary-950)'], ['light', 'white'], ['indigo', 'var(--primary-600)']]);

it('leaves a panel without the plugin untouched', function () {
    get('/plain/login')->assertOk()
        ->assertDontSee('data-animalia-tokens', false)
        ->assertDontSee('animalia-core.css', false)
        ->assertDontSee('--primary-600:oklch(0.444 0.145 278.692)', false);
});
