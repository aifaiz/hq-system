<?php

namespace App\Filament\Distributor\Pages;

use Filament\Pages\Page;

class Setting extends Page
{
    protected static ?int $navigationSort = 6;
    
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string $view = 'filament.distributor.pages.setting';
}
