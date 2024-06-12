<?php

namespace App\Filament\Backend\Resources\OrderResource\Pages;

use App\Filament\Backend\Resources\OrderResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateOrder extends CreateRecord
{
    protected static string $resource = OrderResource::class;
}
