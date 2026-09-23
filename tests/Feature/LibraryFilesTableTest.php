<?php

use AdamMarshall\FilamentFileLibrary\Filament\Resources\Files\LibraryFileResource;
use AdamMarshall\FilamentFileLibrary\Models\LibraryFile;
use AdamMarshall\FilamentFileLibrary\Tests\User;
use Livewire\Livewire;

test('the type filter narrows the table to matching mime types', function () {
    $user = User::create(['name' => 'Filterer', 'email' => 'filterer@example.com', 'password' => 'secret']);

    $pdf = LibraryFile::create(['disk' => 'local', 'path' => 'a.pdf', 'original_name' => 'a.pdf', 'mime_type' => 'application/pdf', 'size' => 1]);
    $png = LibraryFile::create(['disk' => 'local', 'path' => 'a.png', 'original_name' => 'a.png', 'mime_type' => 'image/png', 'size' => 1]);

    Livewire::actingAs($user)
        ->test(LibraryFileResource::getPages()['index']->getPage())
        ->filterTable('mime_type', ['application/pdf'])
        ->assertCanSeeTableRecords([$pdf])
        ->assertCanNotSeeTableRecords([$png]);
});
