<?php

namespace AdamMarshall\FilamentFileLibrary\Filament\Resources\Files;

use AdamMarshall\FilamentFileLibrary\Filament\Resources\Files\Pages\ManageLibraryFiles;
use AdamMarshall\FilamentFileLibrary\Filament\Resources\Files\Schemas\LibraryFileForm;
use AdamMarshall\FilamentFileLibrary\Filament\Resources\Files\Tables\LibraryFilesTable;
use AdamMarshall\FilamentFileLibrary\Models\LibraryFile;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class LibraryFileResource extends Resource
{
    protected static ?string $model = LibraryFile::class;

    protected static ?string $modelLabel = 'File';

    protected static ?string $recordTitleAttribute = 'original_name';

    public static function getNavigationIcon(): string|BackedEnum|null
    {
        return config('filament-file-library.navigation_icon');
    }

    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return config('filament-file-library.navigation_group');
    }

    public static function getNavigationSort(): ?int
    {
        return config('filament-file-library.navigation_sort');
    }

    public static function form(Schema $schema): Schema
    {
        return LibraryFileForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LibraryFilesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageLibraryFiles::route('/{folder?}'),
        ];
    }
}
