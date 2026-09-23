<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Storage Disk
    |--------------------------------------------------------------------------
    |
    | The filesystem disk uploaded files are stored on. Point this at a
    | dedicated disk in the host application's config/filesystems.php.
    |
    */

    'disk' => env('FILE_LIBRARY_DISK', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Storage Path Prefix
    |--------------------------------------------------------------------------
    |
    | Directory (within the disk above) that uploaded files are stored under.
    |
    */

    'directory' => 'file-library',

    /*
    |--------------------------------------------------------------------------
    | Permission Names
    |--------------------------------------------------------------------------
    |
    | These are checked via the authenticated user's `can()` method, so they
    | work out of the box with spatie/laravel-permission (or any Laravel
    | Gate/Policy setup) as long as a permission with this exact name exists.
    | Override the strings here, or override them per-panel via the fluent
    | methods on FilamentFileLibraryPlugin.
    |
    */

    'permissions' => [
        'upload' => 'Upload Files',
        'manage_share_links' => 'Manage File Share Links',
        'manage' => 'Manage Files',
    ],

    /*
    |--------------------------------------------------------------------------
    | Public Share Route
    |--------------------------------------------------------------------------
    |
    | The URI prefix (outside the Filament panel, unauthenticated) that
    | signed share links are served from.
    |
    */

    'share_route_prefix' => 'file-library/s',

    /*
    |--------------------------------------------------------------------------
    | Navigation
    |--------------------------------------------------------------------------
    */

    'navigation_group' => null,
    'navigation_icon' => 'heroicon-o-folder',
    'navigation_sort' => null,

];
