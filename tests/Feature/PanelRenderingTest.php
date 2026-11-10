<?php

use function Pest\Laravel\get;

it('renders the admin login page with the plugin registered', function () {
    get('/admin/login')->assertOk();

    expect(filament()->getPanel('admin')->hasPlugin('animalia-theme'))->toBeTrue();
});
