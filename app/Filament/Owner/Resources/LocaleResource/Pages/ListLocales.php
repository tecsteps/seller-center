<?php

namespace App\Filament\Owner\Resources\LocaleResource\Pages;

use App\Filament\Owner\Resources\LocaleResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLocales extends ListRecords
{
    protected static string $resource = LocaleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
