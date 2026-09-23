@php
    $resource = \AdamMarshall\FilamentFileLibrary\Filament\Resources\Files\LibraryFileResource::class;
@endphp

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
