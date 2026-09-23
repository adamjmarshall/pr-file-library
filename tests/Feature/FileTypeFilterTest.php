<?php

use AdamMarshall\FilamentFileLibrary\Enums\FileType;

test('common mime types have presentable labels', function (FileType $type, string $label) {
    expect($type->getLabel())->toBe($label);
})->with([
    [FileType::PDF, 'Pdf'],
    [FileType::JPEG, 'Jpeg'],
    [FileType::PNG, 'Png'],
    [FileType::WORD_DOCX, 'Word (.docx)'],
]);

test('enum values are the exact mime type strings they represent', function () {
    expect(FileType::PDF->value)->toBe('application/pdf');
    expect(FileType::PNG->value)->toBe('image/png');
});
