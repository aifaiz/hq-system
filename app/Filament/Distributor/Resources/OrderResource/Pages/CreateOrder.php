<?php

namespace App\Filament\Distributor\Resources\OrderResource\Pages;

use App\Filament\Distributor\Resources\OrderResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateOrder extends CreateRecord
{
    protected static string $resource = OrderResource::class;
}
