<?php

namespace App\Filament\Agent\Resources\CommissionPayResource\Pages;

use App\Filament\Agent\Resources\CommissionPayResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCommissionPay extends EditRecord
{
    protected static string $resource = CommissionPayResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
