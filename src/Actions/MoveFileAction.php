<?php

namespace AdamMarshall\FilamentFileLibrary\Actions;

use AdamMarshall\FilamentFileLibrary\Models\Folder;
use AdamMarshall\FilamentFileLibrary\Models\LibraryFile;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Support\Icons\Heroicon;

class MoveFileAction
{
    public static function make(): Action
    {
        return Action::make('moveToFolder')
            ->label('Move')
            ->icon(Heroicon::OutlinedFolderArrowDown)
            ->authorize('moveFile')
            ->schema([
                Select::make('folder_id')
                    ->label('Folder')
                    ->placeholder('Root (no folder)')
                    ->options(fn () => self::folderOptions())
                    ->searchable(),
            ])
            ->fillForm(fn (LibraryFile $record) => ['folder_id' => $record->folder_id])
            ->action(function (array $data, LibraryFile $record) {
                $record->update(['folder_id' => $data['folder_id'] ?? null]);
            });
    }

    /**
     * @return array<int, string>
     */
    protected static function folderOptions(): array
    {
        return Folder::query()
            ->orderBy('name')
            ->get()
            ->mapWithKeys(fn (Folder $folder) => [
                $folder->getKey() => str_repeat('— ', count($folder->ancestors())).$folder->name,
            ])
            ->all();
    }
}
