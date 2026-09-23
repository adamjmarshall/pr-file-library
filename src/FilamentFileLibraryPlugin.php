<?php

namespace AdamMarshall\FilamentFileLibrary;

use AdamMarshall\FilamentFileLibrary\Filament\Resources\Files\LibraryFileResource;
use Filament\Contracts\Plugin;
use Filament\Panel;

class FilamentFileLibraryPlugin implements Plugin
{
    protected ?string $disk = null;

    protected ?string $uploadPermission = null;

    protected ?string $manageLinksPermission = null;

    protected ?string $managePermission = null;

    protected ?string $navigationGroup = null;

    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'filament-file-library';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([
            LibraryFileResource::class,
        ]);
    }

    public function boot(Panel $panel): void
    {
        if ($this->disk !== null) {
            config(['filament-file-library.disk' => $this->disk]);
        }

        if ($this->uploadPermission !== null) {
            config(['filament-file-library.permissions.upload' => $this->uploadPermission]);
        }

        if ($this->manageLinksPermission !== null) {
            config(['filament-file-library.permissions.manage_share_links' => $this->manageLinksPermission]);
        }

        if ($this->managePermission !== null) {
            config(['filament-file-library.permissions.manage' => $this->managePermission]);
        }

        if ($this->navigationGroup !== null) {
            config(['filament-file-library.navigation_group' => $this->navigationGroup]);
        }
    }

    public function disk(string $disk): static
    {
        $this->disk = $disk;

        return $this;
    }

    public function uploadPermission(string $permission): static
    {
        $this->uploadPermission = $permission;

        return $this;
    }

    public function manageLinksPermission(string $permission): static
    {
        $this->manageLinksPermission = $permission;

        return $this;
    }

    public function managePermission(string $permission): static
    {
        $this->managePermission = $permission;

        return $this;
    }

    public function navigationGroup(string $group): static
    {
        $this->navigationGroup = $group;

        return $this;
    }
}
