<?php

namespace App\Filament\Backend\Resources\SettingResource\Pages;

use App\Filament\Backend\Resources\SettingResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateSetting extends CreateRecord
{
    protected static string $resource = SettingResource::class;
}
