<?php

namespace AdamMarshall\FilamentFileLibrary\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class FileShareLink extends Model
{
    use HasFactory;

    protected $table = 'file_library_file_share_links';

    protected $fillable = [
        'file_id',
        'token',
        'expires_at',
        'max_downloads',
        'download_count',
        'revoked_at',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'revoked_at' => 'datetime',
            'max_downloads' => 'integer',
            'download_count' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $link) {
            $link->token ??= Str::random(40);
        });
    }

    public function file(): BelongsTo
    {
        return $this->belongsTo(LibraryFile::class, 'file_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model'), 'created_by');
    }

    public function isRevoked(): bool
    {
        return $this->revoked_at !== null;
    }

    public function isExpired(): bool
    {
        return $this->expires_at !== null && $this->expires_at->isPast();
    }

    public function hasReachedDownloadLimit(): bool
    {
        return $this->max_downloads !== null && $this->download_count >= $this->max_downloads;
    }

    public function isUsable(): bool
    {
        return ! $this->isRevoked() && ! $this->isExpired() && ! $this->hasReachedDownloadLimit();
    }

    public function url(): string
    {
        return url(config('filament-file-library.share_route_prefix').'/'.$this->token);
    }
}
