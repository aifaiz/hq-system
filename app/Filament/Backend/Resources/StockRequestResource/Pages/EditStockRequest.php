<?php

namespace App\Filament\Backend\Resources\StockRequestResource\Pages;

use App\Filament\Backend\Resources\StockRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditStockRequest extends EditRecord
{
    protected static string $resource = StockRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
