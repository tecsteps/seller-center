<?php

namespace App\Filament\Owner\Resources\OrderResource\Pages;

use App\Filament\Owner\Resources\OrderResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateOrder extends CreateRecord
{
    protected static string $resource = OrderResource::class;
}
