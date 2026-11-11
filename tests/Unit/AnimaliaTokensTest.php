<?php

use ThalysJuvenal\FilamentAnimalia\Tokens\AnimaliaTokens;

it('exposes the brand-01 scale', fn () => expect(AnimaliaTokens::scale('brand-01')['base'])->toBe('#4545A1'));

it('exposes eleven neutrals', fn () => expect(AnimaliaTokens::neutrals())->toHaveCount(11)->toHaveKey('95', '#060613'));

it('rejects unknown scales', fn () => AnimaliaTokens::scale('brand-03'))->throws(InvalidArgumentException::class);
