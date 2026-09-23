<?php

namespace AdamMarshall\FilamentFileLibrary\Actions;

use AdamMarshall\FilamentFileLibrary\Models\Folder;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Gate;

class CreateFolderAction
{
    public static function make(?Folder $parent): Action
    {
        return Action::make('createFolder')
            ->label('New folder')
            ->icon(Heroicon::OutlinedFolderPlus)
            ->visible(fn () => Gate::allows('create', Folder::class))
            ->schema([
                TextInput::make('name')
                    ->label('Folder name')
                    ->required()
                    ->maxLength(255),
            ])
            ->action(function (array $data) use ($parent) {
                Gate::authorize('create', Folder::class);

                Folder::query()->create([
                    'name' => $data['name'],
                    'parent_id' => $parent?->getKey(),
                    'created_by' => auth()->id(),
                ]);
            });
    }
}
