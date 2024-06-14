<?php

namespace App\Filament\Backend\Widgets;

use Filament\Widgets\Widget;

class AccountWidget extends Widget
{
    protected static ?int $sort = 99;
    protected static string $view = 'filament.backend.widgets.account-widget';
}
