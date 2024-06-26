<?php

namespace App\Filament\Backend\Resources\StockRequestResource\Pages;

use App\Filament\Backend\Resources\StockRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListStockRequests extends ListRecords
{
    protected static string $resource = StockRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
