<?php

namespace AdamMarshall\FilamentFileLibrary;

use AdamMarshall\FilamentFileLibrary\Models\FileShareLink;
use AdamMarshall\FilamentFileLibrary\Models\Folder;
use AdamMarshall\FilamentFileLibrary\Models\LibraryFile;
use AdamMarshall\FilamentFileLibrary\Policies\FileShareLinkPolicy;
use AdamMarshall\FilamentFileLibrary\Policies\FolderPolicy;
use AdamMarshall\FilamentFileLibrary\Policies\LibraryFilePolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class FilamentFileLibraryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/filament-file-library.php', 'filament-file-library');
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'filament-file-library');

        Gate::policy(LibraryFile::class, LibraryFilePolicy::class);
        Gate::policy(FileShareLink::class, FileShareLinkPolicy::class);
        Gate::policy(Folder::class, FolderPolicy::class);

        $this->publishes([
            __DIR__.'/../config/filament-file-library.php' => config_path('filament-file-library.php'),
        ], 'filament-file-library-config');

        $this->publishesMigrations([
            __DIR__.'/../database/migrations' => database_path('migrations'),
        ], 'filament-file-library-migrations');
    }
}
