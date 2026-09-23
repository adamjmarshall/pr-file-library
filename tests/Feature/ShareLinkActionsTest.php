<?php

use AdamMarshall\FilamentFileLibrary\Filament\Resources\Files\LibraryFileResource;
use AdamMarshall\FilamentFileLibrary\Models\FileShareLink;
use AdamMarshall\FilamentFileLibrary\Models\LibraryFile;
use AdamMarshall\FilamentFileLibrary\Tests\User;
use Illuminate\Support\Facades\Gate;
use Livewire\Livewire;

function makeUploader(): User
{
    return User::create(['name' => 'Sharer', 'email' => 'sharer@example.com', 'password' => 'secret']);
}

test('a user with the manage share links permission can create a share link from the table', function () {
    $user = makeUploader();
    Gate::define('Manage File Share Links', fn (User $u) => $u->is($user));

    $file = LibraryFile::create([
        'disk' => 'local', 'path' => 'file-library/x.txt', 'original_name' => 'x.txt', 'size' => 1,
    ]);

    Livewire::actingAs($user)
        ->test(LibraryFileResource::getPages()['index']->getPage())
        ->callTableAction('createShareLink', $file, data: [])
        ->assertHasNoTableActionErrors();

    expect(FileShareLink::where('file_id', $file->id)->count())->toBe(1);
});

test('a user without the manage share links permission cannot create a share link', function () {
    $user = makeUploader();

    $file = LibraryFile::create([
        'disk' => 'local', 'path' => 'file-library/x.txt', 'original_name' => 'x.txt', 'size' => 1,
    ]);

    Livewire::actingAs($user)
        ->test(LibraryFileResource::getPages()['index']->getPage())
        ->assertTableActionHidden('createShareLink', $file);
});

test('an authorized user can revoke a share link via the revoke route', function () {
    $user = makeUploader();
    Gate::define('Manage File Share Links', fn (User $u) => $u->is($user));

    $file = LibraryFile::create([
        'disk' => 'local', 'path' => 'file-library/x.txt', 'original_name' => 'x.txt', 'size' => 1,
    ]);
    $link = FileShareLink::create(['file_id' => $file->id]);

    $this->actingAs($user)
        ->post(route('filament-file-library.revoke-share-link', $link))
        ->assertRedirect();

    expect($link->fresh()->isRevoked())->toBeTrue();
});

test('an unauthorized user cannot revoke a share link', function () {
    $user = makeUploader();

    $file = LibraryFile::create([
        'disk' => 'local', 'path' => 'file-library/x.txt', 'original_name' => 'x.txt', 'size' => 1,
    ]);
    $link = FileShareLink::create(['file_id' => $file->id]);

    $this->actingAs($user)
        ->post(route('filament-file-library.revoke-share-link', $link))
        ->assertForbidden();

    expect($link->fresh()->isRevoked())->toBeFalse();
});
