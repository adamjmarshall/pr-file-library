<?php

namespace AdamMarshall\FilamentFileLibrary\Policies;

use AdamMarshall\FilamentFileLibrary\Models\Folder;
use Illuminate\Foundation\Auth\User;

class FolderPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Folder $folder): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->can(config('filament-file-library.permissions.upload'));
    }

    public function update(User $user, Folder $folder): bool
    {
        return $user->can(config('filament-file-library.permissions.upload'));
    }

    public function delete(User $user, Folder $folder): bool
    {
        return $user->can(config('filament-file-library.permissions.upload'));
    }
}
