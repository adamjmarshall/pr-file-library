<?php

use AdamMarshall\FilamentFileLibrary\Models\LibraryFile;

test('human readable size formats bytes sensibly', function (int $bytes, string $expected) {
    $file = LibraryFile::make(['size' => $bytes]);

    expect($file->humanReadableSize())->toBe($expected);
})->with([
    [500, '500 B'],
    [2048, '2 KB'],
    [5 * 1024 * 1024, '5 MB'],
]);
