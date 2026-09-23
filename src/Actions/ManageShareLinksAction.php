<?php

namespace AdamMarshall\FilamentFileLibrary\Actions;

use AdamMarshall\FilamentFileLibrary\Models\LibraryFile;
use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;

class ManageShareLinksAction
{
    public static function make(): Action
    {
        return Action::make('manageShareLinks')
            ->label('Links')
            ->icon(Heroicon::OutlinedQueueList)
            ->color('gray')
            ->authorize('manageShareLinks')
            ->modalHeading(fn (LibraryFile $record) => "Share links for {$record->original_name}")
            ->modalContent(fn (LibraryFile $record) => view(
                'filament-file-library::share-links-modal',
                ['file' => $record->loadMissing(['shareLinks' => fn ($query) => $query->latest()])]
            ))
            ->modalSubmitAction(false)
            ->modalCancelActionLabel('Close');
    }
}
