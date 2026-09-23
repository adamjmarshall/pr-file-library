<?php

namespace AdamMarshall\FilamentFileLibrary\Actions;

use AdamMarshall\FilamentFileLibrary\Models\LibraryEntry;
use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Gate;

class ManageShareLinksAction
{
    public static function make(): Action
    {
        return Action::make('manageShareLinks')
            ->label('Links')
            ->icon(Heroicon::OutlinedQueueList)
            ->color('gray')
            ->visible(fn (LibraryEntry $record) => $record->isFile() && Gate::allows('manageShareLinks', $record->toLibraryFile()))
            ->modalHeading(fn (LibraryEntry $record) => "Share links for {$record->name}")
            ->modalContent(function (LibraryEntry $record) {
                $file = $record->toLibraryFile();

                Gate::authorize('manageShareLinks', $file);

                return view(
                    'filament-file-library::share-links-modal',
                    ['file' => $file->loadMissing(['shareLinks' => fn ($query) => $query->latest()])]
                );
            })
            ->modalSubmitAction(false)
            ->modalCancelActionLabel('Close');
    }
}
