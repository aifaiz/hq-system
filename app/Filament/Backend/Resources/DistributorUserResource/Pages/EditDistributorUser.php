<?php

namespace App\Filament\Backend\Resources\DistributorUserResource\Pages;

use App\Filament\Backend\Resources\DistributorUserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDistributorUser extends EditRecord
{
    protected static string $resource = DistributorUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
