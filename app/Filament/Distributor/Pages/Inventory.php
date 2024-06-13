<?php

namespace App\Filament\Distributor\Pages;

use Filament\Pages\Page;

class Inventory extends Page
{
    protected static ?int $navigationSort = 1;

    protected static ?string $navigationIcon = 'heroicon-m-gift';

    protected static string $view = 'filament.distributor.pages.inventory';
}
