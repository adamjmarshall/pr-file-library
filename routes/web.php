<?php

use AdamMarshall\FilamentFileLibrary\Http\Controllers\PublicDownloadController;
use AdamMarshall\FilamentFileLibrary\Http\Controllers\RevokeShareLinkController;
use Illuminate\Support\Facades\Route;

Route::middleware('web')->group(function () {
    Route::get(
        config('filament-file-library.share_route_prefix').'/{token}',
        PublicDownloadController::class
    )->name('filament-file-library.share-download');

    Route::middleware('auth')->post(
        'file-library/links/{link}/revoke',
        RevokeShareLinkController::class
    )->name('filament-file-library.revoke-share-link');
});
