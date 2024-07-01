<?php

namespace App\Filament\Distributor\Resources\AgentUserResource\Pages;

use App\Filament\Distributor\Resources\AgentUserResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAgentUsers extends ListRecords
{
    protected static string $resource = AgentUserResource::class;

    protected function getHeaderActions(): array
    {
        $refcode = auth()->user()->refcode;
        $url = route('distributor.reg.agent', ['refcode'=>$refcode]);
        return [
            // Actions\CreateAction::make(),
            Actions\Action::make('New Agent')
                ->color('info')
                ->icon('heroicon-c-plus')
                ->url($url)
                ->openUrlInNewTab()
        ];
    }

    // public function getSubheading(): ?string
    // {
    //     $refcode = auth()->user()->refcode;
    //     $url = route('distributor.reg.agent', ['refcode'=>$refcode]);
    //     $registerAgent = 'Register Agent Link: '.$url;
    //     return $registerAgent;
    // }


}
