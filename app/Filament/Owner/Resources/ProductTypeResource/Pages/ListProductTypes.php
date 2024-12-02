<?php

namespace App\Filament\Owner\Resources\ProductTypeResource\Pages;

use App\Filament\Owner\Resources\ProductTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProductTypes extends ListRecords
{
    protected static string $resource = ProductTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
