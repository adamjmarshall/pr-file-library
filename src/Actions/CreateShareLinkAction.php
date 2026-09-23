<?php

namespace AdamMarshall\FilamentFileLibrary\Actions;

use AdamMarshall\FilamentFileLibrary\Models\FileShareLink;
use AdamMarshall\FilamentFileLibrary\Models\LibraryEntry;
use Filament\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Gate;

class CreateShareLinkAction
{
    public static function make(): Action
    {
        return Action::make('createShareLink')
            ->label('Share')
            ->icon(Heroicon::OutlinedLink)
            ->visible(fn (LibraryEntry $record) => $record->isFile() && Gate::allows('createShareLink', $record->toLibraryFile()))
            ->schema([
                DateTimePicker::make('expires_at')
                    ->label('Expires at')
                    ->native(false)
                    ->helperText('Leave blank for a link that never expires.'),
                TextInput::make('max_downloads')
                    ->label('Maximum downloads')
                    ->numeric()
                    ->minValue(1)
                    ->helperText('Leave blank for unlimited downloads.'),
            ])
            ->action(function (array $data, LibraryEntry $record) {
                $file = $record->toLibraryFile();

                Gate::authorize('createShareLink', $file);

                $link = FileShareLink::query()->create([
                    'file_id' => $file->getKey(),
                    'expires_at' => $data['expires_at'] ?? null,
                    'max_downloads' => $data['max_downloads'] ?? null,
                    'created_by' => auth()->id(),
                ]);

                Notification::make()
                    ->title('Share link created')
                    ->body($link->url())
                    ->persistent()
                    ->success()
                    ->send();
            });
    }
}
