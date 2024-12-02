<?php

namespace App\Filament\Owner\Resources\ProductTypeResource\Pages;

use App\Filament\Owner\Resources\ProductTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProductType extends EditRecord
{
    protected static string $resource = ProductTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
