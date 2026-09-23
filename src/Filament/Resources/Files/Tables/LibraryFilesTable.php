<?php

namespace AdamMarshall\FilamentFileLibrary\Filament\Resources\Files\Tables;

use AdamMarshall\FilamentFileLibrary\Actions\CreateShareLinkAction;
use AdamMarshall\FilamentFileLibrary\Actions\ManageShareLinksAction;
use AdamMarshall\FilamentFileLibrary\Actions\MoveFileAction;
use AdamMarshall\FilamentFileLibrary\Enums\FileType;
use AdamMarshall\FilamentFileLibrary\Filament\Resources\Files\LibraryFileResource;
use AdamMarshall\FilamentFileLibrary\Models\LibraryEntry;
use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Gate;

class LibraryFilesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->weight('medium')
                    ->icon(fn (LibraryEntry $record) => $record->isFolder() ? Heroicon::OutlinedFolder : Heroicon::OutlinedDocument)
                    ->iconColor(fn (LibraryEntry $record) => $record->isFolder() ? 'warning' : 'gray'),
                TextColumn::make('size')
                    ->label('Size')
                    ->formatStateUsing(fn (LibraryEntry $record) => $record->humanReadableSize() ?? '—'),
                TextColumn::make('uploader.id')
                    ->label('Uploaded by')
                    ->formatStateUsing(fn (LibraryEntry $record) => $record->uploader?->getFilamentName() ?? '—'),
                TextColumn::make('created_at')
                    ->label('Uploaded')
                    ->dateTime(),
            ])
            ->filters([
                SelectFilter::make('mime_type')
                    ->label('Type')
                    ->options(FileType::class)
                    ->multiple(),
            ])
            ->recordUrl(
                fn (LibraryEntry $record) => $record->isFolder()
                    ? LibraryFileResource::getUrl('index', ['folder' => $record->id])
                    : $record->toLibraryFile()?->previewUrl(),
                shouldOpenInNewTab: fn (LibraryEntry $record) => $record->isFile(),
            )
            ->recordActions([
                Action::make('download')
                    ->label('Download')
                    ->icon(Heroicon::OutlinedArrowDownTray)
                    ->visible(fn (LibraryEntry $record) => $record->isFile() && Gate::allows('view', $record->toLibraryFile()))
                    ->action(fn (LibraryEntry $record) => $record->toLibraryFile()->download()),
                MoveFileAction::make(),
                CreateShareLinkAction::make(),
                ManageShareLinksAction::make(),
                Action::make('deleteFile')
                    ->label('Delete')
                    ->icon(Heroicon::OutlinedTrash)
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (LibraryEntry $record) => $record->isFile() && Gate::allows('delete', $record->toLibraryFile()))
                    ->action(function (LibraryEntry $record) {
                        $file = $record->toLibraryFile();

                        Gate::authorize('delete', $file);

                        $file->delete();
                    }),
                Action::make('deleteFolder')
                    ->label('Delete')
                    ->icon(Heroicon::OutlinedTrash)
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalDescription('This will permanently delete the folder, its subfolders, and every file inside them.')
                    ->visible(fn (LibraryEntry $record) => $record->isFolder() && Gate::allows('delete', $record->toFolder()))
                    ->action(function (LibraryEntry $record) {
                        $folder = $record->toFolder();

                        Gate::authorize('delete', $folder);

                        $folder->deleteWithContents();
                    }),
            ]);
    }
}
