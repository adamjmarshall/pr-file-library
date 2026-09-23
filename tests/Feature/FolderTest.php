<?php

use AdamMarshall\FilamentFileLibrary\Models\Folder;
use AdamMarshall\FilamentFileLibrary\Models\LibraryFile;
use AdamMarshall\FilamentFileLibrary\Tests\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

function makeFolder(?Folder $parent = null, string $name = 'Folder'): Folder
{
    return Folder::create(['name' => $name, 'parent_id' => $parent?->id]);
}

test('ancestors returns the chain from root to the immediate parent, in order', function () {
    $root = makeFolder(name: 'Root');
    $child = makeFolder($root, 'Child');
    $grandchild = makeFolder($child, 'Grandchild');

    expect($grandchild->ancestors())->toHaveCount(2);
    expect(collect($grandchild->ancestors())->pluck('name')->all())->toBe(['Root', 'Child']);
    expect($root->ancestors())->toBe([]);
});

test('a user without the upload permission cannot create, rename, or delete folders', function () {
    $user = User::create(['name' => 'Viewer', 'email' => 'viewer@example.com', 'password' => 'secret']);
    $folder = makeFolder();

    expect(Gate::forUser($user)->allows('create', Folder::class))->toBeFalse();
    expect(Gate::forUser($user)->allows('update', $folder))->toBeFalse();
    expect(Gate::forUser($user)->allows('delete', $folder))->toBeFalse();
});

test('a user with the upload permission can create, rename, and delete folders', function () {
    $user = User::create(['name' => 'Uploader', 'email' => 'uploader@example.com', 'password' => 'secret']);
    Gate::define('Upload Files', fn (User $u) => $u->is($user));
    $folder = makeFolder();

    expect(Gate::forUser($user)->allows('create', Folder::class))->toBeTrue();
    expect(Gate::forUser($user)->allows('update', $folder))->toBeTrue();
    expect(Gate::forUser($user)->allows('delete', $folder))->toBeTrue();
});

test('deleting a folder with contents recursively removes subfolders, files, and their storage objects', function () {
    $root = makeFolder(name: 'Root');
    $child = makeFolder($root, 'Child');

    Storage::disk('local')->put('root-file.txt', 'root');
    Storage::disk('local')->put('child-file.txt', 'child');

    $rootFile = LibraryFile::create(['folder_id' => $root->id, 'disk' => 'local', 'path' => 'root-file.txt', 'original_name' => 'root-file.txt', 'size' => 4]);
    $childFile = LibraryFile::create(['folder_id' => $child->id, 'disk' => 'local', 'path' => 'child-file.txt', 'original_name' => 'child-file.txt', 'size' => 5]);

    $root->deleteWithContents();

    expect(Folder::find($root->id))->toBeNull();
    expect(Folder::find($child->id))->toBeNull();
    expect(LibraryFile::find($rootFile->id))->toBeNull();
    expect(LibraryFile::find($childFile->id))->toBeNull();
    expect(Storage::disk('local')->exists('root-file.txt'))->toBeFalse();
    expect(Storage::disk('local')->exists('child-file.txt'))->toBeFalse();
});

test('deleting a single file removes its storage object too', function () {
    Storage::disk('local')->put('solo.txt', 'solo');
    $file = LibraryFile::create(['disk' => 'local', 'path' => 'solo.txt', 'original_name' => 'solo.txt', 'size' => 4]);

    $file->delete();

    expect(Storage::disk('local')->exists('solo.txt'))->toBeFalse();
});
