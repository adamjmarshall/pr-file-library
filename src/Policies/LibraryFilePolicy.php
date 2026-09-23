<?php

namespace AdamMarshall\FilamentFileLibrary\Policies;

use AdamMarshall\FilamentFileLibrary\Models\LibraryFile;
use Illuminate\Foundation\Auth\User;

class LibraryFilePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, LibraryFile $file): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->can(config('filament-file-library.permissions.upload'));
    }

    public function update(User $user, LibraryFile $file): bool
    {
        return $user->can(config('filament-file-library.permissions.manage'));
    }

    public function delete(User $user, LibraryFile $file): bool
    {
        return $user->can(config('filament-file-library.permissions.manage'));
    }

    public function createShareLink(User $user, LibraryFile $file): bool
    {
        return $user->can(config('filament-file-library.permissions.manage_share_links'));
    }

    public function manageShareLinks(User $user, LibraryFile $file): bool
    {
        return $user->can(config('filament-file-library.permissions.manage_share_links'));
    }
}
