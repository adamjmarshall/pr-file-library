<?php

namespace AdamMarshall\FilamentFileLibrary\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class LibraryFile extends Model
{
    use HasFactory;

    protected $table = 'file_library_files';

    protected $fillable = [
        'folder_id',
        'disk',
        'path',
        'original_name',
        'mime_type',
        'size',
        'uploaded_by',
    ];

    protected function casts(): array
    {
        return [
            'size' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::deleting(function (self $file) {
            Storage::disk($file->disk)->delete($file->path);
        });
    }

    public function folder(): BelongsTo
    {
        return $this->belongsTo(Folder::class, 'folder_id');
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model'), 'uploaded_by');
    }

    public function shareLinks(): HasMany
    {
        return $this->hasMany(FileShareLink::class, 'file_id');
    }

    public function humanReadableSize(): string
    {
        $bytes = $this->size;
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes >= 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $bytes < 10 ? 1 : 0).' '.$units[$i];
    }

    public function download()
    {
        return Storage::disk($this->disk)->download($this->path, $this->original_name);
    }

    public function view()
    {
        return Storage::disk($this->disk)->response($this->path, $this->original_name);
    }

    public function isPreviewable(): bool
    {
        return in_array($this->mime_type, [
            'application/pdf',
            'image/jpeg',
            'image/png',
            'image/gif',
            'image/webp',
            'image/svg+xml',
        ], true);
    }

    public function previewUrl(): ?string
    {
        if (! $this->isPreviewable()) {
            return null;
        }

        return route('filament-file-library.view-file', $this);
    }
}
