<?php

namespace AdamMarshall\FilamentFileLibrary\Filament\Resources\Files\Pages;

use AdamMarshall\FilamentFileLibrary\Actions\CreateFolderAction;
use AdamMarshall\FilamentFileLibrary\Filament\Resources\Files\LibraryFileResource;
use AdamMarshall\FilamentFileLibrary\Models\Folder;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;
use Filament\Schemas\Components\EmbeddedTable;
use Filament\Schemas\Components\RenderHook;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;
use Filament\View\PanelsRenderHook;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class ManageLibraryFiles extends ManageRecords
{
    protected static string $resource = LibraryFileResource::class;

    public ?Folder $folder = null;

    public function mount(?Folder $folder = null): void
    {
        $this->folder = $folder;

        parent::mount();
    }

    public function getTitle(): string
    {
        return $this->folder?->name ?? parent::getTitle();
    }

    protected function getTableQuery(): Builder|Relation|null
    {
        return parent::getTableQuery()?->where('folder_id', $this->folder?->getKey());
    }

    public function content(Schema $schema): Schema
    {
        return $schema->components([
            View::make('filament-file-library::components.folder-browser')
                ->viewData([
                    'folder' => $this->folder,
                    'breadcrumbs' => $this->folder?->ancestors() ?? [],
                    'subfolders' => Folder::query()
                        ->where('parent_id', $this->folder?->getKey())
                        ->orderBy('name')
                        ->get(),
                ]),
            RenderHook::make(PanelsRenderHook::RESOURCE_PAGES_LIST_RECORDS_TABLE_BEFORE),
            EmbeddedTable::make(),
            RenderHook::make(PanelsRenderHook::RESOURCE_PAGES_LIST_RECORDS_TABLE_AFTER),
        ]);
    }

    public function deleteFolderAction(): Action
    {
        return Action::make('deleteFolder')
            ->label('Delete folder')
            ->color('danger')
            ->requiresConfirmation()
            ->modalDescription('This will permanently delete the folder, its subfolders, and every file inside them.')
            ->action(function (array $arguments) {
                $folder = Folder::query()->findOrFail($arguments['folder']);

                Gate::authorize('delete', $folder);

                $folder->deleteWithContents();
            });
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateFolderAction::make($this->folder),
            CreateAction::make()
                ->authorize('create')
                ->mutateFormDataUsing(function (array $data) {
                    $disk = config('filament-file-library.disk');

                    $data['disk'] = $disk;
                    $data['mime_type'] = Storage::disk($disk)->mimeType($data['path']);
                    $data['size'] = Storage::disk($disk)->size($data['path']);
                    $data['uploaded_by'] = auth()->id();
                    $data['folder_id'] = $this->folder?->getKey();

                    return $data;
                }),
        ];
    }
}
