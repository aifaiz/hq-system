<?php

namespace App\Filament\Backend\Resources\FeatureResource\Pages;

use App\Filament\Backend\Resources\FeatureResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateFeature extends CreateRecord
{
    protected static string $resource = FeatureResource::class;
}
