<?php

use AdamMarshall\FilamentFileLibrary\Filament\Resources\Files\LibraryFileResource;
use AdamMarshall\FilamentFileLibrary\Models\LibraryFile;
use AdamMarshall\FilamentFileLibrary\Tests\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Gate;
use Livewire\Livewire;

test('an authenticated user can render the file index page', function () {
    $user = User::create(['name' => 'Viewer', 'email' => 'viewer@example.com', 'password' => 'secret']);

    Livewire::actingAs($user)
        ->test(LibraryFileResource::getPages()['index']->getPage())
        ->assertSuccessful();
});

test('uploading a file records its metadata against the uploading user', function () {
    $user = User::create(['name' => 'Uploader', 'email' => 'uploader@example.com', 'password' => 'secret']);
    Gate::define('Upload Files', fn (User $u) => $u->is($user));

    Livewire::actingAs($user)
        ->test(LibraryFileResource::getPages()['index']->getPage())
        ->callAction('create', data: [
            'path' => [UploadedFile::fake()->create('report.pdf', 10)],
        ])
        ->assertHasNoActionErrors();

    $file = LibraryFile::first();

    expect($file)->not->toBeNull();
    expect($file->original_name)->toBe('report.pdf');
    expect($file->uploaded_by)->toBe($user->id);
});
