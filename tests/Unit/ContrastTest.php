<?php

use ThalysJuvenal\FilamentAnimalia\Colors\Contrast;

it('computes black on white as 21', fn () => expect(Contrast::ratio('#000000', '#FFFFFF'))->toEqualWithDelta(21.0, 0.01));

it('is symmetric', fn () => expect(Contrast::ratio('#4545A1', '#FFFFFF'))->toEqual(Contrast::ratio('#FFFFFF', '#4545A1')));

it('matches the spec value for brand-01 on white', fn () => expect(Contrast::ratio('#4545A1', '#FFFFFF'))->toEqualWithDelta(8.02, 0.01));

it('flags n-30 input borders as failing', fn () => expect(Contrast::ratio('#B4B4C0', '#FFFFFF'))->toBeLessThan(3.0));

it('is case-insensitive', fn () => expect(Contrast::ratio('#ffffff', '#000000'))->toEqualWithDelta(21.0, 0.01));

it('rejects malformed colors', function (string $value) {
    Contrast::ratio($value, '#FFFFFF');
})->with(['red', '#FFF', '#GGGGGG', '4545A1', '#4545A1FF'])->throws(InvalidArgumentException::class);
