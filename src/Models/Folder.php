<?php

namespace AdamMarshall\FilamentFileLibrary\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Folder extends Model
{
    use HasFactory;

    protected $table = 'file_library_folders';

    protected $fillable = [
        'name',
        'parent_id',
        'created_by',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function files(): HasMany
    {
        return $this->hasMany(LibraryFile::class, 'folder_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model'), 'created_by');
    }

    /**
     * @return array<Folder>
     */
    public function ancestors(): array
    {
        $ancestors = [];
        $folder = $this->parent;

        while ($folder !== null) {
            array_unshift($ancestors, $folder);
            $folder = $folder->parent;
        }

        return $ancestors;
    }

    /**
     * Recursively deletes this folder, its subfolders, and their files —
     * going through Eloquent (rather than relying on the database's
     * cascading foreign keys) so that each file's `deleting` hook runs and
     * its underlying storage object is actually removed, not just its row.
     */
    public function deleteWithContents(): void
    {
        $this->files->each(fn (LibraryFile $file) => $file->delete());
        $this->children->each(fn (self $child) => $child->deleteWithContents());

        $this->delete();
    }
}
