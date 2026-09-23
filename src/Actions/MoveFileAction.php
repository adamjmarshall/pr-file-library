<?php

namespace AdamMarshall\FilamentFileLibrary\Actions;

use AdamMarshall\FilamentFileLibrary\Models\Folder;
use AdamMarshall\FilamentFileLibrary\Models\LibraryEntry;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Gate;

class MoveFileAction
{
    public static function make(): Action
    {
        return Action::make('moveToFolder')
            ->label('Move')
            ->icon(Heroicon::OutlinedFolderArrowDown)
            ->visible(fn (LibraryEntry $record) => $record->isFile() && Gate::allows('moveFile', $record->toLibraryFile()))
            ->schema([
                Select::make('folder_id')
                    ->label('Folder')
                    ->placeholder('Root (no folder)')
                    ->options(fn () => self::folderOptions())
                    ->searchable(),
            ])
            ->fillForm(fn (LibraryEntry $record) => ['folder_id' => $record->toLibraryFile()->folder_id])
            ->action(function (array $data, LibraryEntry $record) {
                $file = $record->toLibraryFile();

                Gate::authorize('moveFile', $file);

                $file->update(['folder_id' => $data['folder_id'] ?? null]);
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
