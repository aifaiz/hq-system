<?php

namespace App\Filament\Backend\Resources\DistributorUserResource\Pages;

use App\Filament\Backend\Resources\DistributorUserResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDistributorUsers extends ListRecords
{
    protected static string $resource = DistributorUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            // Actions\Action::make('test')->color('success')->requiresConfirmation()
        ];
    }
}
