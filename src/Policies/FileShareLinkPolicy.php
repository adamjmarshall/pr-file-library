<?php

namespace AdamMarshall\FilamentFileLibrary\Policies;

use AdamMarshall\FilamentFileLibrary\Models\FileShareLink;
use Illuminate\Foundation\Auth\User;

class FileShareLinkPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can(config('filament-file-library.permissions.manage_share_links'));
    }

    public function create(User $user): bool
    {
        return $user->can(config('filament-file-library.permissions.manage_share_links'));
    }

    public function delete(User $user, FileShareLink $link): bool
    {
        return $user->can(config('filament-file-library.permissions.manage_share_links'));
    }
}
