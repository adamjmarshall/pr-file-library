<?php

use AdamMarshall\FilamentFileLibrary\Models\LibraryFile;
use AdamMarshall\FilamentFileLibrary\Tests\User;
use Illuminate\Support\Facades\Gate;

function makeUser(array $attributes = []): User
{
    return User::create(array_merge([
        'name' => 'Test User',
        'email' => uniqid('user').'@example.com',
        'password' => 'secret',
    ], $attributes));
}

test('any authenticated user can view the file index and a file', function () {
    $user = makeUser();
    $file = LibraryFile::create([
        'disk' => 'local',
        'path' => 'file-library/test.txt',
        'original_name' => 'test.txt',
        'mime_type' => 'text/plain',
        'size' => 10,
    ]);

    expect(Gate::forUser($user)->allows('viewAny', LibraryFile::class))->toBeTrue();
    expect(Gate::forUser($user)->allows('view', $file))->toBeTrue();
});

test('a user without the upload permission cannot create files', function () {
    $user = makeUser();

    expect(Gate::forUser($user)->allows('create', LibraryFile::class))->toBeFalse();
});

test('a user with the upload permission can create files', function () {
    $user = makeUser();
    Gate::define('Upload Files', fn (User $u) => $u->is($user));

    expect(Gate::forUser($user)->allows('create', LibraryFile::class))->toBeTrue();
});

test('a user with the manage permission can delete files', function () {
    $user = makeUser();
    $file = LibraryFile::create([
        'disk' => 'local',
        'path' => 'file-library/test.txt',
        'original_name' => 'test.txt',
        'size' => 10,
    ]);

    expect(Gate::forUser($user)->allows('delete', $file))->toBeFalse();

    Gate::define('Manage Files', fn (User $u) => $u->is($user));

    expect(Gate::forUser($user)->allows('delete', $file))->toBeTrue();
});

test('a user with the manage share links permission can create and manage share links', function () {
    $user = makeUser();
    $file = LibraryFile::create([
        'disk' => 'local',
        'path' => 'file-library/test.txt',
        'original_name' => 'test.txt',
        'size' => 10,
    ]);

    expect(Gate::forUser($user)->allows('createShareLink', $file))->toBeFalse();

    Gate::define('Manage File Share Links', fn (User $u) => $u->is($user));

    expect(Gate::forUser($user)->allows('createShareLink', $file))->toBeTrue();
    expect(Gate::forUser($user)->allows('manageShareLinks', $file))->toBeTrue();
});
