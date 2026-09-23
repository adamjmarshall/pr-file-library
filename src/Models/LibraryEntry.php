<?php

namespace AdamMarshall\FilamentFileLibrary\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A read-only row representing either a Folder or a LibraryFile, so both can
 * be listed together in a single Filament table. Backed by a UNION of the
 * two real tables rather than a table of its own — `entry_id` disambiguates
 * rows across them (folder ids negated, file ids left as-is), since the two
 * source tables each have their own auto-incrementing `id` sequence.
 */
class LibraryEntry extends Model
{
    protected $table = 'file_library_entries';

    protected $primaryKey = 'entry_id';

    public $incrementing = false;

    protected $keyType = 'int';

    public $timestamps = false;

    protected function casts(): array
    {
        return [
            'size' => 'integer',
            'created_at' => 'datetime',
        ];
    }

    public function newEloquentBuilder($query): LibraryEntryBuilder
    {
        return new LibraryEntryBuilder($query);
    }

    public static function queryFor(?int $folderId): Builder
    {
        $folders = Folder::query()
            ->where('parent_id', $folderId)
            ->selectRaw("-id as entry_id, id, 'folder' as type, name, NULL as mime_type, NULL as size, created_by as uploaded_by, created_at, 0 as sort_group");

        $files = LibraryFile::query()
            ->where('folder_id', $folderId)
            ->selectRaw("id as entry_id, id, 'file' as type, original_name as name, mime_type, size, uploaded_by, created_at, 1 as sort_group");

        return static::query()
            ->fromSub($folders->unionAll($files), (new static)->getTable())
            ->orderBy('sort_group')
            ->orderBy('name');
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model'), 'uploaded_by');
    }

    public function isFolder(): bool
    {
        return $this->type === 'folder';
    }

    public function isFile(): bool
    {
        return $this->type === 'file';
    }

    public function toFolder(): ?Folder
    {
        return $this->isFolder() ? Folder::find($this->id) : null;
    }

    public function toLibraryFile(): ?LibraryFile
    {
        return $this->isFile() ? LibraryFile::find($this->id) : null;
    }

    public function humanReadableSize(): ?string
    {
        return $this->toLibraryFile()?->humanReadableSize();
    }
}
