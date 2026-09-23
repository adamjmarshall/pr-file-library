# Filament File Library

A simple shared file-hosting area for [Filament](https://filamentphp.com) v5 admin panels:

- Any authenticated panel user can browse and view an index of uploaded files.
- Users with an "upload" permission can upload new files.
- Users with a "manage share links" permission can create revocable, expirable signed links so people who aren't logged in can download a specific file.
- Users with a "manage" permission can delete files.

Permission checks go through Laravel's standard `$user->can()` / policy system, so this works out of the box with [spatie/laravel-permission](https://spatie.be/docs/laravel-permission) — or any other Gate-based authorization — in any host application, without depending on that application's own permission model.

## Installation

```bash
composer require adamjmarshal275/filament-file-library
php artisan migrate
```

Add a dedicated storage disk in `config/filesystems.php` (any driver works: `local`, `s3`, etc.) and point the plugin at it, then register the plugin in your panel provider:

```php
use AdamMarshall\FilamentFileLibrary\FilamentFileLibraryPlugin;

public function panel(Panel $panel): Panel
{
    return $panel
        // ...
        ->plugins([
            FilamentFileLibraryPlugin::make()
                ->disk('file-library'),
        ]);
}
```

## Permissions

By default the plugin checks for permissions named `Upload Files`, `Manage File Share Links`, and `Manage Files` (see `config/filament-file-library.php`). Create permission records with these exact names in your host app (however it manages permissions) and assign them to the appropriate roles.

To use different permission names, either publish and edit the config file:

```bash
php artisan vendor:publish --tag=filament-file-library-config
```

or override them fluently per panel:

```php
FilamentFileLibraryPlugin::make()
    ->disk('file-library')
    ->uploadPermission('files.upload')
    ->manageLinksPermission('files.share')
    ->managePermission('files.manage');
```

## Signed share links

Share links are backed by a database row with a cryptographically random token (not Laravel's stateless HMAC-signed routes), so they can be revoked, given an expiry, and capped to a maximum number of downloads — the same approach services like Dropbox use for shareable links. The public download route is registered outside the panel and requires no authentication.

## Testing

```bash
composer install
composer test
```
