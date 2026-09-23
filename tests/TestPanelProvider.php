<?php

namespace AdamMarshall\FilamentFileLibrary\Tests;

use AdamMarshall\FilamentFileLibrary\FilamentFileLibraryPlugin;
use Filament\Panel;
use Filament\PanelProvider;

class TestPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->authGuard('web')
            ->plugin(FilamentFileLibraryPlugin::make());
    }
}
