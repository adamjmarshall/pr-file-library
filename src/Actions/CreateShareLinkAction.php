<?php

namespace AdamMarshall\FilamentFileLibrary\Actions;

use AdamMarshall\FilamentFileLibrary\Models\FileShareLink;
use AdamMarshall\FilamentFileLibrary\Models\LibraryFile;
use Filament\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;

class CreateShareLinkAction
{
    public static function make(): Action
    {
        return Action::make('createShareLink')
            ->label('Share')
            ->icon(Heroicon::OutlinedLink)
            ->authorize('createShareLink')
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
            ->action(function (array $data, LibraryFile $record) {
                $link = FileShareLink::query()->create([
                    'file_id' => $record->getKey(),
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
