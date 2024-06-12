<?php

namespace App\Filament\Agent\Resources\OrderResource\Pages;

use App\Filament\Agent\Resources\OrderResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateOrder extends CreateRecord
{
    protected static string $resource = OrderResource::class;
}
