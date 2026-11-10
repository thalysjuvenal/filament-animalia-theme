<?php

$files = glob(__DIR__.'/../../resources/css/{,groups/}*.css', GLOB_BRACE) ?: [];

it('ships at least one stylesheet to check', function () use ($files) {
    expect($files)->not->toBeEmpty();
});

it('keeps CSS unlayered, logical and token-only', function (string $file) {
    $css = (string) file_get_contents($file);

    expect($css)->not->toMatch('/@layer|!important/');
    expect($css)->not->toMatch('/#[0-9a-fA-F]{3,8}\b|rgba?\(|oklch\(/');
    expect($css)->not->toMatch('/(margin|padding|border)-(left|right)\b|(?<![-\w])(left|right)\s*:|text-align:\s*(left|right)/');
})->with(array_combine(array_map('basename', $files), array_map(fn (string $f): array => [$f], $files)));
