<?php

namespace AdamMarshall\FilamentFileLibrary\Filament\Resources\Files\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Schemas\Schema;

class LibraryFileForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Hidden::make('original_name'),
            FileUpload::make('path')
                ->label('File')
                ->disk(fn () => config('filament-file-library.disk'))
                ->directory(fn () => config('filament-file-library.directory'))
                ->visibility('private')
                ->storeFileNamesIn('original_name')
                ->required()
                ->columnSpanFull(),
        ]);
    }
}
