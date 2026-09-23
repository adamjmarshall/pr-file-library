@php
    $links = $file->shareLinks;
@endphp

<div class="space-y-3">
    @forelse ($links as $link)
        @php
            $status = match (true) {
                $link->isRevoked() => ['label' => 'Revoked', 'class' => 'fi-color-danger'],
                $link->isExpired() => ['label' => 'Expired', 'class' => 'fi-color-gray'],
                $link->hasReachedDownloadLimit() => ['label' => 'Limit reached', 'class' => 'fi-color-gray'],
                default => ['label' => 'Active', 'class' => 'fi-color-success'],
            };
        @endphp

        <div class="rounded-lg border border-gray-200 p-3 dark:border-white/10">
            <div class="flex items-center justify-between gap-3">
                <code class="truncate text-xs">{{ $link->url() }}</code>
                <span class="{{ $status['class'] }} fi-badge inline-flex items-center rounded-md px-2 py-1 text-xs font-medium">
                    {{ $status['label'] }}
                </span>
            </div>

            <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                Created {{ $link->created_at->diffForHumans() }}
                &middot; Expires: {{ $link->expires_at?->format('d M Y H:i') ?? 'Never' }}
                &middot; Downloads: {{ $link->download_count }}{{ $link->max_downloads ? '/'.$link->max_downloads : '' }}
            </div>

            @if ($link->isUsable())
                <form method="POST" action="{{ route('filament-file-library.revoke-share-link', $link) }}" class="mt-2">
                    @csrf
                    <button type="submit" class="text-xs font-medium text-danger-600 hover:underline dark:text-danger-400">
                        Revoke
                    </button>
                </form>
            @endif
        </div>
    @empty
        <p class="text-sm text-gray-500 dark:text-gray-400">No share links have been created for this file yet.</p>
    @endforelse
</div>
