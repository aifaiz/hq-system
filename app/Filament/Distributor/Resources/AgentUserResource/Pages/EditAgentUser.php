<?php

namespace App\Filament\Distributor\Resources\AgentUserResource\Pages;

use App\Filament\Distributor\Resources\AgentUserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAgentUser extends EditRecord
{
    protected static string $resource = AgentUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
