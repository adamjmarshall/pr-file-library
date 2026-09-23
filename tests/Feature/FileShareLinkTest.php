<?php

use AdamMarshall\FilamentFileLibrary\Models\FileShareLink;
use AdamMarshall\FilamentFileLibrary\Models\LibraryFile;
use Illuminate\Support\Facades\Storage;

function makeFile(): LibraryFile
{
    Storage::disk('local')->put('file-library/test.txt', 'hello world');

    return LibraryFile::create([
        'disk' => 'local',
        'path' => 'file-library/test.txt',
        'original_name' => 'test.txt',
        'mime_type' => 'text/plain',
        'size' => 11,
    ]);
}

test('a fresh link is usable', function () {
    $link = FileShareLink::create(['file_id' => makeFile()->id]);

    expect($link->isUsable())->toBeTrue();
    expect($link->token)->toHaveLength(40);
});

test('a revoked link is not usable', function () {
    $link = FileShareLink::create(['file_id' => makeFile()->id, 'revoked_at' => now()]);

    expect($link->isUsable())->toBeFalse();
});

test('an expired link is not usable', function () {
    $link = FileShareLink::create(['file_id' => makeFile()->id, 'expires_at' => now()->subMinute()]);

    expect($link->isUsable())->toBeFalse();
});

test('a link that reached its download limit is not usable', function () {
    $link = FileShareLink::create([
        'file_id' => makeFile()->id,
        'max_downloads' => 2,
        'download_count' => 2,
    ]);

    expect($link->isUsable())->toBeFalse();
});

test('the public download route serves a usable link inline and increments its count', function () {
    $file = makeFile();
    $link = FileShareLink::create(['file_id' => $file->id]);

    $response = $this->get($link->url());

    $response->assertOk();
    expect($response->headers->get('content-disposition'))->toContain('inline');

    expect($link->fresh()->download_count)->toBe(1);
});

test('the public download route rejects a revoked link', function () {
    $file = makeFile();
    $link = FileShareLink::create(['file_id' => $file->id, 'revoked_at' => now()]);

    $this->get($link->url())->assertStatus(410);
});

test('the public download route 404s for an unknown token', function () {
    $this->get(url(config('filament-file-library.share_route_prefix').'/does-not-exist'))->assertNotFound();
});
