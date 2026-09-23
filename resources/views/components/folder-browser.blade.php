@php
    $resource = \AdamMarshall\FilamentFileLibrary\Filament\Resources\Files\LibraryFileResource::class;
@endphp

<div class="space-y-4">
    <nav class="flex flex-wrap items-center gap-1 text-sm text-gray-500 dark:text-gray-400">
        <a href="{{ $resource::getUrl('index') }}" wire:navigate class="hover:text-primary-600 dark:hover:text-primary-400">
            Root
        </a>

        @foreach ($breadcrumbs as $ancestor)
            <span>/</span>
            <a href="{{ $resource::getUrl('index', ['folder' => $ancestor]) }}" wire:navigate class="hover:text-primary-600 dark:hover:text-primary-400">
                {{ $ancestor->name }}
            </a>
        @endforeach

        @if ($folder)
            <span>/</span>
            <span class="font-medium text-gray-950 dark:text-white">{{ $folder->name }}</span>
        @endif
    </nav>

    @if ($subfolders->isNotEmpty())
        <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4">
            @foreach ($subfolders as $subfolder)
                <div class="group relative flex items-center gap-2 rounded-lg border border-gray-200 p-3 dark:border-white/10">
                    <a href="{{ $resource::getUrl('index', ['folder' => $subfolder]) }}" wire:navigate class="flex min-w-0 flex-1 items-center gap-2">
                        <x-heroicon-o-folder class="h-6 w-6 shrink-0 text-warning-500" />
                        <span class="truncate text-sm font-medium text-gray-950 dark:text-white">{{ $subfolder->name }}</span>
                    </a>

                    @can('delete', $subfolder)
                        <button
                            type="button"
                            wire:click="mountAction('deleteFolder', { folder: {{ $subfolder->id }} })"
                            class="shrink-0 rounded text-gray-400 opacity-0 transition hover:text-danger-600 group-hover:opacity-100 dark:hover:text-danger-400"
                            title="Delete folder"
                        >
                            <x-heroicon-o-trash class="h-4 w-4" />
                        </button>
                    @endcan
                </div>
            @endforeach
        </div>
    @endif
</div>
