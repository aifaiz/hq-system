<?php

namespace App\Filament\Distributor\Resources\StockRequestResource\Pages;

use App\Filament\Distributor\Resources\StockRequestResource;
use App\Models\DistributorUser;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications;
use Filament\Notifications\Notification;

class CreateStockRequest extends CreateRecord
{
    protected static string $resource = StockRequestResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $record = new ($this->getModel())($data);

        $record->save();

        $recordID = $record->id;

        $admins = User::all();

        $distributor = DistributorUser::find($record->distributor_id);

        Notification::make()
            ->title('New Stock Request')
            ->body('Stock request from '.$distributor->name)
            ->color('info')
            ->info()
            ->icon('heroicon-c-tag')
            ->actions([
                Notifications\Actions\Action::make('view')
                    ->url('/backend/stock-requests/'.$recordID)
            ])
            ->sendToDatabase($admins);



        return $record;

    }
}
