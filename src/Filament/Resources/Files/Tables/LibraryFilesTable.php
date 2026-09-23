<?php

namespace AdamMarshall\FilamentFileLibrary\Filament\Resources\Files\Tables;

use AdamMarshall\FilamentFileLibrary\Actions\CreateShareLinkAction;
use AdamMarshall\FilamentFileLibrary\Actions\ManageShareLinksAction;
use AdamMarshall\FilamentFileLibrary\Models\LibraryFile;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class LibraryFilesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('original_name')
                    ->label('Name')
                    ->searchable()
                    ->weight('medium'),
                TextColumn::make('mime_type')
                    ->label('Type')
                    ->badge()
                    ->color('gray'),
                TextColumn::make('size')
                    ->label('Size')
                    ->formatStateUsing(fn (LibraryFile $record) => $record->humanReadableSize()),
                TextColumn::make('uploader.id')
                    ->label('Uploaded by')
                    ->formatStateUsing(fn (LibraryFile $record) => $record->uploader?->getFilamentName() ?? '—'),
                TextColumn::make('created_at')
                    ->label('Uploaded')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                Action::make('download')
                    ->label('Download')
                    ->icon(Heroicon::OutlinedArrowDownTray)
                    ->action(fn (LibraryFile $record) => $record->download())
                    ->authorize('view'),
                CreateShareLinkAction::make(),
                ManageShareLinksAction::make(),
                DeleteAction::make()
                    ->authorize('delete'),
            ]);
    }
}
