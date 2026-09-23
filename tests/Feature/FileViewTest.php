<?php

use AdamMarshall\FilamentFileLibrary\Models\LibraryFile;
use AdamMarshall\FilamentFileLibrary\Tests\User;
use Illuminate\Support\Facades\Storage;

function makeViewer(): User
{
    return User::create(['name' => 'Viewer', 'email' => uniqid('viewer').'@example.com', 'password' => 'secret']);
}

test('pdfs and images are previewable, other types are not', function () {
    expect(LibraryFile::make(['mime_type' => 'application/pdf'])->isPreviewable())->toBeTrue();
    expect(LibraryFile::make(['mime_type' => 'image/png'])->isPreviewable())->toBeTrue();
    expect(LibraryFile::make(['mime_type' => 'application/zip'])->isPreviewable())->toBeFalse();
    expect(LibraryFile::make(['mime_type' => null])->isPreviewable())->toBeFalse();
});

test('preview url is only generated for previewable files', function () {
    $pdf = LibraryFile::create(['disk' => 'local', 'path' => 'a.pdf', 'original_name' => 'a.pdf', 'mime_type' => 'application/pdf', 'size' => 1]);
    $zip = LibraryFile::create(['disk' => 'local', 'path' => 'a.zip', 'original_name' => 'a.zip', 'mime_type' => 'application/zip', 'size' => 1]);

    expect($pdf->previewUrl())->not->toBeNull();
    expect($zip->previewUrl())->toBeNull();
});

test('an authorized authenticated user can view a previewable file inline', function () {
    Storage::disk('local')->put('a.pdf', 'pdf-bytes');
    $file = LibraryFile::create(['disk' => 'local', 'path' => 'a.pdf', 'original_name' => 'a.pdf', 'mime_type' => 'application/pdf', 'size' => 9]);

    $response = $this->actingAs(makeViewer())->get($file->previewUrl());

    $response->assertOk();
    expect($response->headers->get('content-disposition'))->toContain('inline');
});

test('a non-previewable file 404s on the view route even for an authenticated user', function () {
    Storage::disk('local')->put('a.zip', 'zip-bytes');
    $file = LibraryFile::create(['disk' => 'local', 'path' => 'a.zip', 'original_name' => 'a.zip', 'mime_type' => 'application/zip', 'size' => 9]);

    $this->actingAs(makeViewer())
        ->get(route('filament-file-library.view-file', $file))
        ->assertNotFound();
});

test('a guest is redirected away from the view route', function () {
    Storage::disk('local')->put('a.pdf', 'pdf-bytes');
    $file = LibraryFile::create(['disk' => 'local', 'path' => 'a.pdf', 'original_name' => 'a.pdf', 'mime_type' => 'application/pdf', 'size' => 9]);

    $this->get($file->previewUrl())->assertRedirect();
});
