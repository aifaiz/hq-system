<?php

namespace App\Filament\Backend\Resources\ProductResource\Pages;

use App\Filament\Backend\Resources\ProductResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;
}
