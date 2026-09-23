<?php

namespace AdamMarshall\FilamentFileLibrary\Filament\Resources\Files\Pages;

use AdamMarshall\FilamentFileLibrary\Filament\Resources\Files\LibraryFileResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Support\Facades\Storage;

class ManageLibraryFiles extends ManageRecords
{
    protected static string $resource = LibraryFileResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->authorize('create')
                ->mutateFormDataUsing(function (array $data) {
                    $disk = config('filament-file-library.disk');

                    $data['disk'] = $disk;
                    $data['mime_type'] = Storage::disk($disk)->mimeType($data['path']);
                    $data['size'] = Storage::disk($disk)->size($data['path']);
                    $data['uploaded_by'] = auth()->id();

                    return $data;
                }),
        ];
    }
}
