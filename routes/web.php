<?php

use AdamMarshall\FilamentFileLibrary\Http\Controllers\PublicDownloadController;
use AdamMarshall\FilamentFileLibrary\Http\Controllers\RevokeShareLinkController;
use AdamMarshall\FilamentFileLibrary\Http\Controllers\ViewFileController;
use Illuminate\Support\Facades\Route;

Route::middleware('web')->group(function () {
    Route::get(
        config('filament-file-library.share_route_prefix').'/{token}',
        PublicDownloadController::class
    )->name('filament-file-library.share-download');

    Route::middleware('auth')->group(function () {
        Route::post(
            'file-library/links/{link}/revoke',
            RevokeShareLinkController::class
        )->name('filament-file-library.revoke-share-link');

        Route::get(
            'file-library/view/{file}',
            ViewFileController::class
        )->name('filament-file-library.view-file');
    });
});
