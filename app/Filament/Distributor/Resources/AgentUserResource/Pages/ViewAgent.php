<?php

namespace App\Filament\Distributor\Resources\AgentUserResource\Pages;

use App\Filament\Distributor\Resources\AgentUserResource;
use App\Models\AgentUser;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewAgent extends ViewRecord
{
    protected static string $resource = AgentUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
            Actions\Action::make('View Shop')
                ->color('info')
                ->icon('heroicon-o-globe-alt')
                ->url(function(AgentUser $record){
                    return route('agent.product', ['refcode'=>$record->refcode]);
                })
                ->openUrlInNewTab()
        ];
    }
}
