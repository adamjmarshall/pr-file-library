<?php

use AdamMarshall\FilamentFileLibrary\Filament\Resources\Files\Pages\ManageLibraryFiles;
use AdamMarshall\FilamentFileLibrary\Models\Folder;
use AdamMarshall\FilamentFileLibrary\Models\LibraryFile;
use AdamMarshall\FilamentFileLibrary\Tests\User;
use Illuminate\Support\Facades\Gate;
use Livewire\Livewire;

test('the table only shows files in the current folder', function () {
    $user = User::create(['name' => 'Browser', 'email' => 'browser@example.com', 'password' => 'secret']);
    $folderA = Folder::create(['name' => 'A']);
    $folderB = Folder::create(['name' => 'B']);

    $rootFile = LibraryFile::create(['disk' => 'local', 'path' => 'root.txt', 'original_name' => 'root.txt', 'size' => 1]);
    $fileInA = LibraryFile::create(['folder_id' => $folderA->id, 'disk' => 'local', 'path' => 'a.txt', 'original_name' => 'a.txt', 'size' => 1]);
    $fileInB = LibraryFile::create(['folder_id' => $folderB->id, 'disk' => 'local', 'path' => 'b.txt', 'original_name' => 'b.txt', 'size' => 1]);

    Livewire::actingAs($user)
        ->test(ManageLibraryFiles::class)
        ->assertCanSeeTableRecords([$rootFile])
        ->assertCanNotSeeTableRecords([$fileInA, $fileInB]);

    Livewire::actingAs($user)
        ->test(ManageLibraryFiles::class, ['folder' => $folderA])
        ->assertCanSeeTableRecords([$fileInA])
        ->assertCanNotSeeTableRecords([$rootFile, $fileInB]);
});

test('folders and files appear together in the same table, folders first', function () {
    $user = User::create(['name' => 'Browser', 'email' => 'together@example.com', 'password' => 'secret']);
    $folder = Folder::create(['name' => 'Zeta Folder']);
    $file = LibraryFile::create(['disk' => 'local', 'path' => 'alpha.txt', 'original_name' => 'Alpha File', 'size' => 1]);

    $component = Livewire::actingAs($user)->test(ManageLibraryFiles::class);

    $names = $component->instance()->getTable()->getRecords()->pluck('name')->all();

    expect($names)->toBe(['Zeta Folder', 'Alpha File']);
});

test('clicking a folder row navigates into it rather than downloading', function () {
    $user = User::create(['name' => 'Browser', 'email' => 'clicker@example.com', 'password' => 'secret']);
    $folder = Folder::create(['name' => 'Nested']);

    $component = Livewire::actingAs($user)->test(ManageLibraryFiles::class);

    $entry = $component->instance()->getTable()->getRecords()->firstWhere('type', 'folder');

    expect($entry)->not->toBeNull();
    expect($component->instance()->getTable()->getRecordUrl($entry))
        ->toContain((string) $folder->getKey());
    expect($component->instance()->getTable()->shouldOpenRecordUrlInNewTab($entry))->toBeFalse();
});

test('a user with the upload permission can create a subfolder in the current folder', function () {
    $user = User::create(['name' => 'Uploader', 'email' => 'uploader@example.com', 'password' => 'secret']);
    Gate::define('Upload Files', fn (User $u) => $u->is($user));

    $parent = Folder::create(['name' => 'Parent']);

    Livewire::actingAs($user)
        ->test(ManageLibraryFiles::class, ['folder' => $parent])
        ->callAction('createFolder', data: ['name' => 'Child'])
        ->assertHasNoActionErrors();

    $child = Folder::where('name', 'Child')->firstOrFail();
    expect($child->parent_id)->toBe($parent->id);
    expect($child->created_by)->toBe($user->id);
});

test('a user with the upload permission can create a root-level folder', function () {
    $user = User::create(['name' => 'Uploader', 'email' => 'root-uploader@example.com', 'password' => 'secret']);
    Gate::define('Upload Files', fn (User $u) => $u->is($user));

    Livewire::actingAs($user)
        ->test(ManageLibraryFiles::class)
        ->callAction('createFolder', data: ['name' => 'Top Level'])
        ->assertHasNoActionErrors();

    $folder = Folder::where('name', 'Top Level')->firstOrFail();
    expect($folder->parent_id)->toBeNull();
});

test('a user without the upload permission cannot see the new folder action', function () {
    $user = User::create(['name' => 'Viewer', 'email' => 'viewer@example.com', 'password' => 'secret']);

    Livewire::actingAs($user)
        ->test(ManageLibraryFiles::class)
        ->assertActionHidden('createFolder');
});

test('deleting a folder via its row action removes it and its contents', function () {
    $user = User::create(['name' => 'Uploader', 'email' => 'uploader@example.com', 'password' => 'secret']);
    Gate::define('Upload Files', fn (User $u) => $u->is($user));

    $folder = Folder::create(['name' => 'Doomed']);

    Livewire::actingAs($user)
        ->test(ManageLibraryFiles::class)
        ->callTableAction('deleteFolder', -$folder->id)
        ->assertHasNoTableActionErrors();

    expect(Folder::find($folder->id))->toBeNull();
});

test('a user without the upload permission cannot see the delete action on a folder row', function () {
    $user = User::create(['name' => 'Viewer', 'email' => 'viewer@example.com', 'password' => 'secret']);
    $folder = Folder::create(['name' => 'Protected']);

    Livewire::actingAs($user)
        ->test(ManageLibraryFiles::class)
        ->assertTableActionHidden('deleteFolder', -$folder->id);

    expect(Folder::find($folder->id))->not->toBeNull();
});

test('uploading a file inside a folder files it into that folder', function () {
    $user = User::create(['name' => 'Uploader', 'email' => 'uploader@example.com', 'password' => 'secret']);
    Gate::define('Upload Files', fn (User $u) => $u->is($user));

    $folder = Folder::create(['name' => 'Docs']);

    Livewire::actingAs($user)
        ->test(ManageLibraryFiles::class, ['folder' => $folder])
        ->callAction('create', data: [
            'path' => [\Illuminate\Http\UploadedFile::fake()->create('report.pdf', 10)],
        ])
        ->assertHasNoActionErrors();

    $file = LibraryFile::firstOrFail();
    expect($file->folder_id)->toBe($folder->id);
});

test('a user with the upload permission can move a file to a different folder', function () {
    $user = User::create(['name' => 'Uploader', 'email' => 'uploader@example.com', 'password' => 'secret']);
    Gate::define('Upload Files', fn (User $u) => $u->is($user));

    $folder = Folder::create(['name' => 'Target']);
    $file = LibraryFile::create(['disk' => 'local', 'path' => 'x.txt', 'original_name' => 'x.txt', 'size' => 1]);

    Livewire::actingAs($user)
        ->test(ManageLibraryFiles::class)
        ->callTableAction('moveToFolder', $file, data: ['folder_id' => $folder->id])
        ->assertHasNoTableActionErrors();

    expect($file->fresh()->folder_id)->toBe($folder->id);
});

test('a user without the upload permission cannot move a file', function () {
    $user = User::create(['name' => 'Viewer', 'email' => 'viewer@example.com', 'password' => 'secret']);
    $file = LibraryFile::create(['disk' => 'local', 'path' => 'x.txt', 'original_name' => 'x.txt', 'size' => 1]);

    Livewire::actingAs($user)
        ->test(ManageLibraryFiles::class)
        ->assertTableActionHidden('moveToFolder', $file);
});
