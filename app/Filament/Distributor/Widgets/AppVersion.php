<?php

namespace App\Filament\Distributor\Widgets;

use Filament\Widgets\Widget;

class AppVersion extends Widget
{
    protected static ?int $sort = -2;
    protected static string $view = 'filament.distributor.widgets.app-version';
}
