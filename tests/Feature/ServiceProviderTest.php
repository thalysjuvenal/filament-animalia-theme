<?php

use ThalysJuvenal\FilamentAnimalia\FilamentAnimaliaServiceProvider;

it('loads the package service provider', function () {
    expect(app()->providerIsLoaded(FilamentAnimaliaServiceProvider::class))->toBeTrue();
});
