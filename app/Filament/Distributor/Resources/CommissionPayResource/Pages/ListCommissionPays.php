<?php

namespace App\Filament\Distributor\Resources\CommissionPayResource\Pages;

use App\Filament\Distributor\Resources\CommissionPayResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCommissionPays extends ListRecords
{
    protected static string $resource = CommissionPayResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
