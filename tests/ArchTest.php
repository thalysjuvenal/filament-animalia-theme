<?php

arch('no debugging calls in src', function () {
    expect('ThalysJuvenal\FilamentAnimalia')
        ->not->toUse(['dd', 'dump', 'ray', 'var_dump']);
});
