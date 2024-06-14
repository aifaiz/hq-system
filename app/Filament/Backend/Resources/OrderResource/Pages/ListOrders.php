<?php

namespace App\Filament\Backend\Resources\OrderResource\Pages;

use App\Filament\Backend\Resources\OrderResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('export')
                ->label('Export All')
                ->requiresConfirmation()
                ->color('info')
        ];
    }
}
